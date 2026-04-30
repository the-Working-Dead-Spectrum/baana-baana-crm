<?php
// app/Jobs/SyncOrdersJob.php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SyncLog;
use App\Services\WordPressService;
use App\Services\CreatorOrderSyncService; // ✅ Ajout de votre service
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600;
    public int $maxExceptions = 3;

    protected string $syncType;
    protected bool $force;

    public function __construct(string $syncType = 'incremental', bool $force = false)
    {
        $this->syncType = $syncType;
        $this->force    = $force;
        $this->onQueue('sync');
    }

    public function handle(
        WordPressService $wp,
        CreatorOrderSyncService $creatorSyncService // ✅ Injection de votre service
    ): void {
        $start = microtime(true);

        $log = SyncLog::create([
            'sync_type' => 'orders',
            'status'    => 'pending',
            'started_at' => now(),
            'metadata'  => [
                'type'  => $this->syncType,
                'force' => $this->force,
            ],
        ]);

        $stats = [
            'total'   => 0,
            'created' => 0,
            'updated' => 0,
            'failed'  => 0,
            'creators_synced' => 0,
        ];

        try {
            $page         = 1;
            $perPage      = 50;
            $lastSyncDate = $this->getLastSyncDate();

            Log::info('🔄 Starting orders sync', [
                'sync_type' => $this->syncType,
                'force' => $this->force,
                'last_sync_date' => $lastSyncDate,
            ]);

            while (true) {
                $orders = $wp->getOrders(
                    $page,
                    $perPage,
                    $this->syncType === 'incremental' ? $lastSyncDate : null
                );

                if (empty($orders)) {
                    break;
                }

                Log::info("📦 Processing page {$page} with " . count($orders) . " orders");

                foreach ($orders as $orderData) {
                    $stats['total']++;

                    try {
                        DB::transaction(function () use ($orderData, $wp, $creatorSyncService, &$stats) {
                            // 1. Synchroniser la commande et ses items
                            $order = $this->processOrder($orderData, $wp, $stats);
                            
                            if ($order) {
                                // 2. Utiliser VOTRE service pour synchroniser les créateurs
                                $creatorResult = $creatorSyncService->syncCreatorsForOrder($order);
                                
                                if ($creatorResult['success'] && $creatorResult['creators_synced'] > 0) {
                                    $stats['creators_synced'] += $creatorResult['creators_synced'];
                                    Log::info("✅ Synced {$creatorResult['creators_synced']} creators for order #{$order->id}");
                                }
                            }
                        });
                    } catch (\Throwable $e) {
                        $stats['failed']++;
                        Log::error('❌ Order sync failed', [
                            'wp_order_id' => $orderData['id'] ?? null,
                            'error'       => $e->getMessage(),
                            'trace'       => $e->getTraceAsString(),
                        ]);
                    }
                }

                if (count($orders) < $perPage) {
                    break;
                }

                $page++;
                usleep(200000); // 200ms
            }

            $duration = round((microtime(true) - $start) * 1000);

            $log->update([
                'status'           => $stats['failed'] > 0 ? 'partial' : 'success',
                'total_records'    => $stats['total'],
                'created_records'  => $stats['created'],
                'updated_records'  => $stats['updated'],
                'failed_records'   => $stats['failed'],
                'duration_ms'      => $duration,
                'completed_at'     => now(),
                'metadata'         => [
                    'type'  => $this->syncType,
                    'force' => $this->force,
                    'creators_synced' => $stats['creators_synced'],
                ],
            ]);

            $this->updateStatsCache();

            Log::info('✅ Orders sync completed', $stats);
            
        } catch (\Throwable $e) {
            Log::error('💥 Orders sync failed completely', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $log->update([
                'status'       => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    private function processOrder(array $orderData, WordPressService $wp, array &$stats): ?Order
    {
        $wpOrderId = $orderData['id'] ?? null;
        if (!$wpOrderId) return null;

        $wasRecentlyCreated = false;
        
        $order = Order::updateOrCreate(
            ['wp_order_id' => $wpOrderId],
            [
                'order_number'     => $orderData['number'] ?? null,
                'order_date'       => $orderData['date_created'] ?? null,
                'wp_updated_at'    => $orderData['date_modified'] ?? null,
                'status'           => str_replace('wc-', '', $orderData['status'] ?? 'pending'),
                'subtotal'         => $orderData['subtotal'] ?? 0,
                'tax'              => $orderData['total_tax'] ?? 0,
                'shipping'         => $orderData['shipping_total'] ?? 0,
                'total'            => $orderData['total'] ?? 0,
                'customer_name'    => $this->getCustomerName($orderData),
                'customer_email'   => $orderData['billing']['email'] ?? null,
                'customer_phone'   => $orderData['billing']['phone'] ?? null,
                'shipping_address' => $this->formatShippingAddress($orderData),
                'metadata'         => json_encode($orderData),
                'last_synced_at'   => now(),
            ]
        );
        
        if ($order->wasRecentlyCreated) {
            $stats['created']++;
            Log::info("✨ Created new order #{$order->id} (WP: {$wpOrderId})");
        } else {
            $stats['updated']++;
            Log::debug("📝 Updated order #{$order->id} (WP: {$wpOrderId})");
        }

        // Synchroniser les items
        $this->syncOrderItems($order, $orderData['line_items'] ?? [], $wp);

        return $order;
    }

    private function syncOrderItems(Order $order, array $items, WordPressService $wp): void
    {
        // Supprimer les anciens items
        $order->items()->delete();

        if (empty($items)) {
            Log::warning("⚠️ Order #{$order->id} has no items");
            return;
        }

        $productIds = collect($items)->pluck('product_id')->unique()->toArray();
        $brandsMap = $wp->getProductsBrandsBulk($productIds);

        foreach ($items as $item) {
            $brandSlug = $brandsMap[$item['product_id']] ?? null;
            
            OrderItem::create([
                'order_id'      => $order->id,
                'wp_product_id' => $item['product_id'],
                'product_name'  => $item['name'],
                'sku'           => $item['sku'] ?? null,
                'brand_slug'    => $brandSlug,
                'quantity'      => $item['quantity'] ?? 1,
                'unit_price'    => $item['price'] ?? 0,
                'total'         => $item['total'] ?? 0,
                'metadata'      => json_encode($item),
            ]);
        }
        
        Log::debug("✅ Created " . count($items) . " order items for order #{$order->id}");
    }

    private function getLastSyncDate(): ?string
    {
        if ($this->force) {
            return null;
        }

        $last = Order::orderByDesc('last_synced_at')->first();
        return $last ? $last->last_synced_at->subMinutes(5)->toIso8601String() : null;
    }

    private function updateStatsCache(): void
    {
        cache()->put('dashboard_stats_orders', [
            'total_orders' => Order::count(),
            'today_orders' => Order::whereDate('order_date', today())->count(),
            'total_sales'  => Order::sum('total'),
        ], now()->addMinutes(5));
    }

    private function getCustomerName(array $order): string
    {
        return trim(($order['billing']['first_name'] ?? '') . ' ' . ($order['billing']['last_name'] ?? ''));
    }

    private function formatShippingAddress(array $order): string
    {
        return collect($order['shipping'] ?? [])
            ->only(['address_1', 'address_2', 'city', 'state', 'postcode', 'country'])
            ->filter()
            ->implode(', ');
    }
}
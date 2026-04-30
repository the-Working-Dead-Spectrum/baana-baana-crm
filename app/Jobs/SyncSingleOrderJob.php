<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\WordPressService;
use App\Services\CreatorOrderSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncSingleOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120; // 2 minutes max
    public $backoff = [10, 30, 60]; // Retry après 10s, 30s, 60s

    protected int $wpOrderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $wpOrderId)
    {
        $this->wpOrderId = $wpOrderId;
        // $this->onQueue('sync');
    }

    /**
     * Execute the job.
     */
    public function handle(
        WordPressService $wpService,
        CreatorOrderSyncService $creatorSyncService
    ): void {
        Log::info("🚀 SyncSingleOrderJob started", [
            'wp_order_id' => $this->wpOrderId,
        ]);

        try {
            // ========================================
            // ÉTAPE 1: Récupérer la commande depuis WordPress
            // ========================================
            Log::info("🔍 Fetching order #{$this->wpOrderId} from WordPress");

            $orderData = $wpService->getOrderDetails($this->wpOrderId);

            if (!$orderData) {
                Log::error("❌ Failed to fetch order #{$this->wpOrderId} from WordPress");
                throw new \Exception("Order #{$this->wpOrderId} not found in WordPress");
            }

            Log::info("✅ Order #{$this->wpOrderId} fetched", [
                'order_number' => $orderData['number'] ?? 'N/A',
                'total'        => $orderData['total'] ?? 0,
                'items'        => count($orderData['line_items'] ?? [])
            ]);

            // ========================================
            // ÉTAPE 2: Sauvegarder la commande dans Laravel
            // ========================================
            DB::beginTransaction();

            $order = Order::updateOrCreate(
                ['wp_order_id' => $this->wpOrderId],
                [
                    'order_number'     => $orderData['number'] ?? null,
                    'order_date'       => $orderData['date_created'] ?? now(),
                    'wp_updated_at'    => $orderData['date_modified'] ?? now(),
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

            Log::info($order->wasRecentlyCreated ? "✨ New order created" : "📝 Order updated", [
                'order_id'    => $order->id,
                'wp_order_id' => $this->wpOrderId,
                'total'       => $order->total
            ]);

            // ========================================
            // ÉTAPE 3: Synchroniser les items
            // ========================================
            $this->syncOrderItems($order, $orderData['line_items'] ?? [], $wpService);

            DB::commit();

            // ========================================
            // ÉTAPE 4: Synchroniser les créateurs
            // ========================================
            $creatorResult = $creatorSyncService->syncCreatorsForOrder($order);

            Log::info("✅ SyncSingleOrderJob completed", [
                'wp_order_id'     => $this->wpOrderId,
                'order_id'        => $order->id,
                'creators_synced' => $creatorResult['creators_synced'] ?? 0,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("❌ SyncSingleOrderJob failed", [
                'wp_order_id' => $this->wpOrderId,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Synchroniser les items d'une commande
     */
    private function syncOrderItems(Order $order, array $items, WordPressService $wpService): void
    {
        // Supprimer les anciens items
        $order->items()->delete();

        if (empty($items)) {
            Log::warning("⚠️ Order #{$order->id} has no items");
            return;
        }

        // Récupérer les brand_slug en bulk
        $productIds = collect($items)->pluck('product_id')->unique()->toArray();
        $brandsMap = $wpService->getProductsBrandsBulk($productIds);

        // Créer les nouveaux items
        foreach ($items as $item) {
            $brandSlug = $brandsMap[$item['product_id']] ?? null;

            OrderItem::create([
                'order_id' => $order->id,
                'wp_product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'sku' => $item['sku'] ?? null,
                'brand_slug' => $brandSlug,
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['price'] ?? 0,
                'total' => $item['total'] ?? 0,
                'metadata' => json_encode($item),
            ]);
        }

        Log::info("✅ Created {count} items for order #{$order->id}", [
            'count' => count($items)
        ]);
    }

    /**
     * Extraire le nom du client
     */
    private function getCustomerName(array $orderData): string
    {
        return trim(
            ($orderData['billing']['first_name'] ?? '') . ' ' .
                ($orderData['billing']['last_name'] ?? '')
        );
    }

    /**
     * Formater l'adresse de livraison
     */
    private function formatShippingAddress(array $orderData): string
    {
        return collect($orderData['shipping'] ?? [])
            ->only(['address_1', 'address_2', 'city', 'state', 'postcode', 'country'])
            ->filter()
            ->implode(', ');
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("💥 SyncSingleOrderJob failed permanently", [
            'wp_order_id' => $this->wpOrderId,
            'error' => $exception->getMessage()
        ]);

        // Optionnel: Notifier l'admin, enregistrer dans une table d'erreurs, etc.
    }
}

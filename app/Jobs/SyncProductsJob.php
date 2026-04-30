<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Creator;
use App\Models\SyncLog;
use App\Services\WordPressService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    protected $brandSlug = null;
    protected $creatorId = null;
    protected $specificProductId = null;
    protected $productData = null;

    /**
     * Constructor amélioré pour supporter sync directe depuis webhook
     */
    public function __construct($brandSlug = null, $productData = null)
    {
        $this->brandSlug = $brandSlug;
        $this->productData = $productData;

        // Si on reçoit des données produit directes (depuis webhook)
        if ($productData && isset($productData['product_id'])) {
            $this->specificProductId = $productData['product_id'];
        }

        if ($brandSlug) {
            $creator = Creator::where('brand_slug', $brandSlug)->first();
            $this->creatorId = $creator ? $creator->id : null;
        }
    }

    public function handle(WordPressService $wordPressService): void
    {
        $startTime = microtime(true);

        // CAS 1 : Synchronisation d'un produit spécifique depuis webhook
        if ($this->specificProductId && $this->productData) {
            Log::info("🔄 Sync produit spécifique depuis webhook", [
                'product_id' => $this->specificProductId,
                'brand_slug' => $this->productData['brand_slug'] ?? null
            ]);

            $this->syncSingleProductFromWebhook($this->productData);
            return;
        }

        // CAS 2 : Synchronisation complète (existant)
        $logData = [
            'sync_type' => 'products',
            'status' => 'pending',
            'started_at' => now(),
        ];

        if ($this->brandSlug) {
            $logData['metadata'] = ['brand_slug' => $this->brandSlug];
        }

        $log = SyncLog::create($logData);

        try {
            $stats = [
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'failed' => 0,
                'associated_creators' => 0,
            ];

            $page = 1;
            $hasMore = true;

            while ($hasMore) {
                $products = $wordPressService->getProductsWithBrands($page, 100);

                if (empty($products)) {
                    $hasMore = false;
                    continue;
                }

                foreach ($products as $productData) {
                    $stats['total']++;

                    try {
                        $this->processProduct($productData, $stats);
                    } catch (\Exception $e) {
                        $stats['failed']++;
                        Log::error('❌ Failed to sync product', [
                            'product_id' => $productData['id'] ?? 'N/A',
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                if (count($products) < 100) {
                    $hasMore = false;
                } else {
                    $page++;
                }
            }

            $duration = round((microtime(true) - $startTime) * 1000);

            $log->update([
                'status' => $stats['failed'] > 0 ? 'partial' : 'success',
                'total_records' => $stats['total'],
                'created_records' => $stats['created'],
                'updated_records' => $stats['updated'],
                'failed_records' => $stats['failed'],
                'metadata' => [
                    'brand_slug' => $this->brandSlug,
                    'associated_creators' => $stats['associated_creators']
                ],
                'duration_ms' => $duration,
                'completed_at' => now(),
            ]);

            Log::info('✅ Products sync completed', $stats);
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000);

            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'duration_ms' => $duration,
                'completed_at' => now(),
            ]);

            Log::error('❌ Products sync failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * NOUVELLE MÉTHODE : Synchroniser un produit depuis les données webhook
     */
    private function syncSingleProductFromWebhook(array $productData): void
    {
        Log::info("📦 Traitement produit webhook", [
            'product_id' => $productData['product_id'] ?? $productData['id'] ?? null,
            'name' => $productData['name'] ?? 'N/A'
        ]);

        $stats = [
            'created' => 0,
            'updated' => 0,
            'associated_creators' => 0
        ];

        // Normaliser les données en s'assurant que 'id' est bien défini
        $normalizedData = [
            'id'             => $productData['product_id'] ?? $productData['id'],
            'name'           => $productData['name'] ?? null,
            'slug'           => $productData['slug'] ?? null,
            'sku'            => $productData['sku'] ?? null,
            'price'          => isset($productData['price']) ? (float) $productData['price'] : null,
            'regular_price'  => isset($productData['regular_price']) ? (float) $productData['regular_price'] : null,
            'sale_price'     => isset($productData['sale_price']) ? (float) $productData['sale_price'] : null,
            'brand_slug'     => $productData['brand_slug'] ?? null,
            'stock_quantity' => $productData['stock_quantity'] ?? null,
            'stock_status'   => $productData['stock_status'] ?? null,
            'manage_stock'   => $productData['manage_stock'] ?? null,
        ];

        // processProduct() gère sa propre transaction — pas besoin de DB::beginTransaction() ici
        $this->processProduct($normalizedData, $stats);

        Log::info("✅ Produit webhook synchronisé", [
            'product_id' => $normalizedData['id'],
            'created'    => $stats['created'],
            'updated'    => $stats['updated']
        ]);
    }

    private function processProduct(array $productData, array &$stats): void
    {
        DB::transaction(function () use ($productData, &$stats) {
            $productId = $productData['id'] ?? null;

            $existing = Product::where('wp_product_id', $productId)->first();

            $updateData = [
                'wp_product_id' => $productId,
                'last_synced_at' => now(),
                'wp_data' => json_encode($productData),
            ];

            // N'écraser que si la valeur est présente et non-vide
            if (!empty($productData['name'])) {
                $updateData['name'] = $productData['name'];
            }
            if (isset($productData['price']) && $productData['price'] > 0) {
                $updateData['price'] = (float) $productData['price'];
            }
            if (!empty($productData['slug'])) {
                $updateData['slug'] = $productData['slug'];
            }
            if (!empty($productData['sku'])) {
                $updateData['sku'] = $productData['sku'];
            }

            // Brand slug
            $brandSlug = $this->extractBrandSlug($productData);
            if ($brandSlug) {
                $updateData['brand_slug'] = $brandSlug;
                $creator = Creator::where('brand_slug', $brandSlug)->first();
                if ($creator) {
                    $updateData['creator_id'] = $creator->id;
                    $stats['associated_creators']++;
                }
            }

            // Stock — ces champs peuvent légitimement valoir 0
            if (array_key_exists('stock_quantity', $productData)) {
                $updateData['stock_quantity'] = (int) $productData['stock_quantity'];
            }
            if (array_key_exists('stock_status', $productData)) {
                $updateData['stock_status'] = $productData['stock_status'];
            }
            if (array_key_exists('manage_stock', $productData)) {
                $updateData['manage_stock'] = (bool) $productData['manage_stock'];
            }

            $product = Product::updateOrCreate(
                ['wp_product_id' => $productId],
                $updateData
            );

            $product->wasRecentlyCreated ? $stats['created']++ : $stats['updated']++;
        });
    }

    private function extractBrandSlug(array $productData): ?string
    {
        // 1. Champ direct brand_slug
        if (!empty($productData['brand_slug'])) {
            return $productData['brand_slug'];
        }

        // 2. Taxonomie brands
        if (!empty($productData['brands']) && is_array($productData['brands'])) {
            return $productData['brands'][0]['slug'] ?? null;
        }

        // 3. Meta data
        if (!empty($productData['meta_data']) && is_array($productData['meta_data'])) {
            foreach ($productData['meta_data'] as $meta) {
                if (in_array($meta['key'], ['brand_slug', 'brand', 'product_brand'])) {
                    return $meta['value'];
                }
            }
        }

        // 4. Attributs
        if (!empty($productData['attributes']) && is_array($productData['attributes'])) {
            foreach ($productData['attributes'] as $attribute) {
                if (
                    strtolower($attribute['name'] ?? '') === 'brand' ||
                    strtolower($attribute['name'] ?? '') === 'marque'
                ) {
                    $options = $attribute['options'] ?? [];
                    return isset($options[0]) ? \Illuminate\Support\Str::slug($options[0]) : null;
                }
            }
        }

        return null;
    }
}

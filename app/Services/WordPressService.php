<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Creator;

class WordPressService
{
    protected string $baseUrl;
    protected string $apiToken;

    public function __construct()
    {
        $this->baseUrl  = rtrim(config('services.wordpress.url'), '/');
        $this->apiToken = config('services.wordpress.api_token');
    }

    /**
     * Client HTTP centralisé
     */
    private function http()
    {
        $client = Http::withHeaders([
            'X-MP-Token' => $this->apiToken,
            'Accept'     => 'application/json',
        ])
            ->timeout(30)
            ->retry(2, 500);

        // SSL local pour environnement de developpement
        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    /* -----------------------------------------------------------------
     |  CRÉATEURS
     |------------------------------------------------------------------*/

    public function getCreators(int $page = 1, int $perPage = 100): array
    {
        try {
            $response = $this->http()->get(
                "{$this->baseUrl}/wp-json/mp/v2/creators",
                [
                    'page'     => $page,
                    'per_page' => $perPage,
                ]
            );

            return $response->successful()
                ? ($response->json('data') ?? [])
                : [];
        } catch (\Throwable $e) {
            Log::error('WP: getCreators failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    // Identifier les createurs pour une commande donnée
    public function identifyCreatorForOrder(array $order): array
    {
        if (empty($order['line_items'])) {
            return [];
        }

        $productIds = collect($order['line_items'])
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($productIds)) {
            return [];
        }

        //  Essayer de récupérer les marques via l'API
        $brandsByProduct = $this->getProductsBrandsBulk($productIds);

        //  Si pas de marques via API, utiliser une approche alternative
        if (empty($brandsByProduct)) {
            Log::warning('No brands found via API, trying alternative method', [
                'order_id' => $order['id'] ?? 'N/A',
                'product_ids' => $productIds
            ]);

            // Essayer de récupérer les produits un par un
            foreach ($productIds as $productId) {
                $brandSlug = $this->getProductBrandSlug($productId);
                if ($brandSlug) {
                    $brandsByProduct[$productId] = $brandSlug;
                }
            }
        }

        if (empty($brandsByProduct)) {
            Log::warning('No brands found at all for products', [
                'order_id' => $order['id'] ?? 'N/A',
                'product_ids' => $productIds
            ]);
            return [];
        }

        //  Trouver les créateurs
        $brandSlugs = array_unique(array_values($brandsByProduct));
        $creators = Creator::whereIn('brand_slug', $brandSlugs)
            ->get()
            ->keyBy('brand_slug');

        if ($creators->isEmpty()) {
            Log::warning('No creators found for brand slugs', [
                'brand_slugs' => $brandSlugs,
                'available_creators' => Creator::pluck('brand_slug')->toArray()
            ]);

            
            $this->createMissingCreators($brandSlugs);

            
            $creators = Creator::whereIn('brand_slug', $brandSlugs)
                ->get()
                ->keyBy('brand_slug');
        }

        // Regrouper par créateur
        $result = [];
        foreach ($order['line_items'] as $item) {
            $productId = $item['product_id'];

            if (!isset($brandsByProduct[$productId])) {
                continue;
            }

            $brandSlug = $brandsByProduct[$productId];

            if (!$creators->has($brandSlug)) {
                continue;
            }

            $creator = $creators->get($brandSlug);
            $creatorId = $creator->id;

            if (!isset($result[$creatorId])) {
                $result[$creatorId] = [
                    'creator_id'    => $creatorId,
                    'brand_slug'    => $brandSlug,
                    'products'      => [],
                    'creator_total' => 0,
                ];
            }

            $lineTotal = (float) ($item['total'] ?? 0);
            $result[$creatorId]['products'][] = [
                'product_id' => $productId,
                'name'       => $item['name'] ?? '',
                'quantity'   => (int) ($item['quantity'] ?? 1),
                'total'      => $lineTotal,
            ];
            $result[$creatorId]['creator_total'] += $lineTotal;
        }

        return $result;
    }

    private function createMissingCreators(array $brandSlugs): void
    {
        foreach ($brandSlugs as $brandSlug) {
            if (Creator::where('brand_slug', $brandSlug)->exists()) {
                continue;
            }

            Creator::create([
                'wp_creator_id' => 0,
                'name' => 'Créateur ' . $brandSlug,
                'email' => 'creator-' . $brandSlug . '@example.com',
                'brand_slug' => $brandSlug,
                'status' => 'active',
                'commission_rate' => 15.00,
                'last_synced_at' => now(),
            ]);

            Log::info('Auto-created creator for brand', ['brand_slug' => $brandSlug]);
        }
    }

    /* -----------------------------------------------------------------
     |  COMMANDES CRÉATEUR
     |------------------------------------------------------------------*/

    public function getCreatorOrders(int $creatorId, int $page = 1, int $perPage = 50): array
    {
        $cacheKey = "wp_creator_orders_{$creatorId}_{$page}";

        return Cache::remember($cacheKey, 300, function () use ($creatorId, $page, $perPage) {
            try {
                $response = $this->http()->get(
                    "{$this->baseUrl}/wp-json/mp/v2/creators/{$creatorId}/orders",
                    [
                        'page'     => $page,
                        'per_page' => $perPage,
                    ]
                );

                return $response->successful()
                    ? ($response->json('data') ?? [])
                    : [];
            } catch (\Throwable $e) {
                Log::error('WP: getCreatorOrders failed', [
                    'creator_id' => $creatorId,
                    'error'      => $e->getMessage()
                ]);

                return [];
            }
        });
    }

    /* -----------------------------------------------------------------
     |  STATS
     |------------------------------------------------------------------*/

    public function getGlobalStats(string $period = 'month'): ?array
    {
        $cacheKey = "wp_global_stats_{$period}";

        return Cache::remember($cacheKey, 300, function () use ($period) {
            try {
                $response = $this->http()->get(
                    "{$this->baseUrl}/wp-json/mp/v2/stats",
                    ['period' => $period]
                );

                return $response->successful()
                    ? ($response->json('stats') ?? null)
                    : null;
            } catch (\Throwable $e) {
                Log::error('WP: getGlobalStats failed', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }

    /* -----------------------------------------------------------------
     |  COMMANDES WOOCOMMERCE
     |------------------------------------------------------------------*/

    public function getOrders(int $page = 1, int $perPage = 50, ?string $updatedSince = null): array
    {
        try {
            $params = [
                'page'     => $page,
                'per_page' => $perPage,
                'orderby'  => 'date',
                'order'    => 'desc',
            ];

            if ($updatedSince) {
                $params['modified_after'] = $updatedSince;
            }

            $response = $this->woo()->get(
                "{$this->baseUrl}/wp-json/wc/v3/orders",
                $params
            );

            return $response->successful()
                ? $response->json()
                : [];
        } catch (\Throwable $e) {
            Log::error('WP: getOrders failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function getOrderDetails(int $orderId): ?array
    {
        try {
            $response = $this->woo()->get(
                "{$this->baseUrl}/wp-json/wc/v3/orders/{$orderId}"
            );

            return $response->successful()
                ? $response->json()
                : null;
        } catch (\Throwable $e) {
            Log::error('WP: getOrderDetails failed', [
                'order_id' => $orderId,
                'error'    => $e->getMessage()
            ]);

            return null;
        }
    }

    /* -----------------------------------------------------------------
     |  PRODUITS & MARQUES
     |------------------------------------------------------------------*/

   
    /**
     * Récupérer les marques de plusieurs produits en une seule requête
     * 
     * @param array $productIds
     * @return array [product_id => brand_slug]
     */
    public function getProductsBrandsBulk(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        try {
            $response = $this->http()->post(
                "{$this->baseUrl}/wp-json/mp/v2/products/brands-bulk",
                ['product_ids' => $productIds]
            );

            if ($response->successful()) {
                $brands = $response->json('data') ?? [];

                if (!empty($brands)) {
                    Log::info('Brands retrieved via custom endpoint', [
                        'count' => count($brands)
                    ]);
                    return $brands;
                }
            }

            Log::warning('Custom endpoint failed or empty, trying WooCommerce API', [
                'status' => $response->status(),
                'product_ids' => $productIds
            ]);

            $brands = $this->getProductsBrandsFromWooCommerce($productIds);

            if (!empty($brands)) {
                return $brands;
            }

            Log::warning('Bulk methods failed, fetching one by one');
            return $this->getProductsBrandsOneByOne($productIds);
        } catch (\Throwable $e) {
            Log::error('WP: getProductsBrandsBulk failed', [
                'error' => $e->getMessage(),
                'product_ids' => $productIds
            ]);

            return [];
        }
    }

    /**
     * Récupérer via l'API WooCommerce standard
     */
    private function getProductsBrandsFromWooCommerce(array $productIds): array
    {
        try {
            $response = $this->woo()->get(
                "{$this->baseUrl}/wp-json/wc/v3/products",
                [
                    'include'  => implode(',', $productIds),
                    'per_page' => 100,
                ]
            );

            if (!$response->successful()) {
                Log::warning('WooCommerce API failed', [
                    'status' => $response->status()
                ]);
                return [];
            }

            $brands = [];

            foreach ($response->json() as $product) {
                $productId = $product['id'];
                $brandSlug = null;

                if (!empty($product['brands'])) {
                    $brandSlug = $product['brands'][0]['slug'] ?? null;
                }

                if (!$brandSlug && !empty($product['meta_data'])) {
                    foreach ($product['meta_data'] as $meta) {
                        if (in_array($meta['key'], ['brand_slug', '_brand', 'brand'])) {
                            $brandSlug = $meta['value'];
                            break;
                        }
                    }
                }

                if (!$brandSlug && !empty($product['attributes'])) {
                    foreach ($product['attributes'] as $attribute) {
                        if (in_array(strtolower($attribute['name']), ['brand', 'marque'])) {
                            $brandSlug = sanitize_title($attribute['options'][0] ?? '');
                            break;
                        }
                    }
                }

                if ($brandSlug) {
                    $brands[$productId] = $brandSlug;
                }
            }

            Log::info('Brands retrieved via WooCommerce API', [
                'count' => count($brands)
            ]);

            return $brands;
        } catch (\Throwable $e) {
            Log::error('getProductsBrandsFromWooCommerce failed', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Fallback ultime : récupérer un par un
     */
    private function getProductsBrandsOneByOne(array $productIds): array
    {
        $brands = [];

        foreach ($productIds as $productId) {
            try {
                $response = $this->woo()->get(
                    "{$this->baseUrl}/wp-json/wc/v3/products/{$productId}"
                );

                if (!$response->successful()) {
                    continue;
                }

                $product = $response->json();
                $brandSlug = null;

                // Mméthodes de détection
                if (!empty($product['brands'])) {
                    $brandSlug = $product['brands'][0]['slug'] ?? null;
                }

                if (!$brandSlug && !empty($product['meta_data'])) {
                    foreach ($product['meta_data'] as $meta) {
                        if (in_array($meta['key'], ['brand_slug', '_brand', 'brand'])) {
                            $brandSlug = $meta['value'];
                            break;
                        }
                    }
                }

                if ($brandSlug) {
                    $brands[$productId] = $brandSlug;
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to get brand for product', [
                    'product_id' => $productId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Brands retrieved one by one', ['count' => count($brands)]);

        return $brands;
    }

        public function getProductsByIds(array $productIds, bool $withBrands = true): array
    {
        if (empty($productIds)) {
            return [];
        }

        try {
            // Utiliser l'API WooCommerce
            $response = $this->woo()->get(
                "{$this->baseUrl}/wp-json/wc/v3/products",
                [
                    'include'  => implode(',', $productIds),
                    'per_page' => 100, 
                ]
            );

            if (!$response->successful()) {
                Log::warning('WP: getProductsByIds failed', [
                    'status' => $response->status(),
                    'error'  => $response->body()
                ]);
                return [];
            }

            $products = $response->json();

            if (!$withBrands) {
                return $products;
            }

            // Formater avec les marques
            $formattedProducts = [];

            foreach ($products as $product) {
                $brandSlug = null;

                // Plugin WooCommerce Brands
                if (!empty($product['brands'])) {
                    $brandSlug = $product['brands'][0]['slug'] ?? null;
                }

                // Meta data
                if (!$brandSlug && !empty($product['meta_data'])) {
                    foreach ($product['meta_data'] as $meta) {
                        if (in_array($meta['key'], ['brand_slug', '_brand', 'brand'])) {
                            $brandSlug = $meta['value'];
                            break;
                        }
                    }
                }

                // Attributs
                if (!$brandSlug && !empty($product['attributes'])) {
                    foreach ($product['attributes'] as $attribute) {
                        if (in_array(strtolower($attribute['name']), ['brand', 'marque'])) {
                            $brandSlug = sanitize_title($attribute['options'][0] ?? '');
                            break;
                        }
                    }
                }

                $formattedProducts[] = [
                    'id'          => $product['id'],
                    'name'        => $product['name'],
                    'slug'        => $product['slug'],
                    'sku'         => $product['sku'] ?? '',
                    'price'       => (float) ($product['price'] ?? 0),
                    'regular_price' => (float) ($product['regular_price'] ?? 0),
                    'sale_price'  => (float) ($product['sale_price'] ?? 0),
                    'stock_status' => $product['stock_status'] ?? 'outofstock',
                    'stock_quantity' => (int) ($product['stock_quantity'] ?? 0),
                    'brand_slug'  => $brandSlug,
                    'wp_data'     => $product,
                ];
            }

            Log::info('WP: Retrieved products by IDs', [
                'count' => count($formattedProducts)
            ]);

            return $formattedProducts;
        } catch (\Throwable $e) {
            Log::error('WP: getProductsByIds failed', [
                'error' => $e->getMessage(),
                'product_ids' => $productIds
            ]);
            return [];
        }
    }

    /**
     * Version alternative avec cache
     */
    public function getProductsByIdsCached(array $productIds, int $cacheMinutes = 60): array
    {
        $cacheKey = 'wp_products_' . md5(implode(',', $productIds));

        return Cache::remember($cacheKey, $cacheMinutes, function () use ($productIds) {
            return $this->getProductsByIds($productIds);
        });
    }

    /**
     * Récupérer un produit spécifique par ID
     */
    public function getProductById(int $productId): ?array
    {
        try {
            $response = $this->woo()->get(
                "{$this->baseUrl}/wp-json/wc/v3/products/{$productId}"
            );

            if (!$response->successful()) {
                return null;
            }

            $product = $response->json();

            return [
                'id'          => $product['id'],
                'name'        => $product['name'],
                'sku'         => $product['sku'] ?? '',
                'price'       => (float) ($product['price'] ?? 0),
                'stock_status' => $product['stock_status'] ?? 'outofstock',
                'stock_quantity' => (int) ($product['stock_quantity'] ?? 0),
                'brand_slug'  => $this->extractBrandSlugFromProduct($product),
                'wp_data'     => $product,
            ];
        } catch (\Throwable $e) {
            Log::error('WP: getProductById failed', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Extraire le brand_slug d'un produit
     */
    private function extractBrandSlugFromProduct(array $product): ?string
    {
        // plugin WooCommerce Brands
        if (!empty($product['brands'])) {
            return $product['brands'][0]['slug'] ?? null;
        }

        //  Meta data
        if (!empty($product['meta_data'])) {
            foreach ($product['meta_data'] as $meta) {
                if (in_array($meta['key'], ['brand_slug', '_brand', 'brand'])) {
                    return $meta['value'];
                }
            }
        }

        // Attributs
        if (!empty($product['attributes'])) {
            foreach ($product['attributes'] as $attribute) {
                if (in_array(strtolower($attribute['name']), ['brand', 'marque'])) {
                    return sanitize_title($attribute['options'][0] ?? '');
                }
            }
        }

        return null;
    }

    /**
     * Récupérer les produits avec leurs marques
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array
     */

    public function getProductsWithBrands(int $page = 1, int $perPage = 100): array
    {
        try {
            // Utiliser uniquement l'API WooCommerce standard
            $response = $this->woo()->get(
                "{$this->baseUrl}/wp-json/wc/v3/products",
                [
                    'page'     => $page,
                    'per_page' => $perPage,
                ]
            );

            if (!$response->successful()) {
                Log::warning('WP: getProducts failed', [
                    'status' => $response->status(),
                    'error'  => $response->body()
                ]);
                return [];
            }

            $products = $response->json();
            $formattedProducts = [];

            foreach ($products as $product) {
                $brandSlug = null;

                // Via le plugin WooCommerce Brands
                if (!empty($product['brands'])) {
                    $brandSlug = $product['brands'][0]['slug'] ?? null;
                }

                // Via meta_data (champ personnalisé)
                if (!$brandSlug && !empty($product['meta_data'])) {
                    foreach ($product['meta_data'] as $meta) {
                        if (in_array($meta['key'], ['brand_slug', 'brand', 'product_brand', '_brand'])) {
                            $brandSlug = $meta['value'];
                            break;
                        }
                    }
                }

                // Via les attributs
                if (!$brandSlug && !empty($product['attributes'])) {
                    foreach ($product['attributes'] as $attribute) {
                        if (
                            strtolower($attribute['name']) === 'brand' ||
                            strtolower($attribute['name']) === 'marque'
                        ) {
                            $brandSlug = sanitize_title($attribute['options'][0] ?? '');
                            break;
                        }
                    }
                }

                $formattedProducts[] = [
                    'id'         => $product['id'],
                    'name'       => $product['name'],
                    'slug'       => $product['slug'],
                    'sku'        => $product['sku'] ?? '',
                    'price'      => $product['price'] ?? 0,
                    'regular_price' => $product['regular_price'] ?? 0,
                    'sale_price' => $product['sale_price'] ?? 0,
                    'brand_slug' => $brandSlug,
                    'wp_data'    => $product,
                ];
            }

            Log::info('WP: Retrieved products', [
                'count' => count($formattedProducts),
                'page' => $page
            ]);

            return $formattedProducts;
        } catch (\Throwable $e) {
            Log::error('WP: getProductsWithBrands failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function identifyCreatorsForOrder(array $orderData): array
    {
        if (empty($orderData['line_items'])) {
            return [];
        }

        // Récupérer tous les product_ids de la commande
        $productIds = collect($orderData['line_items'])
            ->pluck('product_id')
            ->unique()
            ->values()
            ->toArray();

        //  Mapping BULK product_id => creator_id
        
        $productCreators = $this->getProductsCreatorsBulk($productIds);

        if (empty($productCreators)) {
            return [];
        }

        //  Extraire les creator_ids uniques
        return collect($productCreators)
            ->values()
            ->unique()
            ->values()
            ->toArray();
    }

    public function calculateCreatorTotal(array $orderData, int $creatorId): float
    {
        if (empty($orderData['line_items'])) {
            return 0;
        }

        // Préparer les product_ids
        $productIds = collect($orderData['line_items'])
            ->pluck('product_id')
            ->unique()
            ->values()
            ->toArray();

        // Mapping BULK product_id => creator_id
        $productCreators = $this->getProductsCreatorsBulk($productIds);

        if (empty($productCreators)) {
            return 0;
        }

        // Calcul du total créateur
        $total = 0;

        foreach ($orderData['line_items'] as $item) {
            $productId = $item['product_id'];

            if (
                isset($productCreators[$productId]) &&
                (int) $productCreators[$productId] === $creatorId
            ) {
                $total += (float) ($item['total'] ?? 0);
            }
        }

        return round($total, 2);
    }


    private function getProductsCreatorsBulk(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        try {
            $response = $this->http()->post(
                "{$this->baseUrl}/wp-json/mp/v2/products/creators",
                [
                    'product_ids' => $productIds,
                ]
            );

            if (! $response->successful()) {
                return [];
            }

            return $response->json('data') ?? [];
        } catch (\Throwable $e) {
            Log::error('WP: getProductsCreatorsBulk failed', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }


    /**
     *  Fallback (éviter en masse)
     */
    public function getProductBrandSlug(int $productId): ?string
    {
        try {
            $response = $this->http()->get(
                "{$this->baseUrl}/wp-json/mp/v2/products/{$productId}/brand"
            );

            return $response->successful()
                ? ($response->json('brand_slug') ?? null)
                : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /* -----------------------------------------------------------------
     |  OUTILS
     |------------------------------------------------------------------*/

    public function testConnection(): array
    {
        try {
            $response = $this->http()->get(
                "{$this->baseUrl}/wp-json/mp/v2/system/test"
            );

            return [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'data'    => $response->successful() ? $response->json() : null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    private function woo()
    {
        $client = Http::withBasicAuth(
            config('services.wordpress.wc_key'),
            config('services.wordpress.wc_secret')
        )
            ->timeout(60)
            ->retry(2, 500);

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }
}

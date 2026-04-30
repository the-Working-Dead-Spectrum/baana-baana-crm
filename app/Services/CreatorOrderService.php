<?php

namespace App\Services;

use App\Models\Creator;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatorOrderService
{
    /**
     * Synchroniser les créateurs pour une commande
     * 
     * @param Order $order
     * @return array Tableau des créateurs synchronisés avec leurs totaux
     */
    public function syncCreatorsForOrder(Order $order): array
    {
        try {
            DB::beginTransaction();

            // Récupérer tous les brand_slug présents dans la commande
            $brandSlugs = $order->items()
                ->select('brand_slug')
                ->distinct()
                ->whereNotNull('brand_slug')
                ->pluck('brand_slug');

            if ($brandSlugs->isEmpty()) {
                Log::warning("Order #{$order->id} has no items with brand_slug");
                DB::commit();
                return [];
            }

            // Récupérer les créateurs actifs correspondants
            $creators = Creator::whereIn('brand_slug', $brandSlugs)
                ->where('status', 'active')
                ->get();

            $syncData = [];
            
            foreach ($creators as $creator) {
                // Récupérer les items du créateur dans cette commande
                $creatorItems = $order->items()
                    ->where('brand_slug', $creator->brand_slug)
                    ->get();

                if ($creatorItems->isEmpty()) {
                    continue;
                }

                // Calculer les totaux
                $creatorTotal = $creatorItems->sum('total');
                $productCount = $creatorItems->count();
                $totalQuantity = $creatorItems->sum('quantity');

                // Préparer les données pour la table pivot
                $syncData[$creator->id] = [
                    'creator_total' => $creatorTotal,
                    'product_count' => $productCount,
                    'total_quantity' => $totalQuantity,
                    'metadata' => json_encode([
                        'items' => $creatorItems->map(function ($item) {
                            return [
                                'product_id' => $item->wp_product_id,
                                'product_name' => $item->product_name,
                                'sku' => $item->sku,
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'total' => $item->total,
                            ];
                        })->toArray(),
                        'synced_at' => now()->toIso8601String(),
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                Log::info("Creator #{$creator->id} ({$creator->brand_slug}) in Order #{$order->id}: {$creatorTotal} CFA ({$productCount} products, {$totalQuantity} items)");
            }

            // Synchroniser avec la table pivot
            $order->creators()->sync($syncData);

            DB::commit();

            return $syncData;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to sync creators for Order #{$order->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mettre à jour le total d'un créateur pour une commande
     * 
     * @param Order $order
     * @param Creator $creator
     * @return bool
     */
    public function updateCreatorTotal(Order $order, Creator $creator): bool
    {
        try {
            $creatorItems = $order->items()
                ->where('brand_slug', $creator->brand_slug)
                ->get();

            if ($creatorItems->isEmpty()) {
                // Détacher le créateur si plus aucun produit
                $order->creators()->detach($creator->id);
                return true;
            }

            $creatorTotal = $creatorItems->sum('total');
            $productCount = $creatorItems->count();
            $totalQuantity = $creatorItems->sum('quantity');

            // Mettre à jour la table pivot
            $order->creators()->updateExistingPivot($creator->id, [
                'creator_total' => $creatorTotal,
                'product_count' => $productCount,
                'total_quantity' => $totalQuantity,
                'updated_at' => now(),
            ]);

            Log::info("Updated Creator #{$creator->id} total for Order #{$order->id}: {$creatorTotal} CFA");

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to update creator total: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les statistiques d'un créateur pour une période
     * 
     * @param Creator $creator
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getCreatorStats(Creator $creator, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = $creator->orders()->where('status', 'completed');

        if ($startDate) {
            $query->where('order_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('order_date', '<=', $endDate);
        }

        $totalOrders = $query->count();
        $totalSales = $query->sum('creator_order.creator_total');
        $totalProductsSold = $query->sum('creator_order.total_quantity');

        return [
            'total_orders' => $totalOrders,
            'total_sales' => $totalSales,
            'total_products_sold' => $totalProductsSold,
            'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
        ];
    }

    /**
     * Récupérer les commandes d'un créateur avec pagination
     * 
     * @param Creator $creator
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCreatorOrders(Creator $creator, int $perPage = 20, array $filters = [])
    {
        $query = $creator->orders()
            ->with(['items' => function ($query) use ($creator) {
                $query->where('brand_slug', $creator->brand_slug);
            }])
            ->orderBy('order_date', 'desc');

        // Appliquer les filtres
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from'])) {
            $query->where('order_date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->where('order_date', '<=', $filters['to']);
        }

        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_number', 'like', "%{$filters['q']}%")
                  ->orWhere('customer_name', 'like', "%{$filters['q']}%")
                  ->orWhere('customer_email', 'like', "%{$filters['q']}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Récupérer les produits les plus vendus d'un créateur
     * 
     * @param Creator $creator
     * @param int $limit
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getTopProducts(Creator $creator, int $limit = 10, ?string $startDate = null, ?string $endDate = null)
    {
        $query = OrderItem::select([
                'wp_product_id',
                'product_name',
                'sku',
                'brand_slug',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(DISTINCT order_id) as order_count'),
                DB::raw('AVG(unit_price) as avg_price'),
            ])
            ->where('brand_slug', $creator->brand_slug)
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed');
                
                if ($startDate) {
                    $q->where('order_date', '>=', $startDate);
                }
                
                if ($endDate) {
                    $q->where('order_date', '<=', $endDate);
                }
            })
            ->groupBy('wp_product_id', 'product_name', 'sku', 'brand_slug')
            ->orderByDesc('total_sales')
            ->limit($limit);

        return $query->get();
    }

    /**
     * Récupérer les statistiques par jour pour un créateur
     * 
     * @param Creator $creator
     * @param int $days
     * @return \Illuminate\Support\Collection
     */
    public function getDailyStats(Creator $creator, int $days = 7)
    {
        $startDate = now()->subDays($days - 1)->startOfDay();
        
        return DB::table('creator_order')
            ->join('orders', 'creator_order.order_id', '=', 'orders.id')
            ->where('creator_order.creator_id', $creator->id)
            ->where('orders.status', 'completed')
            ->where('orders.order_date', '>=', $startDate)
            ->select(
                DB::raw('DATE(orders.order_date) as date'),
                DB::raw('COUNT(DISTINCT creator_order.order_id) as order_count'),
                DB::raw('SUM(creator_order.creator_total) as total_sales'),
                DB::raw('SUM(creator_order.total_quantity) as products_sold')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Vérifier si une commande concerne un créateur
     * 
     * @param Order $order
     * @param Creator $creator
     * @return bool
     */
    public function orderInvolvesCreator(Order $order, Creator $creator): bool
    {
        return $order->items()
            ->where('brand_slug', $creator->brand_slug)
            ->exists();
    }

    /**
     * Récupérer le détail de la participation d'un créateur à une commande
     * 
     * @param Order $order
     * @param Creator $creator
     * @return array|null
     */
    public function getCreatorOrderDetails(Order $order, Creator $creator): ?array
    {
        $pivot = $order->creators()->where('creator_id', $creator->id)->first()?->pivot;
        
        if (!$pivot) {
            return null;
        }

        $items = $order->items()->where('brand_slug', $creator->brand_slug)->get();

        return [
            'creator_total' => $pivot->creator_total,
            'product_count' => $pivot->product_count,
            'total_quantity' => $pivot->total_quantity,
            'items' => $items->map(function ($item) {
                return [
                    'product_id' => $item->wp_product_id,
                    'product_name' => $item->product_name,
                    'sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ];
            })->toArray(),
            'synced_at' => $pivot->updated_at,
        ];
    }

    /**
     * Récupérer les clients uniques d'un créateur
     * 
     * @param Creator $creator
     * @return int
     */
    public function getUniqueCustomersCount(Creator $creator): int
    {
        return $creator->orders()
            ->where('status', 'completed')
            ->distinct('customer_email')
            ->count('customer_email');
    }

    /**
     * Récupérer le panier moyen d'un créateur
     * 
     * @param Creator $creator
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    public function getAverageBasket(Creator $creator, ?string $startDate = null, ?string $endDate = null): float
    {
        $stats = $this->getCreatorStats($creator, $startDate, $endDate);
        return $stats['average_order_value'];
    }
}
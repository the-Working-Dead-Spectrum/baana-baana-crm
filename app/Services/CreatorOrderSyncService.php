<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Creator;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatorOrderSyncService
{
    /**
     * Synchronise les créateurs associés à une commande
     * 
     * @param Order $order
     * @return array
     */
    public function syncCreatorsForOrder(Order $order): array
    {
        Log::info("🔄 Syncing creators for order #{$order->id}");
        
        // 1. Récupérer tous les items de la commande
        $orderItems = $order->items;
        
        if ($orderItems->isEmpty()) {
            Log::warning("⚠️ Order #{$order->id} has no items");
            return [
                'success' => false,
                'message' => 'Order has no items',
                'creators_synced' => 0,
            ];
        }
        
        // 2. Grouper les items par brand_slug
        $itemsByBrand = $orderItems->groupBy('brand_slug')->filter(function ($items, $brandSlug) {
            return !empty($brandSlug);
        });
        
        if ($itemsByBrand->isEmpty()) {
            Log::warning("⚠️ Order #{$order->id} has no items with brand_slug");
            return [
                'success' => false,
                'message' => 'No items with brand_slug found',
                'creators_synced' => 0,
            ];
        }
        
        Log::info("📦 Found {$itemsByBrand->count()} brands in order #{$order->id}");
        
        // 3. Pour chaque marque, trouver le créateur et calculer son total
        $syncedCreators = [];
        $totalCreatorsSynced = 0;
        
        foreach ($itemsByBrand as $brandSlug => $items) {
            // Trouver le créateur pour cette marque
            $creator = Creator::where('brand_slug', $brandSlug)
                ->where('status', 'active')
                ->first();
            
            if (!$creator) {
                Log::warning("⚠️ No active creator found for brand: {$brandSlug}");
                continue;
            }
            
            // Calculer le total pour ce créateur
            $creatorTotal = $items->sum('total');
            $productCount = $items->count();
            $totalQuantity = $items->sum('quantity');
            
            Log::info("💰 Creator #{$creator->id} ({$creator->name}) - Total: {$creatorTotal} ({$productCount} products)");
            
            // 4. Attacher le créateur à la commande via la table pivot
            try {
                // Détacher d'abord pour éviter les doublons
                $order->creators()->detach($creator->id);
                
                // Attacher avec les données du pivot
                $order->creators()->attach($creator->id, [
                    'creator_total' => $creatorTotal,
                    'product_count' => $productCount,
                    'total_quantity' => $totalQuantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $syncedCreators[] = [
                    'creator_id' => $creator->id,
                    'creator_name' => $creator->name,
                    'brand_slug' => $brandSlug,
                    'creator_total' => $creatorTotal,
                    'product_count' => $productCount,
                ];
                
                $totalCreatorsSynced++;
                
                Log::info("✅ Successfully synced creator #{$creator->id} with order #{$order->id}");
                
            } catch (\Exception $e) {
                Log::error("❌ Failed to sync creator #{$creator->id} with order #{$order->id}: {$e->getMessage()}");
            }
        }
        
        Log::info("✅ Synced {$totalCreatorsSynced} creators for order #{$order->id}");
        
        return [
            'success' => true,
            'message' => "Synced {$totalCreatorsSynced} creators",
            'creators_synced' => $totalCreatorsSynced,
            'creators' => $syncedCreators,
        ];
    }
    
    /**
     * Synchronise toutes les commandes existantes avec leurs créateurs
     * À utiliser pour la migration initiale ou la resynchronisation
     * 
     * @param int|null $limit
     * @return array
     */
    public function syncAllOrders(?int $limit = null): array
    {
        Log::info("🔄 Starting full orders sync" . ($limit ? " (limit: {$limit})" : ""));
        
        $query = Order::with('items');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        $orders = $query->get();
        
        $totalOrders = $orders->count();
        $successCount = 0;
        $failedCount = 0;
        $skippedCount = 0;
        
        foreach ($orders as $order) {
            $result = $this->syncCreatorsForOrder($order);
            
            if ($result['success']) {
                if ($result['creators_synced'] > 0) {
                    $successCount++;
                } else {
                    $skippedCount++;
                }
            } else {
                $failedCount++;
            }
        }
        
        Log::info("✅ Full sync completed: {$successCount} success, {$failedCount} failed, {$skippedCount} skipped");
        
        return [
            'total_orders' => $totalOrders,
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'skipped_count' => $skippedCount,
        ];
    }
    
    /**
     * Synchronise les créateurs pour les commandes d'une période donnée
     * 
     * @param string $startDate
     * @param string|null $endDate
     * @return array
     */
    public function syncOrdersByDateRange(string $startDate, ?string $endDate = null): array
    {
        $endDate = $endDate ?? now()->toDateString();
        
        Log::info("🔄 Syncing orders from {$startDate} to {$endDate}");
        
        $orders = Order::with('items')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->get();
        
        $totalOrders = $orders->count();
        $successCount = 0;
        $failedCount = 0;
        
        foreach ($orders as $order) {
            $result = $this->syncCreatorsForOrder($order);
            
            if ($result['success'] && $result['creators_synced'] > 0) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }
        
        return [
            'total_orders' => $totalOrders,
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
    
    /**
     * Vérifie la cohérence des données de synchronisation
     * 
     * @return array
     */
    public function validateSync(): array
    {
        Log::info("🔍 Validating sync data");
        
        // 1. Compter les commandes sans créateurs
        $ordersWithoutCreators = Order::doesntHave('creators')
            ->whereHas('items', function ($query) {
                $query->whereNotNull('brand_slug');
            })
            ->count();
        
        // 2. Compter les créateurs actifs
        $activeCreators = Creator::where('status', 'active')->count();
        
        // 3. Compter les commandes par créateur
        $creatorsWithOrders = Creator::withCount('orders')->get();
        
        // 4. Vérifier les incohérences dans la table pivot
        $pivotIssues = DB::table('creator_order')
            ->whereNull('creator_total')
            ->orWhere('creator_total', '<=', 0)
            ->count();
        
        return [
            'orders_without_creators' => $ordersWithoutCreators,
            'active_creators' => $activeCreators,
            'creators_stats' => $creatorsWithOrders->map(function ($creator) {
                return [
                    'id' => $creator->id,
                    'name' => $creator->name,
                    'brand_slug' => $creator->brand_slug,
                    'orders_count' => $creator->orders_count,
                ];
            }),
            'pivot_issues_count' => $pivotIssues,
            'needs_resync' => $ordersWithoutCreators > 0 || $pivotIssues > 0,
        ];
    }
    
    /**
     * Recalcule le creator_total pour une commande spécifique
     * 
     * @param Order $order
     * @param Creator $creator
     * @return float
     */
    public function recalculateCreatorTotal(Order $order, Creator $creator): float
    {
        $total = $order->items()
            ->where('brand_slug', $creator->brand_slug)
            ->sum('total');
        
        // Mettre à jour la table pivot
        $order->creators()->updateExistingPivot($creator->id, [
            'creator_total' => $total,
            'updated_at' => now(),
        ]);
        
        Log::info("💰 Recalculated total for creator #{$creator->id} on order #{$order->id}: {$total}");
        
        return $total;
    }
}
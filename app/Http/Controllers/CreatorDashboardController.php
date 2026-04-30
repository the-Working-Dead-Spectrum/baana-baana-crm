<?php

namespace App\Http\Controllers;

use App\Models\Creator;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Services\CreatorOrderService;
use App\Services\WordPressService;
use App\Services\OrderSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatorDashboardController extends Controller
{
    protected WordPressService $wordPressService;
    protected CreatorOrderService $creatorOrderService;
    protected OrderSyncService $orderSyncService;

    public function __construct(
        WordPressService $wordPressService,
        CreatorOrderService $creatorOrderService,
        OrderSyncService $orderSyncService
    ) {
        $this->wordPressService = $wordPressService;
        $this->creatorOrderService = $creatorOrderService;
        $this->orderSyncService = $orderSyncService;
    }

    /**
     * Dashboard principal du créateur
     */
    public function dashboard()
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('creator.setup')
                ->with('error', 'Vous devez compléter votre profil créateur avant d\'accéder au dashboard.');
        }

        if (empty($user->wp_creator_id)) {
            return redirect()->route('creator.setup')
                ->with('warning', 'Votre profil créateur nécessite une configuration supplémentaire.');
        }

        // Calculer les stats personnelles
        $personalStats = $this->calculatePersonalStats($creator);

        // Récupérer les produits du créateur
        $creatorProducts = Cache::remember(
            'creator_products_' . $creator->id,
            now()->addMinutes(15),
            fn() => $this->getCreatorProducts($creator)
        );

        // Statistiques des 7 derniers jours
        $last7DaysStats = $this->getCreatorLast7DaysStats($creator);

        // Alertes
        $alerts = $this->getCreatorAlerts($creator);

        // Commandes récentes
        $recentOrders = $this->getRecentLocalOrders($creator);

        return view('creator.dashboard', [
            'creator' => $creator,
            'personalStats' => $personalStats,
            'creatorProducts' => $creatorProducts,
            'last7DaysStats' => $last7DaysStats,
            'alerts' => $alerts,
            'recentLocalOrders' => $recentOrders,
            'apiConnected' => $this->wordPressService->testConnection(),
        ]);
    }

    /**
     * Calculer les stats personnelles du créateur
     */
    private function calculatePersonalStats(Creator $creator): array
    {
        return Cache::remember(
            'creator_personal_stats_' . $creator->id,
            now()->addMinutes(5),
            function () use ($creator) {
                // Requête de base pour les commandes complétées
                $completedOrders = $creator->orders()->where('status', 'completed');

                // Mois en cours
                $currentMonthOrders = $creator->orders()
                    ->where('status', 'completed')
                    ->whereMonth('order_date', now()->month)
                    ->whereYear('order_date', now()->year);

                // Mois précédent
                $lastMonthOrders = $creator->orders()
                    ->where('status', 'completed')
                    ->whereMonth('order_date', now()->subMonth()->month)
                    ->whereYear('order_date', now()->subMonth()->year);

                // Calculs des totaux via la table pivot
                $totalSales = $completedOrders->sum('creator_order.creator_total');
                $totalOrders = $completedOrders->count();

                $currentMonthSales = $currentMonthOrders->sum('creator_order.creator_total');
                $currentMonthOrdersCount = $currentMonthOrders->count();

                $lastMonthSales = $lastMonthOrders->sum('creator_order.creator_total');
                $lastMonthOrdersCount = $lastMonthOrders->count();

                // Croissance
                $salesGrowth = $this->calculateGrowthRate($lastMonthSales, $currentMonthSales);
                $ordersGrowth = $this->calculateGrowthRate($lastMonthOrdersCount, $currentMonthOrdersCount);

                // Panier moyen
                $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
                $currentMonthAvg = $currentMonthOrdersCount > 0 ? $currentMonthSales / $currentMonthOrdersCount : 0;
                $lastMonthAvg = $lastMonthOrdersCount > 0 ? $lastMonthSales / $lastMonthOrdersCount : 0;
                $avgOrderGrowth = $this->calculateGrowthRate($lastMonthAvg, $currentMonthAvg);

                // Aujourd'hui
                $todayOrders = $creator->orders()
                    ->where('status', 'completed')
                    ->whereDate('order_date', today());

                // Produits vendus
                $productsSold = $creator->orders()
                    ->where('status', 'completed')
                    ->sum('creator_order.total_quantity');

                // Clients uniques
                $uniqueCustomers = $creator->orders()
                    ->where('status', 'completed')
                    ->distinct('customer_email')
                    ->count('customer_email');

                return [
                    'total_sales' => [
                        'value' => $totalSales,
                        'label' => 'Chiffre d\'affaires total',
                        'icon' => 'currency',
                        'color' => 'blue',
                    ],
                    'month_sales' => [
                        'value' => $currentMonthSales,
                        'label' => 'CA du mois',
                        'growth' => $salesGrowth,
                        'icon' => 'trending',
                        'color' => 'green',
                    ],
                    'total_orders' => [
                        'value' => $totalOrders,
                        'month_value' => $currentMonthOrdersCount,
                        'label' => 'Commandes totales',
                        'growth' => $ordersGrowth,
                        'icon' => 'check',
                        'color' => 'purple',
                    ],
                    'average_order_value' => [
                        'value' => $averageOrderValue,
                        'month_value' => $currentMonthAvg,
                        'label' => 'Panier moyen',
                        'growth' => $avgOrderGrowth,
                        'icon' => 'cart',
                        'color' => 'yellow',
                    ],
                    'products_sold' => [
                        'value' => $productsSold,
                        'label' => 'Produits vendus',
                        'icon' => 'package',
                        'color' => 'indigo',
                    ],
                    'unique_customers' => [
                        'value' => $uniqueCustomers,
                        'label' => 'Clients uniques',
                        'icon' => 'users',
                        'color' => 'pink',
                    ],
                    'today_orders' => $todayOrders->count(),
                    'today_sales' => $todayOrders->sum('creator_order.creator_total'),
                ];
            }
        );
    }

    /**
     * Stats des 7 derniers jours
     */
    private function getCreatorLast7DaysStats(Creator $creator): array
    {
        return Cache::remember(
            'creator_7days_stats_' . $creator->id,
            now()->addHours(1),
            function () use ($creator) {
                $stats = $this->creatorOrderService->getDailyStats($creator, 7);

                // Remplir les jours manquants avec des zéros
                $fullStats = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $dateStr = $date->format('Y-m-d');

                    $dayStat = $stats->firstWhere('date', $dateStr);

                    $fullStats[] = [
                        'date' => $dateStr,
                        'label' => $date->format('D d/m'),
                        'orders' => $dayStat->order_count ?? 0,
                        'sales' => $dayStat->total_sales ?? 0,
                    ];
                }

                return $fullStats;
            }
        );
    }

    /**
     * Récupérer les produits du créateur avec stats
     */
    private function getCreatorProducts(Creator $creator): array
    {
        $topProducts = $this->creatorOrderService->getTopProducts($creator, 5);

        $totalProducts = $creator->products()->count();
        $productsWithSales = $topProducts->count();

        return [
            'total_products' => $totalProducts,
            'products_with_sales' => $productsWithSales,
            'best_selling_products' => $topProducts->map(function ($product) {
                return [
                    'wp_product_id' => $product->wp_product_id,
                    'name' => $product->product_name,
                    'sku' => $product->sku ?? 'N/A',
                    'total_quantity' => $product->total_quantity,
                    'total_sales' => $product->total_sales,
                    'order_count' => $product->order_count,
                    'has_sales' => true,
                ];
            })->toArray(),
            'total_products_sales' => $topProducts->sum('total_sales'),
            'total_products_quantity' => $topProducts->sum('total_quantity'),
        ];
    }

    /**
     * Alertes pour le créateur
     */
    private function getCreatorAlerts(Creator $creator): array
    {
        return [
            'pending_orders' => $creator->orders()
                ->where('status', 'pending')
                ->where('orders.created_at', '<', now()->subHours(24))
                ->count(),
            'low_performing_products' => 0, // À implémenter si nécessaire
            'no_sales_this_month' => !$creator->orders()
                ->where('status', 'completed')
                ->whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year)
                ->exists(),
        ];
    }

    /**
     * Calculer le taux de croissance
     */
    private function calculateGrowthRate($previous, $current): array
    {
        if ($previous == 0) {
            return [
                'percentage' => $current > 0 ? 100 : 0,
                'trend' => $current > 0 ? 'up' : 'neutral'
            ];
        }

        $percentage = (($current - $previous) / $previous) * 100;

        return [
            'percentage' => round($percentage, 1),
            'trend' => $percentage > 0 ? 'up' : ($percentage < 0 ? 'down' : 'neutral')
        ];
    }

    /**
     * Commandes récentes
     */
    private function getRecentLocalOrders(Creator $creator)
    {
        return $creator->orders()
            ->with(['items' => function ($query) use ($creator) {
                $query->where('brand_slug', $creator->brand_slug);
            }])
            ->orderBy('order_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) use ($creator) {
                // Items are already filtered by the eager loading constraint
                $creatorItems = $order->items;

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'total' => $order->pivot->creator_total ?? $creatorItems->sum('total'),
                    'status' => $order->status,
                    'date' => $order->order_date?->format('d/m/Y H:i'),
                    'items_count' => $creatorItems->count(),
                    'items' => $creatorItems->take(2)->map(function ($item) {
                        // Handle both object and array types safely
                        return [
                            'name' => is_object($item) ? $item->product_name : ($item['product_name'] ?? 'N/A'),
                            'quantity' => is_object($item) ? $item->quantity : ($item['quantity'] ?? 0),
                            'total' => is_object($item) ? $item->total : ($item['total'] ?? 0),
                        ];
                    })->toArray(),
                ];
            });
    }

    public function transferToLogistics(Order $order)
    {
        $order->update([
            'status' => 'logistics'
        ]);

        return back()->with('success', 'Commande transférée vers la logistique.');
    }


    /**
     * Page des commandes
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('creator.setup');
        }

        $filters = [
            'q' => $request->get('search') ?? $request->get('q'),
            'status' => $request->get('status'),
            'from' => $request->get('from'),
            'to' => $request->get('to'),
        ];

        $orders = $this->creatorOrderService->getCreatorOrders($creator, 20, $filters);

        // Stats de statut
        $statusCounts = Cache::remember(
            'creator_orders_status_counts_' . $creator->id,
            now()->addMinutes(5),
            function () use ($creator) {
                return $creator->orders()
                    ->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray();
            }
        );

        // Résumé
        $summary = $this->creatorOrderService->getCreatorStats($creator);

        return view('creator.orders', [
            'orders' => $orders,
            'filters' => $filters,
            'statusCounts' => $statusCounts,
            'summary' => $summary,
            'creator' => $creator,
        ]);
    }

    /**
     * Détail d'une commande
     */
    public function showOrder($id)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('creator.setup');
        }

        $order = $creator->orders()->with('items')->findOrFail($id);

        // Récupérer les détails de la participation du créateur
        $creatorDetails = $this->creatorOrderService->getCreatorOrderDetails($order, $creator);

        if (!$creatorDetails) {
            abort(404, 'Cette commande ne concerne pas ce créateur');
        }

        // Convert items to objects for consistent blade template access
        $creatorItems = collect($creatorDetails['items'])->map(function ($item) {
            // If already an object, return as is
            if (is_object($item)) {
                return $item;
            }

            // Convert array to object
            return (object) $item;
        });

        return view('creator.orders.show', [
            'order' => $order,
            'creator' => $creator,
            'creatorItems' => $creatorItems,
            'creatorTotal' => $creatorDetails['creator_total'],
            'productCount' => $creatorDetails['product_count'],
            'totalQuantity' => $creatorDetails['total_quantity'],
        ]);
    }

    public function completeOrder($id)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('creator.setup');
        }

        $order = $creator->orders()->findOrFail($id);

        if ($order->hasCreatorCompleted($creator)) {
            return redirect()->route('creator.orders.show', $id)
                ->with('info', 'Vous avez déjà marqué votre partie comme terminée pour cette commande.');
        }

        if ($order->status === 'completed') {
            return redirect()->route('creator.orders.show', $id)
                ->with('info', 'Cette commande est déjà terminée.');
        }

        try {
            $oldStatus = $order->status;

            // ✅ Transaction rapide
            DB::beginTransaction();

            // Marquer la participation de ce créateur comme terminée
            $order->creators()->updateExistingPivot($creator->id, [
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            // Vérifier si TOUS les créateurs ont terminé
            $progress = $order->getCompletionProgress();
            $allCompleted = $order->allCreatorsCompleted();

            // Seulement si tous les créateurs ont terminé, on passe la commande à 'completed'
            if ($allCompleted) {
                $order->status = 'completed';
                $order->order_date = now();
                $order->save();
            }

            DB::commit();

            // ✅ Synchronisation WordPress avec timeout court (ne bloque plus)
            if ($allCompleted && $order->wp_order_id) {
                // Ces appels retournent immédiatement grâce aux timeouts courts
                $synced = $this->orderSyncService->syncOrderUpdateToWordPress($order, 'completion');

                if ($synced) {
                    $this->orderSyncService->addOrderNote(
                        $order->wp_order_id,
                        "Commande marquée comme terminée par {$creator->name} (CRM) - Tous les créateurs ont terminé",
                        false
                    );
                }

                Log::info("Order completed", [
                    'order_id' => $order->id,
                    'wp_order_id' => $order->wp_order_id,
                    'creator_id' => $creator->id,
                    'old_status' => $oldStatus,
                    'wp_synced' => $synced,
                ]);
            } elseif (!$allCompleted) {
                Log::info("Creator marked their part as completed, waiting for others", [
                    'order_id' => $order->id,
                    'creator_id' => $creator->id,
                    'completed' => $progress['completed'],
                    'total' => $progress['total'],
                    'pending' => $progress['pending'],
                ]);
            }

            // ✅ Réponse rapide à l'utilisateur
            if ($allCompleted) {
                return redirect()->route('creator.orders.show', $id)
                    ->with('success', 'La commande a été marquée comme terminée. Tous les créateurs ont terminé leur partie.');
            } else {
                $pendingCount = $progress['pending'];
                return redirect()->route('creator.orders.show', $id)
                    ->with('success', "Votre partie est terminée. En attente de {$pendingCount} autre(s) créateur(s).");
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la complétion de la commande', [
                'order_id' => $id,
                'creator_id' => $creator->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('creator.orders.show', $id)
                ->with('error', 'Une erreur est survenue lors de la complétion de la commande.');
        }
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('creator.setup');
        }

        $order = $creator->orders()->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,in-production,shipped,completed,cancelled'
        ]);

        try {
            $oldStatus = $order->status;
            $newStatus = $validated['status'];

            if ($oldStatus === $newStatus) {
                return redirect()->route('creator.orders.show', $id)
                    ->with('info', 'Le statut est déjà ' . $newStatus);
            }

            // Mise à jour du statut
            $order->status = $newStatus;

            if ($newStatus === 'completed') {
                $order->order_date = now();
            }

            $order->save();

            // L'Observer gérera la synchronisation automatiquement

            return redirect()->route('creator.orders.show', $id)
                ->with('success', "Statut mis à jour : {$oldStatus} → {$newStatus}");
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du statut', [
                'order_id' => $id,
                'creator_id' => $creator->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('creator.orders.show', $id)
                ->with('error', 'Une erreur est survenue lors de la mise à jour du statut.');
        }
    }

    /**
     * Page des produits du créateur
     */
    public function products(Request $request)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('creator.setup');
        }

        // Récupérer les filtres avec des valeurs par défaut
        $filters = [
            'q' => $request->get('search') ?? $request->get('q'),
            'has_sales' => $request->get('has_sales'),
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
        ];

        // Récupérer les paramètres de tri
        $sort_by = $request->get('sort_by', 'name');
        $sort_order = $request->get('sort_order', 'asc');

        // Récupérer les produits du créateur par brand_slug
        $query = Product::where('brand_slug', $creator->brand_slug);

        // Appliquer les filtres
        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['q']}%")
                    ->orWhere('sku', 'like', "%{$filters['q']}%")
                    ->orWhere('description', 'like', "%{$filters['q']}%");
            });
        }

        if ($filters['min_price']) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if ($filters['max_price']) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Récupérer les stats de vente pour les produits de ce créateur
        $productSales = OrderItem::select([
            'wp_product_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total) as total_sales'),
            DB::raw('COUNT(DISTINCT order_id) as order_count'),
        ])
            ->where('brand_slug', $creator->brand_slug)
            ->whereHas('order', function ($query) use ($creator) {
                $query->where('status', 'completed');
            })
            ->groupBy('wp_product_id')
            ->get()
            ->keyBy('wp_product_id');

        // Filtrer par ventes
        if ($filters['has_sales'] === 'yes') {
            $productIdsWithSales = $productSales->keys()->toArray();
            $query->whereIn('wp_product_id', $productIdsWithSales);
        } elseif ($filters['has_sales'] === 'no') {
            $productIdsWithSales = $productSales->keys()->toArray();
            $query->whereNotIn('wp_product_id', $productIdsWithSales);
        }

        // Appliquer le tri
        $validSortColumns = ['name', 'price', 'created_at'];
        $sort_by = in_array($sort_by, $validSortColumns) ? $sort_by : 'name';
        $sort_order = in_array($sort_order, ['asc', 'desc']) ? $sort_order : 'asc';

        $query->orderBy($sort_by, $sort_order);

        // Pagination
        $allProducts = $query->paginate(20)->withQueryString();

        // Fusionner les données
        $products = $allProducts->map(function ($product) use ($productSales) {
            $sales = $productSales->get($product->wp_product_id);

            return [
                'id' => $product->id,
                'wp_product_id' => $product->wp_product_id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'description' => $product->description,
                'brand_slug' => $product->brand_slug,
                'total_quantity' => $sales ? $sales->total_quantity : 0,
                'total_sales' => $sales ? $sales->total_sales : 0,
                'order_count' => $sales ? $sales->order_count : 0,
                'has_sales' => $sales ? true : false,
            ];
        });

        // Trier après fusion si nécessaire (pour les colonnes calculées)
        if (in_array($sort_by, ['sales', 'quantity'])) {
            $sortKey = $sort_by === 'sales' ? 'total_sales' : 'total_quantity';
            $products = $products->sortBy($sortKey, SORT_REGULAR, $sort_order === 'desc');
        }

        // Récupérer les meilleurs produits
        $bestSellingProducts = $products->sortByDesc('total_sales')->take(5);

        // Statistiques récapitulatives
        $summary = [
            'total_products' => $allProducts->total(),
            'products_with_sales' => $products->where('has_sales', true)->count(),
            'total_products_sales' => $products->sum('total_sales'),
            'total_products_quantity' => $products->sum('total_quantity'),
            'average_price' => $products->avg('price') ?? 0,
            'average_sales_per_product' => $products->where('has_sales', true)->count() > 0
                ? $products->where('has_sales', true)->avg('total_sales')
                : 0,
        ];

        return view('creator.products', [
            'products' => $products,
            'allProducts' => $allProducts,
            'bestSellingProducts' => $bestSellingProducts,
            'summary' => $summary,
            'filters' => $filters,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order,
            'creator' => $creator,
        ]);
    }

    /**
     * Détail d'un produit
     */
    public function showProduct($id)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('creator.setup');
        }

        // Récupérer le produit (vérifier qu'il appartient au créateur par brand_slug)
        $product = Product::where('wp_product_id', $id)
            ->where('brand_slug', $creator->brand_slug)
            ->firstOrFail();

        // Récupérer les stats de vente
        $salesStats = OrderItem::select([
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total) as total_sales'),
            DB::raw('COUNT(DISTINCT order_id) as order_count'),
            DB::raw('AVG(unit_price) as average_unit_price'),
        ])
            ->where('wp_product_id', $id)
            ->where('brand_slug', $creator->brand_slug)
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->first();

        // Commandes récentes avec ce produit
        $recentOrders = Order::with(['items'])
            ->whereHas('items', function ($query) use ($id, $creator) {
                $query->where('wp_product_id', $id)
                    ->where('brand_slug', $creator->brand_slug);
            })
            ->orderBy('order_date', 'desc')
            ->limit(10)
            ->get();

        // Stats mensuelles
        $monthlyStats = $this->getProductMonthlyStats($id, $creator);

        return view('creator.product.show', [
            'product' => $product,
            'creator' => $creator,
            'salesStats' => $salesStats,
            'recentOrders' => $recentOrders,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    /**
     * Récupérer les stats mensuelles d'un produit
     */
    private function getProductMonthlyStats($productId, Creator $creator): array
    {
        $stats = [];

        // 6 derniers mois
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $monthSales = OrderItem::where('wp_product_id', $productId)
                ->where('brand_slug', $creator->brand_slug)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereHas('order', function ($query) {
                    $query->where('status', 'completed');
                })
                ->select([
                    DB::raw('SUM(quantity) as quantity'),
                    DB::raw('SUM(total) as sales'),
                ])
                ->first();

            $stats[] = [
                'month' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
                'quantity' => $monthSales->quantity ?? 0,
                'sales' => $monthSales->sales ?? 0,
            ];
        }

        return $stats;
    }

    /**
     * Page des statistiques détaillées
     */
    public function analytics(Request $request)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('creator.setup');
        }

        // Période sélectionnée (par défaut: 30 derniers jours)
        $period = $request->get('period', '30days');

        // Calculer la date de début en fonction de la période
        switch ($period) {
            case '7days':
                $startDate = now()->subDays(7);
                break;
            case '90days':
                $startDate = now()->subDays(90);
                break;
            case 'year':
                $startDate = now()->subYear();
                break;
            default: // '30days'
                $startDate = now()->subDays(30);
                break;
        }

        // Récupérer les statistiques de vente par jour
        $dailyStats = $this->getDailySalesStats($creator, $startDate);

        // Récupérer les statistiques par produit
        $productStats = $this->getProductAnalytics($creator, $startDate);

        // Récupérer les statistiques par client
        $customerStats = $this->getCustomerAnalytics($creator, $startDate);

        // Récupérer les statistiques par statut de commande
        $orderStatusStats = $this->getOrderStatusStats($creator);

        return view('creator.analytics', [
            'creator' => $creator,
            'period' => $period,
            'dailyStats' => $dailyStats,
            'productStats' => $productStats,
            'customerStats' => $customerStats,
            'orderStatusStats' => $orderStatusStats,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Statistiques de vente par jour
     */
    private function getDailySalesStats(Creator $creator, $startDate): array
    {
        $stats = [];

        // Récupérer les données par jour
        $dailyData = Order::select([
            DB::raw('DATE(order_date) as date'),
            DB::raw('COUNT(*) as orders_count'),
            DB::raw('SUM(total) as total_sales'),
        ])
            ->where('status', 'completed')
            ->where('order_date', '>=', $startDate)
            ->whereHas('items', function ($query) use ($creator) {
                $query->where('brand_slug', $creator->brand_slug);
            })
            ->groupBy(DB::raw('DATE(order_date)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Créer un tableau pour tous les jours
        $currentDate = clone $startDate;
        while ($currentDate <= now()) {
            $dateStr = $currentDate->format('Y-m-d');
            $data = $dailyData->get($dateStr);

            $stats[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d/m'),
                'orders' => $data ? $data->orders_count : 0,
                'sales' => $data ? $data->total_sales : 0,
            ];

            $currentDate->addDay();
        }

        return $stats;
    }

    /**
     * Statistiques par produit
     */
    private function getProductAnalytics(Creator $creator, $startDate): array
    {
        $productStats = OrderItem::select([
            'order_items.wp_product_id',
            'products.name',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.total) as total_sales'),
            DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
        ])
            ->join('products', 'order_items.wp_product_id', '=', 'products.wp_product_id')
            ->where('order_items.brand_slug', $creator->brand_slug)
            ->whereHas('order', function ($query) use ($startDate) {
                $query->where('status', 'completed')
                    ->where('order_date', '>=', $startDate);
            })
            ->groupBy('order_items.wp_product_id', 'products.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return [
            'top_products_by_sales' => $productStats,
            'top_products_by_quantity' => $productStats->sortByDesc('total_quantity')->values(),
        ];
    }

    /**
     * Statistiques par client
     */
    private function getCustomerAnalytics(Creator $creator, $startDate): array
    {
        $customerStats = Order::select([
            'customer_email',
            'customer_name',
            DB::raw('COUNT(*) as orders_count'),
            DB::raw('SUM(total) as total_spent'),
            DB::raw('MAX(order_date) as last_order_date'),
        ])
            ->where('status', 'completed')
            ->where('order_date', '>=', $startDate)
            ->whereHas('items', function ($query) use ($creator) {
                $query->where('brand_slug', $creator->brand_slug);
            })
            ->groupBy('customer_email', 'customer_name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return [
            'top_customers_by_spending' => $customerStats,
            'top_customers_by_orders' => $customerStats->sortByDesc('orders_count')->values(),
        ];
    }

    /**
     * Statistiques par statut de commande
     */
    private function getOrderStatusStats(Creator $creator): array
    {
        $statusStats = Order::select([
            'status',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total_amount'),
        ])
            ->whereHas('items', function ($query) use ($creator) {
                $query->where('brand_slug', $creator->brand_slug);
            })
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $totalOrders = $statusStats->sum('count');

        return [
            'status_stats' => $statusStats,
            'total_orders' => $totalOrders,
            'completion_rate' => $totalOrders > 0
                ? ($statusStats->get('completed')->count ?? 0) / $totalOrders * 100
                : 0,
        ];
    }

    /**
     * Page de profil du créateur
     */
    public function profile()
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('creator.setup');
        }

        return view('creator.profile', [
            'creator' => $creator,
            'user' => $user,
        ]);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('creator.setup');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'brand_name' => 'required|string|max:255',
            'brand_slug' => 'required|string|max:255|unique:creators,brand_slug,' . $creator->id,
            'description' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        // Mettre à jour le créateur
        $creator->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'brand_name' => $validated['brand_name'],
            'brand_slug' => $validated['brand_slug'],
            'description' => $validated['description'],
            'address' => $validated['address'] ?? null,
        ]);

        // Mettre à jour l'utilisateur si l'email a changé
        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->name = $validated['name'];
            $user->save();
        }

        return redirect()->route('creator.profile')
            ->with('success', 'Profil mis à jour avec succès');
    }

    /**
     * Page de configuration initiale
     */
    public function setup()
    {
        $user = Auth::user();

        // Si le créateur existe déjà, rediriger vers le dashboard
        if ($user->creator) {
            return redirect()->route('creator.dashboard');
        }

        return view('creator.setup');
    }

    /**
     * Traiter la configuration initiale
     */
    public function processSetup(Request $request)
    {
        $user = Auth::user();

        // Si le créateur existe déjà, rediriger
        if ($user->creator) {
            return redirect()->route('creator.dashboard');
        }

        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
            'brand_slug' => 'required|string|max:255|unique:creators,brand_slug',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);

        // Créer le créateur
        $creator = Creator::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'brand_name' => $validated['brand_name'],
            'brand_slug' => $validated['brand_slug'],
            'description' => $validated['description'],
            'phone' => $validated['phone'],
            'website' => $validated['website'],
            'status' => 'active',
        ]);

        // Essayer de créer le créateur dans WordPress
        try {
            $wpCreatorId = $this->wordPressService->createCreatorInWordPress($creator);
            $creator->wp_creator_id = $wpCreatorId;
            $creator->save();
        } catch (\Exception $e) {
            Log::error('Failed to create creator in WordPress', [
                'error' => $e->getMessage(),
                'creator_id' => $creator->id,
            ]);
        }

        return redirect()->route('creator.dashboard')
            ->with('success', 'Profil créateur créé avec succès!');
    }

    /**
     * API: Statistiques du créateur pour les widgets
     */
    public function statsApi(Request $request)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return response()->json([
                'success' => false,
                'error' => 'Creator profile not found'
            ], 404);
        }

        $period = $request->get('period', 'month');

        // Stats rapides
        $quickStats = [
            'today_orders' => $creator->orders()
                ->where('status', 'completed')
                ->whereDate('order_date', today())
                ->count(),
            'today_sales' => $creator->orders()
                ->where('status', 'completed')
                ->whereDate('order_date', today())
                ->sum('creator_order.creator_total'),
            'month_orders' => $creator->orders()
                ->where('status', 'completed')
                ->whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year)
                ->count(),
            'month_sales' => $creator->orders()
                ->where('status', 'completed')
                ->whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year)
                ->sum('creator_order.creator_total'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'quick_stats' => $quickStats,
                'creator' => [
                    'id' => $creator->id,
                    'name' => $creator->name,
                    'brand_name' => $creator->brand_name,
                    'brand_slug' => $creator->brand_slug,
                ],
            ]
        ]);
    }

    /**
     * API: Commandes récentes pour les widgets
     */
    public function recentOrdersApi()
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return response()->json([
                'success' => false,
                'error' => 'Creator profile not found'
            ], 404);
        }

        $orders = $creator->orders()
            ->with(['items' => function ($query) use ($creator) {
                $query->where('brand_slug', $creator->brand_slug);
            }])
            ->orderBy('order_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) use ($creator) {
                // Filtrer uniquement les items du créateur
                $creatorItems = $order->items->where('brand_slug', $creator->brand_slug);

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'total' => $order->pivot->creator_total ?? $creatorItems->sum('total'),
                    'status' => $order->status,
                    'date' => $order->order_date?->format('d/m/Y H:i'),
                    'items_count' => $creatorItems->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}

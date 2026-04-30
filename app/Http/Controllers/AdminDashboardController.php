<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Creator;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SyncLog;
use App\Jobs\SyncOrdersJob;
use App\Services\WordPressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class AdminDashboardController extends Controller
{
    protected $wordPressService;

    public function __construct(WordPressService $wordPressService)
    {
        $this->wordPressService = $wordPressService;
    }

    /**
     * Dashboard principal admin
     */
    public function dashboard()
    {
        // KPI principaux
        $kpis = $this->getMainKPIs();

        // Stats locales
        $localStats = $this->getLocalStats();

        // Stats améliorées
        $enhancedStats = $this->getEnhancedStats();

        // Stats des 7 derniers jours
        $last7DaysStats = $this->getLast7DaysStats();

        // Alertes système
        $alerts = $this->getSystemAlerts();

        // Activités récentes
        $recentActivities = $this->getRecentActivities();

        // Derniers créateurs ajoutés
        $recentCreators = Creator::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Dernières commandes synchronisées
        $recentOrders = Order::with(['creators', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Dernière synchronisation
        $lastSync = SyncLog::where('sync_type', 'orders')
            ->where('status', 'success')
            ->latest()
            ->first();

        // Logs récents
        $recentLogs = SyncLog::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'kpis' => $kpis,
            'localStats' => $localStats,
            'enhancedStats' => $enhancedStats,
            'last7DaysStats' => $last7DaysStats,
            'alerts' => $alerts,
            'recentActivities' => $recentActivities,
            'recentCreators' => $recentCreators,
            'recentOrders' => $recentOrders,
            'lastSync' => $lastSync,
            'recentLogs' => $recentLogs,
            'apiConnected' => $this->wordPressService->testConnection(),
        ]);
    }

    /**
     * KPI principaux du dashboard
     */
    private function getMainKPIs(): array
    {
        return Cache::remember('dashboard_main_kpis', now()->addMinutes(10), function () {

            // Toutes les commandes complétées
            $completedOrders = Order::where('status', 'completed');

            // Commandes complétées aujourd'hui
            $todayCompletedOrders = Order::where('status', 'completed')
                ->whereDate('order_date', today());

            // Commandes complétées ce mois
            $monthCompletedOrders = Order::where('status', 'completed')
                ->whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year);

            // Commandes complétées le mois dernier
            $lastMonthCompletedOrders = Order::where('status', 'completed')
                ->whereMonth('order_date', now()->subMonth()->month)
                ->whereYear('order_date', now()->subMonth()->year);

            // KPI 1: Chiffre d'affaires total
            $totalRevenue = $completedOrders->sum('total');

            // KPI 2: Chiffre d'affaires du mois
            $monthRevenue = $monthCompletedOrders->sum('total');
            $lastMonthRevenue = $lastMonthCompletedOrders->sum('total');
            $revenueGrowth = $this->calculateGrowthRate($lastMonthRevenue, $monthRevenue);

            // KPI 3: Nombre de commandes complétées
            $totalCompletedOrders = $completedOrders->count();
            $monthCompletedCount = $monthCompletedOrders->count();
            $lastMonthCompletedCount = $lastMonthCompletedOrders->count();
            $ordersGrowth = $this->calculateGrowthRate($lastMonthCompletedCount, $monthCompletedCount);

            // KPI 4: Panier moyen (seulement commandes complétées)
            $averageOrderValue = $totalCompletedOrders > 0
                ? $totalRevenue / $totalCompletedOrders
                : 0;

            $monthAvgOrderValue = $monthCompletedCount > 0
                ? $monthRevenue / $monthCompletedCount
                : 0;

            $lastMonthAvgOrderValue = $lastMonthCompletedCount > 0
                ? $lastMonthRevenue / $lastMonthCompletedCount
                : 0;

            $avgOrderValueGrowth = $this->calculateGrowthRate($lastMonthAvgOrderValue, $monthAvgOrderValue);

            // KPI 5: Taux de conversion (commandes complétées / total commandes)
            $allOrders = Order::count();
            $conversionRate = $allOrders > 0
                ? ($totalCompletedOrders / $allOrders) * 100
                : 0;

            $monthAllOrders = Order::whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year)
                ->count();

            $monthConversionRate = $monthAllOrders > 0
                ? ($monthCompletedCount / $monthAllOrders) * 100
                : 0;

            $lastMonthAllOrders = Order::whereMonth('order_date', now()->subMonth()->month)
                ->whereYear('order_date', now()->subMonth()->year)
                ->count();

            $lastMonthConversionRate = $lastMonthAllOrders > 0
                ? ($lastMonthCompletedCount / $lastMonthAllOrders) * 100
                : 0;

            $conversionGrowth = $this->calculateGrowthRate($lastMonthConversionRate, $monthConversionRate);

            // KPI 6: Nombre de clients uniques (basé sur commandes complétées)
            $totalCustomers = Order::where('status', 'completed')
                ->distinct('customer_email')
                ->count('customer_email');

            $monthCustomers = Order::where('status', 'completed')
                ->whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year)
                ->distinct('customer_email')
                ->count('customer_email');

            $lastMonthCustomers = Order::where('status', 'completed')
                ->whereMonth('order_date', now()->subMonth()->month)
                ->whereYear('order_date', now()->subMonth()->year)
                ->distinct('customer_email')
                ->count('customer_email');

            $customersGrowth = $this->calculateGrowthRate($lastMonthCustomers, $monthCustomers);

            // KPI Bonus: Clients fidèles (plus d'une commande complétée)
            $repeatCustomers = Order::where('status', 'completed')
                ->select('customer_email')
                ->groupBy('customer_email')
                ->havingRaw('COUNT(*) > 1')
                ->count();

            $customerRetentionRate = $totalCustomers > 0
                ? ($repeatCustomers / $totalCustomers) * 100
                : 0;

            return [
                // KPI 1
                'total_revenue' => [
                    'value' => $totalRevenue,
                    'label' => 'Chiffre d\'affaires total',
                    'icon' => 'currency',
                    'color' => 'blue',
                ],

                // KPI 2
                'month_revenue' => [
                    'value' => $monthRevenue,
                    'label' => 'CA du mois',
                    'growth' => $revenueGrowth,
                    'icon' => 'trending',
                    'color' => 'green',
                ],

                // KPI 3
                'completed_orders' => [
                    'value' => $totalCompletedOrders,
                    'month_value' => $monthCompletedCount,
                    'label' => 'Commandes complétées',
                    'growth' => $ordersGrowth,
                    'icon' => 'check',
                    'color' => 'purple',
                ],

                // KPI 4
                'average_order_value' => [
                    'value' => $averageOrderValue,
                    'month_value' => $monthAvgOrderValue,
                    'label' => 'Panier moyen',
                    'growth' => $avgOrderValueGrowth,
                    'icon' => 'cart',
                    'color' => 'yellow',
                ],

                // KPI 5
                'conversion_rate' => [
                    'value' => $conversionRate,
                    'month_value' => $monthConversionRate,
                    'label' => 'Taux de conversion',
                    'growth' => $conversionGrowth,
                    'icon' => 'percentage',
                    'color' => 'indigo',
                ],

                // KPI 6
                'total_customers' => [
                    'value' => $totalCustomers,
                    'month_value' => $monthCustomers,
                    'label' => 'Clients uniques',
                    'growth' => $customersGrowth,
                    'icon' => 'users',
                    'color' => 'pink',
                ],

                // KPI Bonus
                'customer_retention' => [
                    'value' => $customerRetentionRate,
                    'repeat_customers' => $repeatCustomers,
                    'label' => 'Taux de fidélisation',
                    'icon' => 'heart',
                    'color' => 'red',
                ],

                // Stats additionnelles
                'today_completed' => $todayCompletedOrders->count(),
                'today_revenue' => $todayCompletedOrders->sum('total'),
            ];
        });
    }

    /**
     * Récupère les alertes système
     */
    private function getSystemAlerts(): array
    {
        return [
            'failed_syncs' => SyncLog::where('status', 'failed')
                ->where('created_at', '>', now()->subDay())
                ->count(),
            'pending_orders' => Order::where('status', 'pending')
                ->where('created_at', '<', now()->subHours(24))
                ->count(),
            'cancelled_orders_today' => Order::where('status', 'cancelled')
                ->whereDate('order_date', today())
                ->count(),
        ];
    }

    /**
     * Récupère les activités récentes
     */
    private function getRecentActivities()
    {
        return SyncLog::orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'type' => $log->sync_type,
                    'status' => $log->status,
                    'message' => $log->message,
                    'time' => $log->created_at->diffForHumans(),
                    'icon' => $this->getActivityIcon($log->sync_type, $log->status),
                ];
            });
    }

    private function getActivityIcon($type, $status): string
    {
        $icons = [
            'orders' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'products' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'sync' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        ];

        return $icons[$type] ?? 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
    }

    /**
     * Récupère les stats globales améliorées
     */
    private function getEnhancedStats(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Statistiques créateurs
            |--------------------------------------------------------------------------
            */
            'creator_stats' => Cache::remember('stats_creators_enhanced', now()->addMinutes(30), function () {

                $topCreatorsBySales = Creator::select(
                    'creators.id',
                    'creators.name',
                    'creators.brand_slug',
                    'creators.user_id',
                    DB::raw('SUM(orders.total) as total_sales'),
                    DB::raw('COUNT(DISTINCT orders.id) as orders_count')
                )
                    ->join('creator_order', 'creators.id', '=', 'creator_order.creator_id')
                    ->join('orders', function ($join) {
                        $join->on('creator_order.order_id', '=', 'orders.id')
                            ->where('orders.status', 'completed');
                    })
                    ->groupBy('creators.id', 'creators.name', 'creators.brand_slug', 'creators.user_id')
                    ->orderByDesc('total_sales')
                    ->limit(5)
                    ->get();

                $creatorsWithMostOrders = Creator::withCount([
                    'orders' => function ($query) {
                        $query->where('status', 'completed');
                    }
                ])
                    ->orderByDesc('orders_count')
                    ->limit(5)
                    ->get();

                return [
                    'total_creators' => Creator::count(),
                    'active_creators' => Creator::where('status', 'active')->count(),
                    'creators_with_sales' => Creator::whereHas('orders', function ($query) {
                        $query->where('status', 'completed');
                    })->count(),
                    'top_creators_by_sales' => $topCreatorsBySales,
                    'creators_with_most_orders' => $creatorsWithMostOrders,
                ];
            }),

            /*
            |--------------------------------------------------------------------------
            | Statistiques produits (pas de gestion de stock)
            |--------------------------------------------------------------------------
            */
            'product_stats' => Cache::remember('stats_products', now()->addMinutes(30), function () {

                $bestSellingProduct = OrderItem::select(
                    'wp_product_id',
                    'product_name',
                    DB::raw('SUM(quantity) as total_sold'),
                    DB::raw('SUM(total) as total_revenue')
                )
                    ->whereHas('order', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->groupBy('wp_product_id', 'product_name')
                    ->orderByDesc('total_sold')
                    ->first();

                $topProducts = OrderItem::select(
                    'wp_product_id',
                    'product_name',
                    'brand_slug',
                    DB::raw('SUM(quantity) as total_sold'),
                    DB::raw('SUM(total) as total_revenue')
                )
                    ->whereHas('order', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->groupBy('wp_product_id', 'product_name', 'brand_slug')
                    ->orderByDesc('total_revenue')
                    ->limit(5)
                    ->get();

                return [
                    'total_products' => Product::count(),
                    'products_with_sales' => OrderItem::whereHas('order', function ($query) {
                        $query->where('status', 'completed');
                    })
                        ->distinct('wp_product_id')
                        ->count('wp_product_id'),
                    'best_selling_product' => $bestSellingProduct,
                    'top_products' => $topProducts,
                ];
            }),

            /*
            |--------------------------------------------------------------------------
            | Statistiques clients
            |--------------------------------------------------------------------------
            */
            'customer_stats' => Cache::remember('stats_customers', now()->addMinutes(15), function () {

                $totalCustomers = Order::where('status', 'completed')
                    ->distinct('customer_email')
                    ->count('customer_email');

                $repeatCustomers = Order::where('status', 'completed')
                    ->select('customer_email')
                    ->groupBy('customer_email')
                    ->havingRaw('COUNT(*) > 1')
                    ->count();

                $newCustomersThisMonth = Order::where('status', 'completed')
                    ->whereMonth('order_date', now()->month)
                    ->whereYear('order_date', now()->year)
                    ->distinct('customer_email')
                    ->count('customer_email');

                $topCustomers = Order::where('status', 'completed')
                    ->select(
                        'customer_email',
                        'customer_name',
                        DB::raw('COUNT(*) as orders_count'),
                        DB::raw('SUM(total) as total_spent')
                    )
                    ->groupBy('customer_email', 'customer_name')
                    ->orderByDesc('total_spent')
                    ->limit(5)
                    ->get();

                return [
                    'total_customers' => $totalCustomers,
                    'repeat_customers' => $repeatCustomers,
                    'retention_rate' => $totalCustomers > 0
                        ? round(($repeatCustomers / $totalCustomers) * 100, 1)
                        : 0,
                    'new_customers_this_month' => $newCustomersThisMonth,
                    'top_customers' => $topCustomers,
                ];
            }),
        ];
    }

    /**
     * Calcule le taux de croissance
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
     * Récupère les statistiques des 7 derniers jours (commandes complétées uniquement)
     */
    private function getLast7DaysStats(): array
    {
        return Cache::remember('stats_last_7_days', now()->addHours(1), function () {
            $stats = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dayStart = $date->copy()->startOfDay();
                $dayEnd = $date->copy()->endOfDay();

                $completedOrders = Order::where('status', 'completed')
                    ->whereBetween('order_date', [$dayStart, $dayEnd]);

                $stats[] = [
                    'date' => $date->format('Y-m-d'),
                    'label' => $date->format('D d/m'),
                    'orders' => $completedOrders->count(),
                    'sales' => $completedOrders->sum('total'),
                ];
            }

            return $stats;
        });
    }

    /**
     * Stats locales avec cache
     */
    private function getLocalStats(): array
    {
        return [
            // Créateurs
            'total_creators' => Cache::remember('stats_total_creators', now()->addMinutes(10), function () {
                return Creator::count();
            }),
            'active_creators' => Cache::remember('stats_active_creators', now()->addMinutes(10), function () {
                return Creator::where('status', 'active')->count();
            }),

            // Utilisateurs
            'total_users' => Cache::remember('stats_total_users', now()->addMinutes(10), function () {
                return User::count();
            }),
            'admin_users' => Cache::remember('stats_admin_users', now()->addMinutes(10), function () {
                return User::where('role', 'admin')->count();
            }),

            // Commandes (seulement les complétées pour le CA)
            'total_orders' => Cache::remember('stats_total_orders', now()->addMinutes(5), function () {
                return Order::count();
            }),
            'completed_orders' => Cache::remember('stats_completed_orders', now()->addMinutes(5), function () {
                return Order::where('status', 'completed')->count();
            }),
            'total_sales' => Cache::remember('stats_total_sales', now()->addMinutes(5), function () {
                return Order::where('status', 'completed')->sum('total');
            }),
            'today_orders' => Cache::remember('stats_today_orders', now()->addHours(1), function () {
                return Order::whereDate('order_date', today())->count();
            }),
            'pending_orders' => Cache::remember('stats_pending_orders', now()->addMinutes(5), function () {
                return Order::where('status', 'pending')->count();
            }),
        ];
    }

    /**
     * Récupère les stats WordPress avec cache
     */
    private function getWordPressStats(): array
    {
        return Cache::remember('dashboard_wordpress_stats', now()->addMinutes(5), function () {
            try {
                $stats = $this->wordPressService->getGlobalStats('month');

                return [
                    'total_sales' => $stats['data']['total_sales'] ?? 0,
                    'order_count' => $stats['data']['total_orders'] ?? 0,
                    'customer_count' => $stats['data']['total_customers'] ?? 0,
                    'conversion_rate' => $stats['data']['conversion_rate'] ?? 0,
                    'average_order_value' => $stats['data']['average_order_value'] ?? 0,
                ];
            } catch (\Exception $e) {
                // Fallback aux données locales en cas d'erreur
                return $this->getFallbackWordPressStats();
            }
        });
    }

    /**
     * Stats de fallback
     */
    private function getFallbackWordPressStats(): array
    {
        return [
            'total_sales' => Order::sum('total'),
            'order_count' => Order::count(),
            'customer_count' => Order::distinct('customer_email')->count(),
            'conversion_rate' => 2.5,
            'average_order_value' => Order::avg('total') ?? 0,
        ];
    }

    /**
     * Liste des créateurs
     */
    public function creators(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Creator::with(['user'])->withCount('orders');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('brand_slug', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['active', 'inactive', 'suspended'])) {
            $query->where('status', $status);
        }

        $creators = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.creators', [
            'creators' => $creators,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function showCreator(int $id)
    {
        try {
            $creator = Creator::findOrFail($id);
            return view('admin.creators.show', compact('creator'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Créateur non trouvé');
        }
    }

    /**
     * Liste des commandes synchronisées depuis la base locale
     */
    public function orders(Request $request)
    {
        // Récupérer les filtres avec des valeurs par défaut
        $filters = [
            'q' => $request->get('search') ?? $request->get('q'), // Supporte les deux noms
            'status' => $request->get('status'),
            'order_count' => $request->get('order_count'),
            'from' => $request->get('from'),
            'to' => $request->get('to'),
        ];

        // Récupérer les paramètres de tri
        $sort_by = $request->get('sort_by', 'order_date');
        $sort_order = $request->get('sort_order', 'desc');

        $query = Order::with(['creators', 'items']);

        // Appliquer les filtres
        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_number', 'like', "%{$filters['q']}%")
                    ->orWhere('customer_name', 'like', "%{$filters['q']}%")
                    ->orWhere('customer_email', 'like', "%{$filters['q']}%")
                    ->orWhere('wp_order_id', 'like', "%{$filters['q']}%");
            });
        }

        if ($filters['status'] && in_array($filters['status'], ['pending', 'processing', 'completed', 'cancelled', 'on-hold'])) {
            $query->where('status', $filters['status']);
        }

        if ($filters['order_count']) {
            switch ($filters['order_count']) {
                case '1-2':
                    $query->whereHas('items', function ($q) {
                        $q->having(DB::raw('COUNT(*)'), '>=', 1)
                            ->having(DB::raw('COUNT(*)'), '<=', 2);
                    }, '=', 1);
                    break;
                case '3-5':
                    $query->whereHas('items', function ($q) {
                        $q->having(DB::raw('COUNT(*)'), '>=', 3)
                            ->having(DB::raw('COUNT(*)'), '<=', 5);
                    }, '=', 1);
                    break;
                case '5+':
                    $query->whereHas('items', function ($q) {
                        $q->having(DB::raw('COUNT(*)'), '>', 5);
                    }, '=', 1);
                    break;
            }
        }

        if ($filters['from']) {
            $query->whereDate('order_date', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->whereDate('order_date', '<=', $filters['to']);
        }

        // Appliquer le tri
        $validSortColumns = ['order_number', 'customer_name', 'status', 'total', 'order_date'];
        $sort_by = in_array($sort_by, $validSortColumns) ? $sort_by : 'order_date';
        $sort_order = in_array($sort_order, ['asc', 'desc']) ? $sort_order : 'desc';
        
        $query->orderBy($sort_by, $sort_order);

        // Pagination
        $orders = $query->paginate(20)->withQueryString();

        // Compter par statut pour les filtres
        $statusCounts = Cache::remember('orders_status_counts', now()->addMinutes(5), function () {
            return Order::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });

        // Liste des créateurs pour le filtre
        $creators = Creator::orderBy('name')->get();
        
        return view('admin.orders', [
            'orders' => $orders,
            'filters' => $filters,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order,
            'statusCounts' => $statusCounts,
            'creators' => $creators,
            'pendingCount' => $statusCounts['pending'] ?? 0,
            'completedCount' => $statusCounts['completed'] ?? 0,
            'cancelledCount' => $statusCounts['cancelled'] ?? 0,
        ]);
    }

    /**
     * Détail d'une commande
     */
    public function showOrder($id)
    {
        $order = Order::with(['creators', 'items'])
            ->findOrFail($id);

        return view('admin.order-show', [
            'order' => $order,
        ]);
    }

    /**
     * Synchroniser les commandes manuellement
     */
    public function syncOrders(Request $request)
    {
        $type = $request->input('type', 'incremental');
        $force = $request->boolean('force', false);

        // Lancer le job
        SyncOrdersJob::dispatch($type, $force);

        // Invalider les caches
        Cache::forget('dashboard_wordpress_stats');
        Cache::forget('stats_local_orders');
        Cache::forget('stats_local_sales');
        Cache::forget('stats_today_orders');
        Cache::forget('orders_status_counts');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Synchronisation lancée en arrière-plan',
                'type' => $type,
                'force' => $force,
            ]);
        }

        return redirect()->route('admin.orders')
            ->with('success', 'Synchronisation des commandes lancée');
    }

    /**
     * Produits avec stats des ventes - TOUS LES PRODUITS
     */
    public function products(Request $request)
    {
        // Récupérer les paramètres de filtrage
        $filters = [
            'search' => $request->input('search'),
            'brand' => $request->input('brand'),
            'has_sales' => $request->input('has_sales'),
            'sort_by' => $request->input('sort_by', 'name'),
            'sort_order' => $request->input('sort_order', 'asc'),
        ];

        // 1. Récupérer TOUS les brand_slug depuis la table products
        $allBrands = Product::select('brand_slug')
            ->whereNotNull('brand_slug')
            ->where('brand_slug', '!=', '')
            ->distinct()
            ->orderBy('brand_slug')
            ->pluck('brand_slug')
            ->toArray();

        // 2. Compter le nombre de produits PAR MARQUE depuis la table products
        $productCountsByBrand = Product::select([
            'brand_slug',
            DB::raw('COUNT(*) as product_count'),
            DB::raw('AVG(price) as average_price'),
        ])
            ->whereNotNull('brand_slug')
            ->where('brand_slug', '!=', '')
            ->groupBy('brand_slug')
            ->orderBy('product_count', 'desc')
            ->get()
            ->keyBy('brand_slug');

        // 3. Stats des ventes par marque (COMMANDES COMPLÉTÉES UNIQUEMENT)
        $salesByBrand = OrderItem::select([
            'order_items.brand_slug',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.total) as total_sales'),
            DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
            DB::raw('COUNT(DISTINCT order_items.wp_product_id) as product_with_sales_count'),
        ])
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereNotNull('order_items.brand_slug')
            ->groupBy('order_items.brand_slug')
            ->get()
            ->keyBy('brand_slug');

        // 4. Fusionner toutes les données pour avoir TOUTES les marques
        $brandStats = collect($allBrands)->map(function ($brandSlug) use ($productCountsByBrand, $salesByBrand) {
            $productStats = $productCountsByBrand->get($brandSlug);
            $salesStats = $salesByBrand->get($brandSlug);

            return [
                'brand_slug' => $brandSlug,
                'product_count' => $productStats ? $productStats->product_count : 0,
                'average_price' => $productStats ? $productStats->average_price : 0,
                'total_quantity' => $salesStats ? $salesStats->total_quantity : 0,
                'total_sales' => $salesStats ? $salesStats->total_sales : 0,
                'order_count' => $salesStats ? $salesStats->order_count : 0,
                'product_with_sales_count' => $salesStats ? $salesStats->product_with_sales_count : 0,
                'has_sales' => $salesStats ? ($salesStats->total_quantity > 0) : false,
            ];
        })->sortByDesc('total_sales');

        // 5. Récupérer les stats de ventes par produit (COMMANDES COMPLÉTÉES UNIQUEMENT)
        $productSalesStats = OrderItem::select([
            'order_items.wp_product_id',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.total) as total_sales'),
            DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
        ])
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->groupBy('order_items.wp_product_id')
            ->get()
            ->keyBy('wp_product_id');

        // 6. Récupérer TOUS les produits depuis la base de données avec filtres
        $query = Product::query();

        // Appliquer les filtres
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('sku', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('wp_product_id', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['brand'])) {
            $query->where('brand_slug', $filters['brand']);
        }

        // Filtrer par ventes
        if ($filters['has_sales'] === 'yes') {
            $productIdsWithSales = $productSalesStats->keys()->toArray();
            $query->whereIn('wp_product_id', $productIdsWithSales);
        } elseif ($filters['has_sales'] === 'no') {
            $productIdsWithSales = $productSalesStats->keys()->toArray();
            $query->whereNotIn('wp_product_id', $productIdsWithSales);
        }

        // Appliquer le tri
        $sortField = $filters['sort_by'];
        $sortDirection = $filters['sort_order'];

        switch ($sortField) {
            case 'price':
                $query->orderBy('price', $sortDirection);
                break;
            case 'brand':
                $query->orderBy('brand_slug', $sortDirection);
                break;
            case 'sales':
                // Pour trier par CA, nous devrons le faire après avoir chargé les produits
                $sortBySales = true;
                $query->orderBy('name', $sortDirection); // Tri par défaut
                break;
            case 'quantity':
                // Pour trier par quantité, nous devrons le faire après
                $sortByQuantity = true;
                $query->orderBy('name', $sortDirection); // Tri par défaut
                break;
            default: // 'name' ou autre
                $query->orderBy('name', $sortDirection);
                break;
        }

        // Pagination
        $allProducts = $query->paginate(50);

        // 7. Récupérer les infos produits depuis WordPress si besoin
        $productIds = $allProducts->pluck('wp_product_id')->toArray();
        $wordpressProducts = [];

        if (!empty($productIds)) {
            try {
                $wordpressProducts = collect($this->wordPressService->getProductsByIds($productIds))
                    ->keyBy('id');
            } catch (\Exception $e) {
                // Echec silencieux
            }
        }

        // 8. Fusionner les données produits AVEC LES STATS DE VENTES
        $products = $allProducts->map(function ($product) use ($wordpressProducts, $productSalesStats) {
            $wpProduct = $wordpressProducts->get($product->wp_product_id);
            $salesStats = $productSalesStats->get($product->wp_product_id);

            return [
                'id' => $product->wp_product_id,
                'real_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku ?? ($wpProduct['sku'] ?? 'N/A'),
                'brand_slug' => $product->brand_slug,
                'price' => $product->price,

                // Stats de ventes (0 si aucune vente)
                'total_quantity' => $salesStats ? $salesStats->total_quantity : 0,
                'total_sales' => $salesStats ? $salesStats->total_sales : 0,
                'order_count' => $salesStats ? $salesStats->order_count : 0,
                'average_price_per_unit' => $salesStats && $salesStats->total_quantity > 0
                    ? $salesStats->total_sales / $salesStats->total_quantity
                    : 0,
                'has_sales' => $salesStats ? true : false,

                // Données WordPress
                'wordpress_data' => $wpProduct,
            ];
        });

        // Trier par CA ou quantité si demandé (après avoir fusionné les données)
        if (isset($sortBySales) && $sortBySales) {
            $products = $products->sortBy('total_sales', SORT_REGULAR, $sortDirection === 'desc');
        } elseif (isset($sortByQuantity) && $sortByQuantity) {
            $products = $products->sortBy('total_quantity', SORT_REGULAR, $sortDirection === 'desc');
        }

        // 9. Calcul des totaux (COMMANDES COMPLÉTÉES UNIQUEMENT)
        $summary = [
            'total_brands' => count($allBrands),
            'total_products' => Product::count(),
            'total_products_with_brand' => $productCountsByBrand->sum('product_count'),
            'total_products_without_brand' => Product::whereNull('brand_slug')->orWhere('brand_slug', '')->count(),
            'total_sales_all_brands' => $salesByBrand->sum('total_sales'),
            'total_quantity_all_brands' => $salesByBrand->sum('total_quantity'),
            'brands_with_sales' => $salesByBrand->where('total_quantity', '>', 0)->count(),
            'brands_without_sales' => count($allBrands) - $salesByBrand->where('total_quantity', '>', 0)->count(),
            'average_price_all_products' => $productCountsByBrand->avg('average_price'),
            'products_with_sales' => $productSalesStats->count(),
            'products_without_sales' => Product::count() - $productSalesStats->count(),
        ];

        return view('admin.products', [
            'products' => $products,
            'allProducts' => $allProducts,
            'brandStats' => $brandStats,
            'summary' => $summary,
            'allBrands' => $allBrands,
            'filters' => $filters,
        ]);
    }

    /**
     * Paramètres
     */
    public function settings()
    {
        // Derniers logs de synchronisation
        $syncLogs = SyncLog::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.settings', [
            'wordpressUrl' => config('services.wordpress.url'),
            'webhookConfigured' => !empty(config('services.wordpress.webhook_secret')),
            'syncLogs' => $syncLogs,
        ]);
    }

    /**
     * Mise à jour des paramètres
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'wordpress_url' => 'required|url',
            'wordpress_api_token' => 'required|string',
            'wordpress_webhook_secret' => 'required|string',
        ]);

        // Mettre à jour le fichier .env
        $envPath = base_path('.env');

        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);

            // Remplacer les valeurs
            $envContent = preg_replace([
                '/WORDPRESS_URL=.*/',
                '/WORDPRESS_API_TOKEN=.*/',
                '/WORDPRESS_WEBHOOK_SECRET=.*/',
            ], [
                'WORDPRESS_URL=' . $validated['wordpress_url'],
                'WORDPRESS_API_TOKEN=' . $validated['wordpress_api_token'],
                'WORDPRESS_WEBHOOK_SECRET=' . $validated['wordpress_webhook_secret'],
            ], $envContent);

            file_put_contents($envPath, $envContent);
        }

        // Recharger la configuration
        \Artisan::call('config:clear');

        // Invalider tous les caches liés à WordPress
        Cache::forget('dashboard_wordpress_stats');

        return redirect()->route('admin.settings')
            ->with('success', 'Paramètres mis à jour avec succès');
    }

    /**
     * Test de la connexion WordPress
     */
    public function testWordPressConnection()
    {
        try {
            $connected = $this->wordPressService->testConnection();

            if ($connected) {
                // Récupérer des infos supplémentaires
                $stats = $this->wordPressService->getGlobalStats('day');

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion WordPress API réussie',
                    'data' => [
                        'version' => 'WooCommerce API v2',
                        'url' => config('services.wordpress.url'),
                        'stats' => $stats['data'] ?? [],
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Impossible de se connecter à WordPress'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtenir les dernières commandes (pour AJAX)
     */
    public function recentOrdersApi()
    {
        $orders = Order::with(['creators', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'wp_order_id' => $order->wp_order_id,
                    'customer_name' => $order->customer_name,
                    'total' => $order->total,
                    'status' => $order->status,
                    'date' => $order->order_date?->format('d/m/Y H:i'),
                    'creators' => $order->creators->pluck('name')->implode(', '),
                    'items_count' => $order->items->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * API: Statistiques de synchronisation
     */
    public function syncStats()
    {
        $stats = [
            'total_orders' => Order::count(),
            'today_orders' => Order::whereDate('order_date', today())->count(),
            'total_sales' => Order::sum('total'),
            'last_sync' => SyncLog::where('sync_type', 'orders')
                ->where('status', 'success')
                ->latest()
                ->first()?->completed_at?->diffForHumans() ?? 'Jamais',
            'pending_syncs' => SyncLog::where('status', 'pending')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Logs de synchronisation détaillés
     */
    public function syncLogs(Request $request)
    {
        $logs = SyncLog::orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.sync.logs', [
            'logs' => $logs,
        ]);
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,refunded',
        ]);

        $oldStatus = $order->status;
        $order->status = $validated['status'];
        $order->save();

        // Log l'action
        \Log::info('Order status updated', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $order->status,
            'user_id' => Auth::id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour',
                'order' => $order,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Statut de la commande mis à jour');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReportController extends Controller
{
    /**
     * Rapport : ventes par produit
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->join('order_items', 'order_items.wp_product_id', '=', 'products.wp_product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', ['completed', 'processing'])
            ->where('orders.payment_status', 'paid')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.brand_slug',
                'products.stock_quantity',
                'products.image_url',
                'products.price',

                // Quantité totale vendue
                DB::raw('SUM(order_items.quantity) as total_quantity'),

                // Chiffre d'affaires
                DB::raw('SUM(order_items.total) as total_sales'),

                // Nombre de commandes distinctes
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),

                // Prix moyen par unité
                DB::raw('AVG(order_items.unit_price) as average_unit_price')
            )
            ->groupBy(
                'products.id',
                'products.name',
                'products.sku',
                'products.brand_slug',
                'products.stock_quantity',
                'products.image_url',
                'products.price'
            )
            ->orderByDesc('total_sales');

        // Filtres optionnels
        if ($request->has('brand') && $request->brand != '') {
            $query->where('products.brand_slug', $request->brand);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('orders.order_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('orders.order_date', '<=', $request->date_to);
        }

        // Filtres de période prédéfinie
        if ($request->has('period') && $request->period != 'all' && $request->period != 'custom') {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('orders.order_date', today());
                    break;
                case 'week':
                    $query->whereBetween('orders.order_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('orders.order_date', now()->month)
                        ->whereYear('orders.order_date', now()->year);
                    break;
                case 'quarter':
                    $quarter = ceil(now()->month / 3);
                    $startMonth = ($quarter - 1) * 3 + 1;
                    $query->whereMonth('orders.order_date', '>=', $startMonth)
                        ->whereMonth('orders.order_date', '<=', $startMonth + 2)
                        ->whereYear('orders.order_date', now()->year);
                    break;
                case 'year':
                    $query->whereYear('orders.order_date', now()->year);
                    break;
            }
        }

        // Filtre stock
        if ($request->has('stock_status') && $request->stock_status != '') {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('products.stock_quantity', '>', 10);
                    break;
                case 'low_stock':
                    $query->whereBetween('products.stock_quantity', [1, 10]);
                    break;
                case 'out_of_stock':
                    $query->where('products.stock_quantity', '<=', 0);
                    break;
            }
        }

        // Tri
        if ($request->has('sort_by')) {
            $query->reorder();

            switch ($request->sort_by) {
                case 'sales_desc':
                    $query->orderByDesc('total_sales');
                    break;
                case 'sales_asc':
                    $query->orderBy('total_sales');
                    break;
                case 'quantity_desc':
                    $query->orderByDesc('total_quantity');
                    break;
                case 'quantity_asc':
                    $query->orderBy('total_quantity');
                    break;
                case 'name_asc':
                    $query->orderBy('products.name');
                    break;
                case 'name_desc':
                    $query->orderByDesc('products.name');
                    break;
            }
        }

        // Pagination ou tout afficher
        $limit = $request->get('limit', 50);

        if ($limit == 0 || $limit === '0') {
            // Si "Tous" est sélectionné, ne pas paginer
            $products = $query->get();
        } else {
            // Utiliser la pagination
            $products = $query->paginate($limit)->appends($request->except('page'));
        }

        // Récupérer toutes les marques disponibles
        $brands = Product::select('brand_slug')
            ->whereNotNull('brand_slug')
            ->where('brand_slug', '!=', '')
            ->distinct()
            ->orderBy('brand_slug')
            ->pluck('brand_slug');

        if ($limit > 0 && $limit !== '0') {

            $allProducts = Product::query()
                ->join('order_items', 'order_items.wp_product_id', '=', 'products.wp_product_id')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereIn('orders.status', ['completed', 'processing'])
                ->where('orders.payment_status', 'paid')
                ->select(
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.total) as total_sales'),
                    DB::raw('AVG(order_items.unit_price) as average_unit_price')
                )
                ->first();

            $summary = [
                'total_sales' => $allProducts->total_sales ?? 0,
                'total_quantity' => $allProducts->total_quantity ?? 0,
                'average_price' => $allProducts->average_unit_price ?? 0,
                'products_count' => $products->total(),
                'no_sales_count' => Product::whereNotIn(
                    'wp_product_id',
                    OrderItem::select('wp_product_id')->distinct()->pluck('wp_product_id')
                )->count(),
            ];
        } else {
            $summary = [
                'total_sales' => $products->sum('total_sales'),
                'total_quantity' => $products->sum('total_quantity'),
                'average_price' => $products->avg('average_unit_price'),
                'products_count' => $products->count(),
                'no_sales_count' => Product::whereNotIn(
                    'wp_product_id',
                    OrderItem::select('wp_product_id')->distinct()->pluck('wp_product_id')
                )->count(),
            ];
        }
        return view('admin.products.sales', compact('products', 'brands', 'summary'));
    }

    /**
     * Rapport détaillé pour un produit spécifique
     */
    public function show($productId)
    {
        $product = Product::findOrFail($productId);

        $salesData = OrderItem::query()
            ->where('wp_product_id', $product->wp_product_id)
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->whereIn('orders.status', ['completed', 'processing'])
            ->select(
                DB::raw('DATE(orders.order_date) as sale_date'),
                DB::raw('SUM(order_items.quantity) as daily_quantity'),
                DB::raw('SUM(order_items.total) as daily_sales')
            )
            ->groupBy(DB::raw('DATE(orders.order_date)'))
            ->orderByDesc('sale_date')
            ->limit(30)
            ->get();

        $recentOrders = Order::query()
            ->whereHas('items', function ($query) use ($product) {
                $query->where('wp_product_id', $product->wp_product_id);
            })
            ->where('payment_status', 'paid')
            ->whereIn('status', ['completed', 'processing'])
            ->with(['items' => function ($query) use ($product) {
                $query->where('wp_product_id', $product->wp_product_id);
            }])
            ->orderByDesc('order_date')
            ->limit(10)
            ->get();

        return view('admin.products.sales-detail', compact('product', 'salesData', 'recentOrders'));
    }

    /**
     * Rapport : meilleurs produits vendeurs
     */
    public function bestSellers(Request $request)
    {
        $period = $request->get('period', 'month');

        $query = Product::query()
            ->join('order_items', 'order_items.wp_product_id', '=', 'products.wp_product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->whereIn('orders.status', ['completed', 'processing'])
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.brand_slug',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total) as total_sales'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.brand_slug')
            ->orderByDesc('total_quantity');

        // Filtre par période
        switch ($period) {
            case 'day':
                $query->whereDate('orders.order_date', today());
                break;
            case 'week':
                $query->whereBetween('orders.order_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('orders.order_date', now()->month)
                    ->whereYear('orders.order_date', now()->year);
                break;
            case 'year':
                $query->whereYear('orders.order_date', now()->year);
                break;
        }

        $bestSellers = $query->limit(20)->get();

        return view('admin.products.best-sellers', compact('bestSellers', 'period'));
    }

    /**
     * Rapport : produits à faible stock
     */
    public function lowStock()
    {
        $lowStockProducts = Product::lowStock()->get();

        $salesData = OrderItem::query()
            ->whereIn('wp_product_id', $lowStockProducts->pluck('wp_product_id'))
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->whereIn('orders.status', ['completed', 'processing'])
            ->where('orders.order_date', '>=', now()->subDays(30))
            ->select(
                'wp_product_id',
                DB::raw('SUM(order_items.quantity) as monthly_sales')
            )
            ->groupBy('wp_product_id')
            ->get()
            ->keyBy('wp_product_id');

        return view('admin.products.low-stock', compact('lowStockProducts', 'salesData'));
    }

    /**
     * Afficher les produits d'une marque spécifique
     */
    public function byBrand(Request $request, $brandSlug)
    {
        // Validation du brand_slug
        if (empty($brandSlug)) {
            return redirect()->route('admin.products')
                ->with('error', 'Marque non spécifiée');
        }

        // Récupérer les produits de la marque avec pagination
        $products = Product::where('brand_slug', $brandSlug)
            ->orderBy('name')
            ->paginate(20);

        // Statistiques de la marque
        $brandStats = Product::select([
            DB::raw('COUNT(*) as product_count'),
            DB::raw('SUM(stock_quantity) as total_stock'),
            DB::raw('AVG(price) as average_price'),
            DB::raw('SUM(CASE WHEN stock_quantity > 0 THEN 1 ELSE 0 END) as in_stock_count'),
            DB::raw('SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock_count'),
            DB::raw('SUM(CASE WHEN stock_quantity > 0 AND stock_quantity < 20 THEN 1 ELSE 0 END) as low_stock_count'),
        ])
            ->where('brand_slug', $brandSlug)
            ->first();

        // Statistiques de ventes de la marque
        $salesStats = OrderItem::select([
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total) as total_sales'),
            DB::raw('COUNT(DISTINCT order_id) as order_count'),
            DB::raw('COUNT(DISTINCT wp_product_id) as products_sold_count'),
        ])
            ->where('brand_slug', $brandSlug)
            ->first();

        // Top 5 des produits les plus vendus de cette marque
        $topProducts = OrderItem::select([
            'wp_product_id',
            'product_name',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total) as total_sales'),
            DB::raw('COUNT(DISTINCT order_id) as order_count'),
        ])
            ->where('brand_slug', $brandSlug)
            ->groupBy('wp_product_id', 'product_name')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();

        return view('admin.products.by-brand', [
            'brandSlug' => $brandSlug,
            'products' => $products,
            'brandStats' => $brandStats,
            'salesStats' => $salesStats,
            'topProducts' => $topProducts,
        ]);
    }

    /**
     * API pour récupérer les produits d'une marque (pour AJAX)
     */
    public function getBrandProductsAjax(Request $request, $brandSlug)
    {
        $products = Product::where('brand_slug', $brandSlug)
            ->select([
                'id',
                'wp_product_id',
                'name',
                'sku',
                'price',
                'stock_quantity',
                'brand_slug',
            ])
            ->orderBy('name')
            ->get();

        // Statistiques
        $stats = [
            'total_products' => $products->count(),
            'total_stock' => $products->sum('stock_quantity'),
            'stock_value' => $products->sum(function ($product) {
                return $product->price * $product->stock_quantity;
            }),
            'in_stock' => $products->where('stock_quantity', '>', 0)->count(),
            'out_of_stock' => $products->where('stock_quantity', '<=', 0)->count(),
            'low_stock' => $products->where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<', 20)->count(),
        ];

        return response()->json([
            'success' => true,
            'brand_slug' => $brandSlug,
            'products' => $products,
            'stats' => $stats,
        ]);
    }

    /**
     * Afficher les détails d'un produit
     */
    public function showProduct(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $orderClosed = OrderItem::select([
        DB::raw('SUM(order_items.quantity) as completed_quantity'),
        DB::raw('SUM(order_items.total) as completed_sales'),
        DB::raw('COUNT(DISTINCT orders.id) as completed_order_count'),
    ])
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('order_items.wp_product_id', $product->wp_product_id)
        ->where('orders.status', 'completed')
        ->first();

        // Statistiques de ventes du produit
         $salesStats = OrderItem::select([
        DB::raw('SUM(order_items.quantity) as total_quantity'),
        DB::raw('SUM(order_items.total) as total_sales'),
        DB::raw('COUNT(DISTINCT orders.id) as order_count'),
        DB::raw('AVG(order_items.total) as average_price'),
    ])
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('order_items.wp_product_id', $product->wp_product_id)
        ->whereIn('orders.status', ['processing', 'completed'])
        ->first();
        
        // Historique des ventes (dernières 10 ventes)
        $recentSales = OrderItem::where('wp_product_id', $product->wp_product_id)
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('admin.products.show', [
            'product' => $product,
            'salesStats' => $salesStats,
            'recentSales' => $recentSales,
            'orderClosed' => $orderClosed,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\CustomerSegmentController;

class CustomerController extends Controller
{

    public function index(Request $request)
    {
        // Requête de base pour les clients (groupés par email)
        $query = Order::select(
            'customer_email',
            'customer_name',
            'customer_phone',
            DB::raw('COUNT(DISTINCT orders.id) as order_count'),
            DB::raw('COUNT(DISTINCT CASE WHEN payment_status = "paid" THEN orders.id END) as paid_order_count'),
            DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as total_spent'),
            DB::raw('AVG(CASE WHEN payment_status = "paid" THEN total ELSE NULL END) as average_order'),
            DB::raw('MIN(order_date) as first_order_date'),
            DB::raw('MAX(order_date) as last_order_date'),
            // ✅ AJOUT : Commandes validées (completed)
            DB::raw('COUNT(DISTINCT CASE WHEN status = "completed" THEN orders.id END) as completed_order_count')
        )
            ->whereNotNull('customer_email')
            ->groupBy('customer_email', 'customer_name', 'customer_phone');

        // ===== FILTRE PAR NOMBRE DE COMMANDES =====
        if ($request->filled('orders_filter')) {
            switch ($request->orders_filter) {
                case '1-2':
                    $query->havingRaw('COUNT(DISTINCT orders.id) BETWEEN 1 AND 2');
                    break;
                case '3-5':
                    $query->havingRaw('COUNT(DISTINCT orders.id) BETWEEN 3 AND 5');
                    break;
                case '5+':
                    $query->havingRaw('COUNT(DISTINCT orders.id) > 5');
                    break;
            }
        }

        // ===== RECHERCHE =====
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // ===== TRI PAR COLONNES =====
        $sortField = $request->input('sort', 'last_order_date');
        $sortDirection = $request->input('direction', 'desc');

        switch ($sortField) {
            case 'name':
                $query->orderBy('customer_name', $sortDirection);
                break;
            case 'email':
                $query->orderBy('customer_email', $sortDirection);
                break;
            case 'order_count':
                $query->orderBy('order_count', $sortDirection);
                break;
            case 'total_spent':
                $query->orderBy('total_spent', $sortDirection);
                break;
            case 'average_order':
                $query->orderBy('average_order', $sortDirection);
                break;
            case 'first_order_date':
                $query->orderBy('first_order_date', $sortDirection);
                break;
            case 'last_order_date':
            default:
                $query->orderBy('last_order_date', $sortDirection);
                break;
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $customers = $query->paginate($perPage)->appends($request->except('page'));

        // Convertir les dates en objets Carbon après la pagination
        $customers->getCollection()->transform(function ($customer) {
            if ($customer->first_order_date) {
                $customer->first_order_date = \Carbon\Carbon::parse($customer->first_order_date);
            }
            if ($customer->last_order_date) {
                $customer->last_order_date = \Carbon\Carbon::parse($customer->last_order_date);
            }
            return $customer;
        });

        // ===== STATISTIQUES GLOBALES (CORRIGÉES) =====
        $totalCustomers = Order::select('customer_email')
            ->whereNotNull('customer_email')
            ->distinct()
            ->get()
            ->count();

        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');

        $averageOrderValue = Order::where('payment_status', 'paid')->avg('total') ?? 0;

        $totalOrders = Order::count();
        $paidOrders = Order::where('payment_status', 'paid')->count();
        $completedOrders = Order::where('status', 'completed')->count(); // ✅ AJOUT
        $averageOrdersPerCustomer = $totalCustomers > 0 ? $totalOrders / $totalCustomers : 0;

        // ===== QUANTITÉS DE PRODUITS =====
        // Total quantité commandée (toutes commandes)
        $totalProductsOrdered = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->sum('order_items.quantity');

        // Total quantité validée (commandes payées uniquement)
        $totalProductsValidated = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->sum('order_items.quantity');

        // Compteurs par filtre de commandes
        $orderRanges = [
            '1-2' => Order::select('customer_email')
                ->whereNotNull('customer_email')
                ->groupBy('customer_email')
                ->havingRaw('COUNT(DISTINCT id) BETWEEN 1 AND 2')
                ->get()
                ->count(),
            '3-5' => Order::select('customer_email')
                ->whereNotNull('customer_email')
                ->groupBy('customer_email')
                ->havingRaw('COUNT(DISTINCT id) BETWEEN 3 AND 5')
                ->get()
                ->count(),
            '5+' => Order::select('customer_email')
                ->whereNotNull('customer_email')
                ->groupBy('customer_email')
                ->havingRaw('COUNT(DISTINCT id) > 5')
                ->get()
                ->count(),
        ];

         $segments = app(CustomerSegmentController::class)->getPublicSegments();

        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers',
            'totalRevenue',
            'averageOrderValue',
            'averageOrdersPerCustomer',
            'totalOrders',
            'paidOrders',
            'completedOrders',
            'totalProductsOrdered',
            'totalProductsValidated',
            'orderRanges',
            'segments'
        ));
    }

    /**
     * Afficher la fiche client
     */
    public function show(Request $request, $customerIdentifier)
    {
        /**
         * 1. Identifiant client
         * → md5(email en minuscule)
         */
        $identifier = strtolower($customerIdentifier);

        /**
         * 2. Récupération des commandes
         */
        $query = Order::query()
            ->where(function ($q) use ($identifier) {
                $q->whereRaw(
                    "MD5(LOWER(customer_email)) = ?",
                    [$identifier]
                )
                    ->orWhere('customer_phone', $identifier)
                    ->orWhere('id', $identifier);
            })
            ->with(['items', 'creators'])
            ->orderBy('order_date', 'desc');

        /**
         * 3. Filtre par statut
         */
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        /**
         * 4. Pagination
         */
        $orders = $query->paginate(10);

        if ($orders->isEmpty()) {
            abort(404, 'Client non trouvé');
        }

        /**
         * 5. Commande de référence
         */
        $firstOrder = $orders->first();
        $emailHash = md5(strtolower($firstOrder->customer_email));

        /**
         * 6. Statistiques client
         */
        $customerStats = [
            'customer_name'   => $firstOrder->customer_name,
            'customer_email'  => $firstOrder->customer_email,
            'customer_phone'  => $firstOrder->customer_phone,
            'last_shipping_address' => $firstOrder->shipping_address,

            'order_count' => Order::whereRaw(
                "MD5(LOWER(customer_email)) = ?",
                [$emailHash]
            )->count(),

            'total_spent' => Order::whereRaw(
                "MD5(LOWER(customer_email)) = ?",
                [$emailHash]
            )->where('status', 'completed')->sum('total'),

            'average_order' => Order::whereRaw(
                "MD5(LOWER(customer_email)) = ?",
                [$emailHash]
            )->where('status', 'completed')->avg('total') ?? 0,

            'completed_orders_count' => Order::whereRaw(
                "MD5(LOWER(customer_email)) = ?",
                [$emailHash]
            )->where('status', 'completed')->count(),

            'processing_orders_count' => Order::whereRaw(
                "MD5(LOWER(customer_email)) = ?",
                [$emailHash]
            )->where('status', 'processing')->count(),

            'pending_orders_count' => Order::whereRaw(
                "MD5(LOWER(customer_email)) = ?",
                [$emailHash]
            )->where('status', 'pending')->count(),

            'days_since_last_order' => now()->diffInDays(
                Order::whereRaw(
                    "MD5(LOWER(customer_email)) = ?",
                    [$emailHash]
                )->latest('order_date')->value('order_date') ?? now()
            ),
        ];

        /**
         * 7. Marques favorites
         */
        $favoriteBrands = Order::whereRaw(
            "MD5(LOWER(orders.customer_email)) = ?",
            [$emailHash]
        )
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->whereNotNull('order_items.brand_slug')
            ->select('order_items.brand_slug')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('order_items.brand_slug')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'brand_slug')
            ->toArray();

        /**
         * 8. Vue
         */
        return view('admin.customers.show', [
            'customer' => (object) array_merge($customerStats, [
                'orders' => $orders,
                'favorite_brands' => $favoriteBrands,
            ]),
        ]);
    }



    /**
     * Rechercher un client
     */
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2',
        ]);

        $search = $request->input('search');

        // Rechercher par email, téléphone ou nom
        $customers = Order::where('customer_email', 'like', "%{$search}%")
            ->orWhere('customer_phone', 'like', "%{$search}%")
            ->orWhere('customer_name', 'like', "%{$search}%")
            ->select('customer_email', 'customer_name', 'customer_phone')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('SUM(total) as total_spent')
            ->groupBy('customer_email', 'customer_name', 'customer_phone')
            ->orderByDesc('order_count')
            ->limit(20)
            ->get();

        return view('admin.customers.search', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    /**
     * Exporter les clients en CSV
     */
    public function export(Request $request)
    {
        $customers = Order::select(
            'customer_email',
            'customer_name',
            'customer_phone',
            DB::raw('COUNT(DISTINCT orders.id) as order_count'),
            DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as total_spent'),
            DB::raw('AVG(CASE WHEN payment_status = "paid" THEN total ELSE NULL END) as average_order'),
            DB::raw('MIN(order_date) as first_order_date'),
            DB::raw('MAX(order_date) as last_order_date')
        )
            ->whereNotNull('customer_email')
            ->groupBy('customer_email', 'customer_name', 'customer_phone')
            ->orderByDesc('total_spent')
            ->get();

        $filename = 'customers_export_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // En-têtes
            fputcsv($file, [
                'Nom',
                'Email',
                'Téléphone',
                'Nombre de commandes',
                'CA Total (FCFA)',
                'Panier Moyen (FCFA)',
                'Première commande',
                'Dernière commande',
                'Jours depuis dernière commande'
            ], ';');

            // Données
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->customer_name,
                    $customer->customer_email,
                    $customer->customer_phone ?? '',
                    $customer->order_count,
                    number_format($customer->total_spent, 0, ',', ''),
                    number_format($customer->average_order ?? 0, 0, ',', ''),
                    $customer->first_order_date ? \Carbon\Carbon::parse($customer->first_order_date)->format('d/m/Y') : '',
                    $customer->last_order_date ? \Carbon\Carbon::parse($customer->last_order_date)->format('d/m/Y') : '',
                    $customer->last_order_date ? now()->diffInDays(\Carbon\Carbon::parse($customer->last_order_date)) : '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

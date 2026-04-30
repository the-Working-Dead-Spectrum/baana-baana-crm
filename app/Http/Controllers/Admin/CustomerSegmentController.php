<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CustomerSegmentController extends Controller
{
    /**
     * Afficher la page de segmentation
     */
    public function index(Request $request)
    {
        // Récupérer les segments prédéfinis
        $segments = $this->getPredefinedSegments();

        // Appliquer le filtre si un segment est sélectionné
        $selectedSegment = $request->get('segment', 'all');
        $customers = $this->getCustomersBySegment($selectedSegment);
        return view('admin.segments.index', compact('segments', 'customers', 'selectedSegment'));
    }

    /**
     * Segments prédéfinis
     */
    private function getPredefinedSegments()
    {
        return [
            'all' => [
                'name' => 'Tous les clients',
                'description' => 'Tous les clients de la plateforme',
                'icon' => 'users',
                'color' => 'gray',
                'count' => $this->getAllCustomersCount()
            ],
            'vip' => [
                'name' => 'Clients VIP',
                'description' => 'Clients avec 5+ commandes ou 100 000+ FCFA dépensés',
                'icon' => 'star',
                'color' => 'yellow',
                'count' => $this->getVipCustomersCount()
            ],
            'new' => [
                'name' => 'Nouveaux clients',
                'description' => 'Première commande dans les 30 derniers jours',
                'icon' => 'user-plus',
                'color' => 'green',
                'count' => $this->getNewCustomersCount()
            ],
            'inactive' => [
                'name' => 'Clients inactifs',
                'description' => 'Aucun achat depuis 90+ jours',
                'icon' => 'user-x',
                'color' => 'red',
                'count' => $this->getInactiveCustomersCount()
            ],
            'high_value' => [
                'name' => 'Gros paniers',
                'description' => 'Panier moyen > 50 000 FCFA',
                'icon' => 'shopping-bag',
                'color' => 'purple',
                'count' => $this->getHighValueCustomersCount()
            ],
            'pending_payment' => [
                'name' => 'Paiements en attente',
                'description' => 'Clients avec commandes non payées',
                'icon' => 'clock',
                'color' => 'orange',
                'count' => $this->getPendingPaymentCustomersCount()
            ]
        ];
    }

    /**
     * Récupérer les clients par segment
     */
    private function getCustomersBySegment($segment)
    {
        $baseQuery = DB::table('orders')
            ->select(
                'customer_email',
                'customer_name',
                'customer_phone',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) as total_spent'),
                DB::raw('AVG(CASE WHEN status = "completed" THEN total ELSE NULL END) as average_order'),
                DB::raw('MAX(order_date) as last_order_date'),
                DB::raw('MIN(order_date) as first_order_date')
            )
            ->groupBy('customer_email', 'customer_name', 'customer_phone');

        switch ($segment) {
            case 'vip':
                $baseQuery->havingRaw('COUNT(DISTINCT orders.id) >= 5 OR SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) >= 100000');
                break;

            case 'new':
                $baseQuery->havingRaw('MIN(order_date) >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
                break;

            case 'inactive':
                $baseQuery->havingRaw('MAX(order_date) <= DATE_SUB(NOW(), INTERVAL 90 DAY)');
                break;

            case 'high_value':
                $baseQuery->havingRaw('AVG(CASE WHEN status = "completed" THEN total ELSE NULL END) > 50000');
                break;

            case 'pending_payment':
                $baseQuery->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('orders as o2')
                        ->whereColumn('o2.customer_email', 'orders.customer_email')
                        ->where('o2.status', '!=', 'completed');
                });
                break;

            case 'all':
            default:
                // Pas de filtre supplémentaire
                break;
        }

        return $baseQuery->paginate(20);
    }

    /**
     * Méthodes de comptage pour chaque segment
     */
    private function getAllCustomersCount()
    {
        return DB::table('orders')
            ->distinct('customer_email')
            ->count('customer_email');
    }

    private function getVipCustomersCount()
    {
        return DB::table('orders')
            ->select('customer_email')
            ->groupBy('customer_email')
            ->havingRaw('COUNT(DISTINCT id) >= 5 OR SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) >= 100000')
            ->get()
            ->count();
    }

    private function getNewCustomersCount()
    {
        return DB::table('orders')
            ->select('customer_email')
            ->groupBy('customer_email')
            ->havingRaw('MIN(order_date) >= DATE_SUB(NOW(), INTERVAL 30 DAY)')
            ->get()
            ->count();
    }

    private function getInactiveCustomersCount()
    {
        return DB::table('orders')
            ->select('customer_email')
            ->groupBy('customer_email')
            ->havingRaw('MAX(order_date) <= DATE_SUB(NOW(), INTERVAL 90 DAY)')
            ->get()
            ->count();
    }

    private function getHighValueCustomersCount()
    {
        return DB::table('orders')
            ->select('customer_email')
            ->groupBy('customer_email')
            ->havingRaw('AVG(CASE WHEN status = "completed" THEN total ELSE NULL END) > 50000')
            ->get()
            ->count();
    }

    private function getPendingPaymentCustomersCount()
    {
        return DB::table('orders')
            ->where('status', '!=', 'completed')
            ->distinct('customer_email')
            ->count('customer_email');
    }

    /**
     * Exporter un segment en CSV
     */
    public function export(Request $request, $segment)
    {
        $customers = $this->getCustomersBySegment($segment);

        $filename = "segment_{$segment}_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'Email',
                'Nom',
                'Téléphone',
                'Nombre de commandes',
                'Total dépensé (FCFA)',
                'Panier moyen (FCFA)',
                'Dernière commande',
                'Première commande'
            ]);

            // Données
            foreach ($customers->items() as $customer) {
                fputcsv($file, [
                    $customer->customer_email,
                    $customer->customer_name,
                    $customer->customer_phone,
                    $customer->order_count,
                    number_format($customer->total_spent, 0, ',', ' '),
                    number_format($customer->average_order ?? 0, 0, ',', ' '),
                    $customer->last_order_date,
                    $customer->first_order_date
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Filtrage avancé avec critères personnalisés
     */
    public function customFilter(Request $request)
    {
        $query = DB::table('orders')
            ->select(
                'customer_email',
                'customer_name',
                'customer_phone',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) as total_spent'),
                DB::raw('AVG(CASE WHEN status = "completed" THEN total ELSE NULL END) as average_order'),
                DB::raw('MAX(order_date) as last_order_date')
            )
            ->groupBy('customer_email', 'customer_name', 'customer_phone');

        // Filtre par nombre de commandes
        if ($request->filled('min_orders')) {
            $query->havingRaw('COUNT(DISTINCT orders.id) >= ?', [$request->min_orders]);
        }
        if ($request->filled('max_orders')) {
            $query->havingRaw('COUNT(DISTINCT orders.id) <= ?', [$request->max_orders]);
        }

        // Filtre par montant dépensé
        if ($request->filled('min_spent')) {
            $query->havingRaw('SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) >= ?', [$request->min_spent]);
        }
        if ($request->filled('max_spent')) {
            $query->havingRaw('SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) <= ?', [$request->max_spent]);
        }

        // Filtre par période de dernière commande
        if ($request->filled('last_order_from')) {
            $query->havingRaw('MAX(order_date) >= ?', [$request->last_order_from]);
        }
        if ($request->filled('last_order_to')) {
            $query->havingRaw('MAX(order_date) <= ?', [$request->last_order_to]);
        }

        $customers = $query->paginate(20);

        return view('admin.segments.custom', compact('customers'));
    }

    public function getPublicSegments()
    {
        return $this->getPredefinedSegments();
    }
}

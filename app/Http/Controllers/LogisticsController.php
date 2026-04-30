<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class LogisticsController extends Controller
{
    protected PapsService $papsService;

    public function __construct(PapsService $papsService)
    {
        $this->papsService = $papsService;
    }

    /**
     * Tableau de bord logistique
     */
    public function dashboard()
    {
        // Commandes en attente de pickup
        $pendingPickups = Order::where('status', 'logistics')
            ->whereNull('paps_task_id')
            ->count();

        // Commandes avec tâche PAPS active
        $activePapsTasks = Order::whereNotNull('paps_task_id')
            ->whereIn('paps_status', ['to_pick', 'picked', 'in_transit'])
            ->count();

        // Commandes livrées aujourd'hui
        $deliveredToday = Order::whereDate('paps_delivered_at', today())->count();

        // Commandes en transit
        $inTransit = Order::where('paps_status', 'in_transit')->count();

        // Dernières commandes
        $recentOrders = Order::with(['creators'])
            ->whereNotNull('paps_task_id')
            ->orderBy('paps_picked_at', 'desc')
            ->limit(10)
            ->get();

        return view('logistics.dashboard', [
            'pendingPickups' => $pendingPickups,
            'activePapsTasks' => $activePapsTasks,
            'deliveredToday' => $deliveredToday,
            'inTransit' => $inTransit,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * Liste des commandes à expédier
     */
    public function orders(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'from' => $request->get('from'),
            'to' => $request->get('to'),
        ];

        $query = Order::query();

        if ($filters['status']) {
            if ($filters['status'] === 'pending_paps') {
                $query->where('status', 'logistics')->whereNull('paps_task_id');
            } elseif ($filters['status'] === 'with_paps') {
                $query->whereNotNull('paps_task_id');
            } else {
                $query->where('paps_status', $filters['status']);
            }
        }

        if ($filters['from']) {
            $query->whereDate('order_date', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->whereDate('order_date', '<=', $filters['to']);
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(20);

        return view('logistics.orders', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    /**
     * Détail d'une commande
     */
    public function showOrder(Order $order)
    {
        $papsTaskDetails = null;
        
        if ($order->hasPapsTask()) {
            try {
                $papsTaskDetails = $this->papsService->fetchTask($order->paps_task_id);
            } catch (Exception $e) {
                Log::error('Failed to fetch PAPS task details', [
                    'order_id' => $order->id,
                    'paps_task_id' => $order->paps_task_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('logistics.order-show', [
            'order' => $order,
            'papsTaskDetails' => $papsTaskDetails,
        ]);
    }

    /**
     * Créer une tâche de pickup PAPS pour une commande
     */
    public function createPickup(Request $request, Order $order)
    {
        $validated = $request->validate([
            'date_pickup' => 'required|date|after_or_equal:today',
            'time_pickup' => 'required|date_format:H:i',
            'vehicle_type' => 'required|in:SCOOTER,MINI_VAN,TRICYCLE,VAN,CAMION',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        try {
            // Récupérer l'adresse du créateur ou du warehouse
            $creator = $order->creators()->first();
            
            $pickupData = [
                'order_id' => $order->id,
                'creator_id' => $creator?->id,
                'brand_slug' => $creator?->brand_slug,
                'address' => $order->shipping_address ?? 'Adresse par défaut',
                'date_pickup' => $validated['date_pickup'],
                'time_pickup' => $validated['time_pickup'],
                'vehicle_type' => $validated['vehicle_type'],
                'contact_name' => $validated['contact_name'] ?? $order->customer_name,
                'contact_phone' => $validated['contact_phone'] ?? $order->customer_phone,
                'packages' => $this->preparePackagesData($order),
            ];

            $result = $this->papsService->createPickupTask($pickupData);

            if ($result && isset($result['data']['job'])) {
                $job = $result['data']['job'];
                
                // Mettre à jour la commande
                $order->update([
                    'paps_task_id' => $job['_id'],
                    'paps_status' => 'pending',
                    'paps_pickup_scheduled_at' => "{$job['job_date']} {$job['job_time']}",
                    'paps_metadata' => [
                        'job_type' => $job['job_type'],
                        'job_slot_start' => $job['job_slot_start'] ?? null,
                        'job_slot_end' => $job['job_slot_end'] ?? null,
                        'created_via' => 'crm',
                    ],
                    'paps_status_history' => [[
                        'action' => 'TASK_CREATED',
                        'date' => now()->toIso8601String(),
                        'job_id' => $job['_id'],
                    ]],
                ]);

                Log::info('PAPS pickup task created successfully', [
                    'order_id' => $order->id,
                    'paps_task_id' => $job['_id'],
                ]);

                return redirect()->route('logistics.orders.show', $order)
                    ->with('success', 'Tâche de pickup créée avec succès. ID: ' . $job['_id']);
            }

            return redirect()->route('logistics.orders.show', $order)
                ->with('error', 'Échec de la création de la tâche PAPS');

        } catch (Exception $e) {
            Log::error('Failed to create PAPS pickup task', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('logistics.orders.show', $order)
                ->with('error', 'Erreur lors de la création du pickup: ' . $e->getMessage());
        }
    }

    /**
     * Actualiser le statut d'une commande depuis PAPS
     */
    public function refreshStatus(Order $order)
    {
        if (!$order->hasPapsTask()) {
            return back()->with('error', 'Cette commande n\'a pas de tâche PAPS associée');
        }

        try {
            // Récupérer le statut depuis PAPS
            $statusData = $this->papsService->getOrderStatus($order->paps_order_uid ?? $order->paps_task_id);
            
            if ($statusData && isset($statusData['data'])) {
                $newStatus = $statusData['data']['status'] ?? null;
                
                if ($newStatus) {
                    $order->updatePapsStatus($newStatus, [
                        'action' => 'STATUS_UPDATED',
                        'old_status' => $order->paps_status,
                        'new_status' => $newStatus,
                    ]);

                    // Mettre à jour les timestamps selon le statut
                    $this->updateTimestampsFromStatus($order, $newStatus);
                }

                return back()->with('success', 'Statut actualisé avec succès');
            }

            return back()->with('error', 'Impossible de récupérer le statut PAPS');

        } catch (Exception $e) {
            Log::error('Failed to refresh PAPS status', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de l\'actualisation: ' . $e->getMessage());
        }
    }

    /**
     * Calculer les frais de livraison pour une commande
     */
    public function calculateFees(Request $request, Order $order)
    {
        $validated = $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'delivery_type' => 'nullable|in:STANDARD,EXPRESS',
            'size_details' => 'nullable|array',
        ]);

        try {
            $feesData = [
                'origin' => $validated['origin'],
                'destination' => $validated['destination'],
                'delivery_type' => $validated['delivery_type'] ?? config('services.paps.default_delivery_type'),
                'size_details' => $validated['size_details'] ?? $this->prepareSizeDetails($order),
            ];

            $result = $this->papsService->calculateDeliveryFees($feesData);

            if ($result && isset($result['data'])) {
                return response()->json([
                    'success' => true,
                    'price' => $result['data']['price'],
                    'package_size' => $result['data']['packageSize'] ?? null,
                    'distance' => $result['data']['distance'] ?? null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Impossible de calculer les frais',
            ], 400);

        } catch (Exception $e) {
            Log::error('Failed to calculate PAPS fees', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook pour recevoir les mises à jour de statut PAPS
     */
    public function handleWebhook(Request $request)
    {
        // Vérifier la signature/secret si configuré
        $secret = config('services.paps.webhook_secret');
        
        // TODO: Implémenter la vérification de signature
        
        $payload = $request->all();
        
        Log::info('PAPS Webhook received', ['payload' => $payload]);

        // Extraire les données pertinentes
        $orderId = $payload['order_id'] ?? $payload['metadata']['order_id'] ?? null;
        $newStatus = $payload['status'] ?? null;
        $taskId = $payload['task_id'] ?? null;

        if (!$orderId || !$newStatus) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // Trouver la commande
        $order = Order::where('paps_task_id', $taskId)
            ->orWhere('id', $orderId)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Mettre à jour le statut
        $order->updatePapsStatus($newStatus, [
            'action' => 'WEBHOOK_UPDATE',
            'old_status' => $order->paps_status,
            'new_status' => $newStatus,
            'source' => 'webhook',
        ]);

        // Mettre à jour les timestamps
        $this->updateTimestampsFromStatus($order, $newStatus);

        Log::info('PAPS Webhook processed successfully', [
            'order_id' => $order->id,
            'new_status' => $newStatus,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Mapper le statut PAPS vers le statut logistique interne
     */
    protected function mapPapsStatusToInternal(string $papsStatus): string
    {
        return match($papsStatus) {
            'to_pick', 'picked' => 'processing',
            'in_transit' => 'shipped',
            'delivered' => 'delivered',
            'failed', 'cancelled' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Mettre à jour les timestamps selon le statut
     */
    protected function updateTimestampsFromStatus(Order $order, string $status): void
    {
        $now = now();

        switch ($status) {
            case 'picked':
                $order->paps_picked_at = $now;
                break;
            case 'delivered':
                $order->paps_delivered_at = $now;
                $order->delivered_at = $now;
                $order->logistics_status = 'delivered';
                break;
            case 'in_transit':
                $order->logistics_status = 'shipped';
                break;
        }

        // Mapper le statut logistique
        $order->logistics_status = $this->mapPapsStatusToInternal($status);
        
        $order->save();
    }

    /**
     * Préparer les données des colis pour PAPS
     */
    protected function preparePackagesData(Order $order): array
    {
        $items = $order->items;
        
        // Estimer le poids et dimensions basés sur les items
        $totalWeight = $items->sum(function($item) {
            return ($item->metadata['weight'] ?? 0.5) * $item->quantity;
        });

        return [
            [
                'weight' => max($totalWeight, 0.5), // Minimum 0.5kg
                'length' => 30,
                'width' => 20,
                'height' => 15,
                'quantity' => 1,
            ],
        ];
    }

    /**
     * Préparer les détails de taille pour le calcul des frais
     */
    protected function prepareSizeDetails(Order $order): array
    {
        return $this->preparePackagesData($order);
    }
}

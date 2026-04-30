<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\OrderSyncService;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    protected OrderSyncService $orderSyncService;
    
    public function __construct(OrderSyncService $orderSyncService)
    {
        $this->orderSyncService = $orderSyncService;
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Vérifier si le statut a changé
        if ($order->wasChanged('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            Log::info("📦 Order status changed, triggering WordPress sync", [
                'order_id' => $order->id,
                'wp_order_id' => $order->wp_order_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Synchroniser vers WordPress
            $syncResult = $this->orderSyncService->syncOrderUpdateToWordPress($order, 'status_change');

            // Ajouter une note dans WordPress selon le nouveau statut
            if ($order->wp_order_id && $syncResult) {
                $this->addStatusChangeNote($order, $oldStatus, $newStatus);
            }
        }

        // Si d'autres champs importants ont changé
        $importantFields = ['customer_name', 'customer_email', 'customer_phone', 'notes', 'tracking_number'];
        
        if ($order->wasChanged($importantFields)) {
            Log::info("📝 Order details updated, triggering WordPress sync", [
                'order_id' => $order->id,
                'wp_order_id' => $order->wp_order_id,
                'changed_fields' => array_keys($order->getChanges())
            ]);

            $this->orderSyncService->syncOrderUpdateToWordPress($order, 'customer_update');
            
            // Note spéciale si le numéro de tracking a changé
            if ($order->wasChanged('tracking_number') && $order->wp_order_id) {
                $this->orderSyncService->addOrderNote(
                    $order->wp_order_id,
                    "📦 Numéro de suivi ajouté : {$order->tracking_number}",
                    true // Note visible par le client
                );
            }
        }
    }

    /**
     * Ajouter une note appropriée selon le changement de statut
     */
    protected function addStatusChangeNote(Order $order, string $oldStatus, string $newStatus): void
    {
        $notes = [
            'processing' => "💳 Paiement confirmé via le CRM. Commande prête pour expédition.",
            'shipped' => "📦 Commande expédiée depuis le CRM." . 
                        (!empty($order->tracking_number) ? " N° de suivi : {$order->tracking_number}" : ""),
            'completed' => "✅ Commande marquée comme terminée depuis le CRM.",
            'cancelled' => "❌ Commande annulée depuis le CRM.",
        ];

        $noteText = $notes[$newStatus] ?? "Statut mis à jour depuis le CRM : {$oldStatus} → {$newStatus}";

        // Note visible par le client uniquement pour shipped et completed
        $isCustomerNote = in_array($newStatus, ['shipped', 'completed']);

        $this->orderSyncService->addOrderNote(
            $order->wp_order_id,
            $noteText,
            $isCustomerNote
        );
    }

    /**
     * Handle the Order "deleting" event.
     */
    public function deleting(Order $order): void
    {
        // Synchroniser la suppression vers WordPress
        if ($order->wp_order_id) {
            Log::warning("🗑️ Order being deleted in CRM", [
                'order_id' => $order->id,
                'wp_order_id' => $order->wp_order_id
            ]);

            // Ajouter une note dans WordPress plutôt que de supprimer
            $this->orderSyncService->addOrderNote(
                $order->wp_order_id,
                "⚠️ Commande supprimée depuis le CRM le " . now()->format('d/m/Y à H:i'),
                false
            );
            
            // Optionnel : passer la commande WordPress en "cancelled"
            // $this->orderSyncService->updateOrderStatus($order->wp_order_id, 'cancelled');
        }
    }
}

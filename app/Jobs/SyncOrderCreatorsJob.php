<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\CreatorOrderSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncOrderCreatorsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    protected int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(CreatorOrderSyncService $syncService): void
    {
        Log::info("🚀 SyncOrderCreatorsJob pour commande #{$this->orderId}");

        try {
            $order = Order::with('items')->find($this->orderId);
            
            if (!$order) {
                Log::error("❌ Commande #{$this->orderId} non trouvée");
                $this->fail("Order not found");
                return;
            }

            // Utiliser votre service
            $result = $syncService->syncCreatorsForOrder($order);

            if ($result['success']) {
                Log::info("✅ Sync terminé: {$result['message']}", [
                    'order_id' => $this->orderId,
                    'creators_synced' => $result['creators_synced'] ?? 0
                ]);
            } else {
                Log::warning("⚠️ Sync partiel: {$result['message']}", [
                    'order_id' => $this->orderId
                ]);
            }

        } catch (\Exception $e) {
            Log::error("❌ Erreur sync commande #{$this->orderId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("💥 SyncOrderCreatorsJob failed permanently for order #{$this->orderId}", [
            'error' => $exception->getMessage()
        ]);
    }
}
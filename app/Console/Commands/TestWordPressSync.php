<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderSyncService;
use Illuminate\Console\Command;

class TestWordPressSync extends Command
{
    protected $signature = 'test:wordpress-sync {order_id}';
    protected $description = 'Test WordPress synchronization for an order';

    public function handle(OrderSyncService $syncService)
    {
        $orderId = $this->argument('order_id');
        $order = Order::find($orderId);

        if (!$order) {
            $this->error("Order #{$orderId} not found");
            return Command::FAILURE;
        }

        if (!$order->wp_order_id) {
            $this->error("Order #{$orderId} has no wp_order_id");
            return Command::FAILURE;
        }

        $this->info("🧪 Testing WordPress sync for Order #{$orderId}");
        $this->info("Current status: {$order->status}");
        $this->info("WordPress Order ID: {$order->wp_order_id}");
        $this->newLine();

        // Test de connexion
        $this->info("1️⃣ Testing connection...");
        if ($syncService->testConnection()) {
            $this->info("✅ Connection successful");
        } else {
            $this->error("❌ Connection failed");
            return Command::FAILURE;
        }

        // Test de synchronisation
        $this->newLine();
        $this->info("2️⃣ Syncing order status...");
        
        if ($syncService->syncOrderUpdateToWordPress($order, 'status_change')) {
            $this->info("✅ Sync successful");
        } else {
            $this->error("❌ Sync failed");
            return Command::FAILURE;
        }

        // Test d'ajout de note
        $this->newLine();
        $this->info("3️⃣ Adding test note...");
        
        if ($syncService->addOrderNote($order->wp_order_id, "Test de synchronisation depuis Laravel CRM", false)) {
            $this->info("✅ Note added successfully");
        } else {
            $this->error("❌ Failed to add note");
        }

        $this->newLine();
        $this->info("🎉 All tests completed!");

        return Command::SUCCESS;
    }
}
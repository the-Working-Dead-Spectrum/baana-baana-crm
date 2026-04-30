<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\CreatorOrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncCreatorOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'creators:sync-orders 
                            {--order-id= : Sync a specific order by ID}
                            {--status= : Sync only orders with this status}
                            {--force : Force resync even if already synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize creators for all orders based on their products';

    protected CreatorOrderService $creatorOrderService;

    public function __construct(CreatorOrderService $creatorOrderService)
    {
        parent::__construct();
        $this->creatorOrderService = $creatorOrderService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting creator-order synchronization...');
        
        // Si un order_id spécifique est fourni
        if ($orderId = $this->option('order-id')) {
            return $this->syncSingleOrder($orderId);
        }

        // Construire la requête
        $query = Order::query()->with('items');

        if ($status = $this->option('status')) {
            $query->where('status', $status);
            $this->info("Filtering by status: {$status}");
        }

        // Si pas de force, exclure les commandes déjà synchronisées
        if (!$this->option('force')) {
            $query->whereDoesntHave('creators');
            $this->info("Syncing only orders without creators (use --force to resync all)");
        } else {
            $this->warn("Force mode: Will resync ALL orders");
        }

        $orders = $query->orderBy('order_date', 'desc')->get();
        
        if ($orders->isEmpty()) {
            $this->warn('No orders to sync.');
            return Command::SUCCESS;
        }

        $this->info("Found {$orders->count()} orders to sync");
        
        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();

        $synced = 0;
        $failed = 0;
        $errors = [];

        foreach ($orders as $order) {
            try {
                $result = $this->creatorOrderService->syncCreatorsForOrder($order);
                
                if (!empty($result)) {
                    $synced++;
                } else {
                    $this->warn("\nOrder #{$order->id} has no creators (no brand_slug in items)");
                }
                
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Order #{$order->id}: " . $e->getMessage();
                $this->error("\nFailed to sync order #{$order->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Résumé
        $this->info("✅ Synchronization complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total orders processed', $orders->count()],
                ['Successfully synced', $synced],
                ['Failed', $failed],
                ['No creators found', $orders->count() - $synced - $failed],
            ]
        );

        if (!empty($errors)) {
            $this->warn('Errors encountered:');
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }

        // Statistiques finales
        $this->displayStats();

        return Command::SUCCESS;
    }

    protected function syncSingleOrder(int $orderId)
    {
        $this->info("Syncing single order #{$orderId}...");
        
        $order = Order::with('items')->find($orderId);
        
        if (!$order) {
            $this->error("Order #{$orderId} not found");
            return Command::FAILURE;
        }

        try {
            $result = $this->creatorOrderService->syncCreatorsForOrder($order);
            
            if (empty($result)) {
                $this->warn("Order #{$orderId} has no creators (no brand_slug in items)");
                return Command::SUCCESS;
            }

            $this->info("✅ Order #{$orderId} synced successfully with " . count($result) . " creator(s)");
            
            // Afficher les détails
            $this->table(
                ['Creator ID', 'Total (CFA)', 'Products', 'Quantity'],
                collect($result)->map(function ($data, $creatorId) {
                    return [
                        $creatorId,
                        number_format($data['creator_total'], 0, ',', ' '),
                        $data['product_count'],
                        $data['total_quantity'],
                    ];
                })->toArray()
            );

            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Failed to sync order #{$orderId}: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function displayStats()
    {
        $this->newLine();
        $this->info('📊 Global Statistics:');
        
        $stats = DB::table('creator_order')
            ->select(
                DB::raw('COUNT(DISTINCT order_id) as total_orders'),
                DB::raw('COUNT(DISTINCT creator_id) as total_creators'),
                DB::raw('SUM(creator_total) as total_amount'),
                DB::raw('SUM(total_quantity) as total_products')
            )
            ->first();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Orders with creators', $stats->total_orders ?? 0],
                ['Active creators', $stats->total_creators ?? 0],
                ['Total amount', number_format($stats->total_amount ?? 0, 0, ',', ' ') . ' CFA'],
                ['Total products sold', number_format($stats->total_products ?? 0, 0, ',', ' ')],
            ]
        );
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\SyncLog;
use App\Models\Order;
use App\Models\Creator;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Jobs\SyncOrdersJob;
use App\Jobs\SyncProductsJob;
use App\Jobs\SyncCreatorsJob;
use App\Services\WordPressService;
use App\Services\CreatorOrderSyncService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminSyncController extends Controller
{
    public function index()
    {
        $syncStats = [
            'total_orders' => Order::count(),
            'today_orders' => Order::whereDate('order_date', today())->count(),
            'total_creators' => Creator::count(),
            'total_products' => Product::count(),
            'last_sync' => SyncLog::where('status', 'success')
                ->latest('completed_at')
                ->first(),
            'queue_status' => $this->getQueueStatus(),
        ];

        return view('admin.sync.index', compact('syncStats'));
    }

    public function runSync(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:all,orders,products,creators',
            'force' => 'nullable|boolean',
            'use_queue' => 'nullable|boolean',
        ]);

        $type = $validated['type'];
        $force = $request->boolean('force', false);
        $useQueue = $request->boolean('use_queue', false);

        Log::info('Sync request received', [
            'type' => $type,
            'force' => $force,
            'use_queue' => $useQueue,
            'user_id' => auth()->id(),
        ]);

        try {
            // Si on n'utilise PAS la queue, on exécute directement
            if (!$useQueue) {
                return $this->runSyncDirectly($type, $force);
            }

            // Sinon on dispatch dans la queue
            return $this->dispatchToQueue($type, $force);

        } catch (\Exception $e) {
            Log::error('Sync failed', [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', '❌ Erreur lors de la synchronisation : ' . $e->getMessage());
        }
    }

    /**
     * Exécution directe (synchrone) des synchronisations
     */
    private function runSyncDirectly(string $type, bool $force)
    {
        set_time_limit(600); // 10 minutes max
        ini_set('memory_limit', '512M');

        Log::info('Running sync directly (no queue)', [
            'type' => $type,
            'force' => $force
        ]);

        try {
            $wordPressService = app(WordPressService::class);
            $creatorOrderSyncService = app(CreatorOrderSyncService::class);

            switch ($type) {
                case 'all':
                    // Exécuter les 3 jobs dans l'ordre
                    Log::info('Starting creators sync...');
                    (new SyncCreatorsJob())->handle($wordPressService);
                    
                    Log::info('Starting products sync...');
                    (new SyncProductsJob())->handle($wordPressService);
                    
                    Log::info('Starting orders sync...');
                    (new SyncOrdersJob($force ? 'full' : 'incremental', $force))
                        ->handle($wordPressService, $creatorOrderSyncService);
                    
                    $message = '✅ Synchronisation complète terminée avec succès';
                    break;

                case 'orders':
                    Log::info('Starting orders sync...');
                    (new SyncOrdersJob($force ? 'full' : 'incremental', $force))
                        ->handle($wordPressService, $creatorOrderSyncService);
                    $message = '✅ Synchronisation des commandes terminée avec succès';
                    break;

                case 'products':
                    Log::info('Starting products sync...');
                    (new SyncProductsJob())->handle($wordPressService);
                    $message = '✅ Synchronisation des produits terminée avec succès';
                    break;

                case 'creators':
                    Log::info('Starting creators sync...');
                    (new SyncCreatorsJob())->handle($wordPressService);
                    $message = '✅ Synchronisation des créateurs terminée avec succès';
                    break;

                default:
                    return back()->with('error', '❌ Type de synchronisation inconnu');
            }

            Log::info('Sync completed successfully', ['type' => $type]);
            
            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Direct sync failed', [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', '❌ Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Dispatch vers la queue
     */
    private function dispatchToQueue(string $type, bool $force)
    {
        Log::info('Dispatching to queue', ['type' => $type, 'force' => $force]);

        try {
            switch ($type) {
                case 'all':
                    SyncCreatorsJob::dispatch()->onQueue('sync');
                    SyncProductsJob::dispatch()->onQueue('sync');
                    SyncOrdersJob::dispatch($force ? 'full' : 'incremental', $force)
                        ->onQueue('sync');
                    $message = '✅ Synchronisation complète ajoutée à la queue';
                    break;

                case 'orders':
                    SyncOrdersJob::dispatch($force ? 'full' : 'incremental', $force)
                        ->onQueue('sync');
                    $message = '✅ Synchronisation des commandes ajoutée à la queue';
                    break;

                case 'products':
                    SyncProductsJob::dispatch()->onQueue('sync');
                    $message = '✅ Synchronisation des produits ajoutée à la queue';
                    break;

                case 'creators':
                    SyncCreatorsJob::dispatch()->onQueue('sync');
                    $message = '✅ Synchronisation des créateurs ajoutée à la queue';
                    break;

                default:
                    return back()->with('error', '❌ Type de synchronisation inconnu');
            }

            return back()->with('success', $message . ' Exécutez <code>php artisan queue:work --queue=sync</code>');

        } catch (\Exception $e) {
            Log::error('Queue dispatch failed', [
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', '❌ Erreur lors de l\'ajout à la queue : ' . $e->getMessage());
        }
    }

    public function logs(Request $request)
    {
        $logs = SyncLog::latest('started_at')
            ->paginate(20);

        return view('admin.sync.logs', compact('logs'));
    }

    public function stats()
    {
        $stats = [
            'last_24h' => $this->getSyncStats(24),
            'last_7_days' => $this->getSyncStats(24 * 7),
            'by_type' => $this->getSyncStatsByType(),
            'performance' => $this->getPerformanceStats(),
        ];

        return view('admin.sync.logs', compact('stats'));
    }

    public function logDetails(SyncLog $log)
    {
        return response()->json([
            'sync_type' => $log->sync_type,
            'status' => $log->status,
            'started_at' => $log->started_at?->format('Y-m-d H:i:s'),
            'completed_at' => $log->completed_at?->format('Y-m-d H:i:s'),
            'duration_ms' => $log->duration_ms,
            'total_records' => $log->total_records,
            'created_records' => $log->created_records,
            'updated_records' => $log->updated_records,
            'failed_records' => $log->failed_records,
            'error_message' => $log->error_message,
            'metadata' => is_string($log->metadata) ? json_decode($log->metadata, true) : $log->metadata,
        ]);
    }

    private function getSyncStats($hours)
    {
        return SyncLog::where('started_at', '>=', now()->subHours($hours))
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = "partial" THEN 1 ELSE 0 END) as partial,
                AVG(duration_ms) as avg_duration,
                SUM(total_records) as total_records_processed
            ')
            ->first();
    }

    private function getSyncStatsByType()
    {
        return SyncLog::selectRaw('
                sync_type,
                COUNT(*) as total,
                AVG(duration_ms) as avg_duration,
                MAX(started_at) as last_sync,
                SUM(total_records) as total_records
            ')
            ->groupBy('sync_type')
            ->orderBy('last_sync', 'desc')
            ->get();
    }

    private function getPerformanceStats()
    {
        return [
            'avg_sync_duration' => SyncLog::where('status', 'success')->avg('duration_ms'),
            'success_rate' => SyncLog::count() > 0
                ? round((SyncLog::where('status', 'success')->count() / SyncLog::count() * 100), 2)
                : 0,
            'records_per_hour' => SyncLog::where('started_at', '>=', now()->subDay())
                ->sum('total_records') / 24,
        ];
    }

    private function getQueueStatus()
    {
        try {
            $tablesExist = DB::select("SHOW TABLES LIKE 'jobs'");

            if (empty($tablesExist)) {
                return [
                    'pending' => 0,
                    'failed' => 0,
                    'last_failed' => null,
                    'warning' => 'Tables de queue non créées. Exécutez: php artisan queue:table && php artisan migrate',
                ];
            }

            return [
                'pending' => DB::table('jobs')->count(),
                'failed' => DB::table('failed_jobs')->count(),
                'last_failed' => DB::table('failed_jobs')
                    ->latest('failed_at')
                    ->first(),
            ];
        } catch (\Exception $e) {
            return [
                'pending' => 0,
                'failed' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }
}
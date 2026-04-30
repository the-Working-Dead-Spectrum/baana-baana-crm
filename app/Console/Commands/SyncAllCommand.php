<?php

namespace App\Console\Commands;

use App\Jobs\SyncCreatorsJob;
use App\Jobs\SyncOrdersJob;
use App\Jobs\SyncProductsJob;
use App\Services\CreatorOrderSyncService;
use App\Models\SyncLog;
use Illuminate\Console\Command;

class SyncAllCommand extends Command
{
    protected $signature = 'mpcrm:sync 
                            {type? : Type de sync (all, orders, products, creators, creators-orders)}
                            {--force : Forcer une sync complète}
                            {--queue : Utiliser la queue au lieu d\'exécuter immédiatement}
                            {--monitor : Afficher le monitoring après la sync}
                            {--validate : Valider l\'état de la sync des créateurs}';
    
    protected $description = 'Synchroniser les données avec WordPress';
    
    protected CreatorOrderSyncService $creatorSyncService;
    
    public function __construct(CreatorOrderSyncService $creatorSyncService)
    {
        parent::__construct();
        $this->creatorSyncService = $creatorSyncService;
    }
    
    public function handle(): int
    {
        $type = $this->argument('type') ?? 'all';
        $force = $this->option('force');
        $useQueue = $this->option('queue');
        $monitor = $this->option('monitor');
        $validate = $this->option('validate');
        
        // Mode validation uniquement
        if ($validate) {
            return $this->validateCreatorOrdersSync();
        }
        
        $this->displayHeader($type, $force, $useQueue);
        
        try {
            $startTime = microtime(true);
            
            switch ($type) {
                case 'all':
                    $this->syncAll($useQueue, $force);
                    break;
                    
                case 'orders':
                    $this->info('📦 Synchronisation des commandes...');
                    $this->executeJob(
                        SyncOrdersJob::class,
                        [$force ? 'full' : 'incremental', $force],
                        $useQueue
                    );
                    break;
                    
                case 'products':
                    $this->info('📦 Synchronisation des produits...');
                    $this->executeJob(SyncProductsJob::class, [], $useQueue);
                    break;
                    
                case 'creators':
                    $this->info('📦 Synchronisation des créateurs...');
                    $this->executeJob(SyncCreatorsJob::class, [], $useQueue);
                    break;
                
                case 'creators-orders':
                    return $this->syncCreatorsOrders($useQueue);
                    
                default:
                    $this->error("❌ Type de sync inconnu: {$type}");
                    $this->info('Types valides: all, orders, products, creators, creators-orders');
                    return 1;
            }
            
            $duration = round(microtime(true) - $startTime, 2);
            
            $this->displaySuccess($useQueue, $duration);
            
            // Afficher le monitoring si demandé
            if ($monitor) {
                $this->newLine();
                $this->call('mpcrm:monitor-sync', ['--hours' => 1]);
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la synchronisation: ' . $e->getMessage());
            
            if ($this->output->isVerbose()) {
                $this->error('Trace: ' . $e->getTraceAsString());
            }
            
            return 1;
        }
    }
    
    /**
     * Synchroniser toutes les données
     */
    private function syncAll(bool $useQueue, bool $force): void
    {
        $this->info('📦 Synchronisation complète...');
        $this->newLine();
        
        // 1. Créateurs
        $this->line('1️⃣ Synchronisation des créateurs...');
        $this->executeJob(SyncCreatorsJob::class, [], $useQueue);
        
        if (!$useQueue) {
            $this->info('   ✅ Créateurs synchronisés');
            sleep(1);
        }
        
        // 2. Produits
        $this->line('2️⃣ Synchronisation des produits...');
        $this->executeJob(SyncProductsJob::class, [], $useQueue);
        
        if (!$useQueue) {
            $this->info('   ✅ Produits synchronisés');
            sleep(1);
        }
        
        // 3. Commandes (qui synchronise automatiquement les créateurs)
        $this->line('3️⃣ Synchronisation des commandes...');
        $this->executeJob(
            SyncOrdersJob::class, 
            [$force ? 'full' : 'incremental', $force],
            $useQueue
        );
        
        if (!$useQueue) {
            $this->info('   ✅ Commandes synchronisées');
        }
    }
    
    /**
     * Synchroniser uniquement la relation créateurs ↔ commandes
     */
    private function syncCreatorsOrders(bool $useQueue): int
    {
        $this->info('🔗 Synchronisation de la relation créateurs ↔ commandes');
        $this->newLine();
        
        if ($useQueue) {
            $this->warn('⚠️  Le mode queue n\'est pas supporté pour cette opération');
            return 1;
        }
        
        // Demander confirmation
        if (!$this->confirm('Cette opération va re-synchroniser tous les créateurs pour toutes les commandes. Continuer ?', true)) {
            $this->info('❌ Opération annulée');
            return 1;
        }
        
        // Demander une limite
        $limit = $this->ask('Limiter le nombre de commandes ? (Entrée = toutes)', 'all');
        $limit = $limit === 'all' ? null : (int) $limit;
        
        $progressBar = $this->output->createProgressBar();
        $progressBar->start();
        
        try {
            $result = $this->creatorSyncService->syncAllOrders($limit);
            
            $progressBar->finish();
            $this->newLine(2);
            
            // Afficher les résultats
            $this->table(
                ['Métrique', 'Valeur'],
                [
                    ['Commandes traitées', $result['total_orders']],
                    ['Succès', $result['success_count']],
                    ['Échecs', $result['failed_count']],
                    ['Ignorées', $result['skipped_count']],
                ]
            );
            
            if ($result['success_count'] > 0) {
                $this->info('✅ Synchronisation terminée avec succès !');
            } else {
                $this->warn('⚠️  Aucune commande n\'a été synchronisée');
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $progressBar->finish();
            $this->newLine(2);
            $this->error('❌ Erreur: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Valider l'état de la synchronisation des créateurs
     */
    private function validateCreatorOrdersSync(): int
    {
        $this->info('🔍 Validation de la synchronisation créateurs ↔ commandes');
        $this->newLine();
        
        $result = $this->creatorSyncService->validateSync();
        
        // Afficher les métriques principales
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Créateurs actifs', $result['active_creators']],
                ['Commandes sans créateurs', $result['orders_without_creators']],
                ['Problèmes dans la table pivot', $result['pivot_issues_count']],
            ]
        );
        
        // Afficher les stats par créateur
        if ($result['creators_stats']->isNotEmpty()) {
            $this->newLine();
            $this->info('📊 Statistiques par créateur :');
            
            $this->table(
                ['ID', 'Nom', 'Brand Slug', 'Commandes'],
                $result['creators_stats']->map(function ($creator) {
                    return [
                        $creator['id'],
                        $creator['name'],
                        $creator['brand_slug'],
                        $creator['orders_count'],
                    ];
                })->toArray()
            );
        }
        
        $this->newLine();
        
        // Recommandations
        if ($result['needs_resync']) {
            $this->warn('⚠️  Une re-synchronisation est nécessaire !');
            $this->newLine();
            
            if ($this->confirm('Voulez-vous synchroniser maintenant ?', true)) {
                return $this->syncCreatorsOrders(false);
            }
        } else {
            $this->info('✅ Tout est en ordre !');
        }
        
        return 0;
    }
    
    /**
     * Afficher l'en-tête
     */
    private function displayHeader(string $type, bool $force, bool $useQueue): void
    {
        $this->info('🚀 Démarrage de la synchronisation: ' . $type);
        
        if ($force) {
            $this->warn('⚠️  Mode FORCE activé - synchronisation complète');
        }
        
        if ($useQueue) {
            $this->warn('📬 Mode QUEUE activé - les jobs seront mis en file d\'attente');
        } else {
            $this->info('⚡ Mode IMMÉDIAT - exécution synchrone');
        }
        
        $this->newLine();
    }
    
    /**
     * Afficher le message de succès
     */
    private function displaySuccess(bool $useQueue, float $duration): void
    {
        $this->newLine();
        
        if ($useQueue) {
            $this->info("✅ Jobs ajoutés à la queue en {$duration}s");
            $this->warn('⚠️  N\'oubliez pas de lancer: php artisan queue:work');
        } else {
            $this->info("✅ Synchronisation terminée en {$duration}s");
        }
    }
    
    /**
     * Exécute un job immédiatement ou le met en queue
     */
    private function executeJob(string $jobClass, array $params = [], bool $useQueue = false): void
    {
        if ($useQueue) {
            // Mettre en queue (asynchrone)
            $jobClass::dispatch(...$params);
        } else {
            // Exécuter immédiatement (synchrone)
            $jobClass::dispatchSync(...$params);
        }
    }
}
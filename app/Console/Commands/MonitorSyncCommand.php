<?php

namespace App\Console\Commands;

use App\Models\SyncLog;
use Illuminate\Console\Command;

class MonitorSyncCommand extends Command
{
    protected $signature = 'mpcrm:monitor-sync {--hours=24 : Nombre d\'heures à surveiller}';
    
    protected $description = 'Surveiller l\'état des synchronisations';
    
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        
        $logs = SyncLog::where('started_at', '>=', now()->subHours($hours))
            ->orderBy('started_at', 'desc')
            ->get();
        
        if ($logs->isEmpty()) {
            $this->info("Aucune synchronisation dans les dernières {$hours} heures.");
            return 0;
        }
        
        $this->table(
            ['Type', 'Date', 'Status', 'Total', 'Créés', 'Échoués', 'Durée'],
            $logs->map(function ($log) {
                return [
                    $log->sync_type,
                    $log->started_at->format('d/m/Y H:i'),
                    $this->formatStatus($log->status),
                    $log->total_records,
                    $log->created_records,
                    $log->failed_records,
                    $log->duration_ms . 'ms',
                ];
            })
        );
        
        // Vérification des échecs
        $failedLogs = $logs->where('status', 'failed')->count();
        
        if ($failedLogs > 0) {
            $this->error("⚠️ {$failedLogs} synchronisations ont échoué récemment!");
            
            $this->info("\nDerniers échecs:");
            $this->table(
                ['Date', 'Type', 'Erreur'],
                $logs->where('status', 'failed')
                    ->take(3)
                    ->map(function ($log) {
                        return [
                            $log->started_at->format('d/m/Y H:i'),
                            $log->sync_type,
                            substr($log->error_message ?? 'N/A', 0, 50) . '...',
                        ];
                    })
            );
        }
        
        return 0;
    }
    
    private function formatStatus($status): string
    {
        $colors = [
            'success' => 'green',
            'failed' => 'red',
            'partial' => 'yellow',
            'pending' => 'gray',
        ];
        
        $color = $colors[$status] ?? 'gray';
        
        return "<fg={$color}>{$status}</>";
    }
}
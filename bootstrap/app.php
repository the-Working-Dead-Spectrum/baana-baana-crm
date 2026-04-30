<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Enregistrer le middleware de rôle
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        // ✅ Exclure les webhooks de la validation CSRF
        $middleware->validateCsrfTokens(except: [
            'api/webhooks/*',
        ]);
        
        // ✅ Pas besoin de modifier web() - on va utiliser une autre approche
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Sync incrémentale des commandes toutes les 15 minutes
        $schedule->job(new \App\Jobs\SyncOrdersJob('incremental', false))
            ->everyFifteenMinutes()
            ->withoutOverlapping()
            ->onOneServer()
            ->name('sync-orders')
            ->description('Sync incrémentale des commandes WordPress');
        
        // Sync complète des commandes toutes les 6 heures
        $schedule->job(new \App\Jobs\SyncOrdersJob('full', false))
            ->everySixHours()
            ->withoutOverlapping()
            ->onOneServer()
            ->name('sync-orders-full')
            ->description('Sync complète des commandes WordPress');
        
        // Sync des produits toutes les heures
        $schedule->job(new \App\Jobs\SyncProductsJob())
            ->hourly()
            ->withoutOverlapping()
            ->onOneServer()
            ->name('sync-products')
            ->description('Sync des produits WordPress');
        
        // Sync des créateurs toutes les 2 heures
        $schedule->job(new \App\Jobs\SyncCreatorsJob())
            ->everyTwoHours()
            ->withoutOverlapping()
            ->onOneServer()
            ->name('sync-creators')
            ->description('Sync des créateurs WordPress');
        
        // Nettoyer les vieux logs de sync chaque jour
        $schedule->command('mpcrm:clean-sync-logs')
            ->daily()
            ->name('clean-sync-logs')
            ->description('Nettoyer les vieux logs de synchronisation');
        
        // Surveiller la santé des syncs
        $schedule->command('mpcrm:monitor-sync --hours=6')
            ->hourly()
            ->name('monitor-sync')
            ->description('Surveiller l\'état des synchronisations');
        
        // Purger les jobs échoués toutes les semaines
        $schedule->command('queue:prune-failed --hours=168')
            ->weekly()
            ->name('prune-failed-jobs')
            ->description('Purger les vieux jobs échoués');
        
        // Monitorer la queue toutes les heures
        $schedule->command('queue:monitor sync --max=100')
            ->hourly()
            ->name('monitor-queue')
            ->description('Monitorer la queue de synchronisation');
    })
    ->create();
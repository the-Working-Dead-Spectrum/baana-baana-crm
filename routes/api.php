<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('admin/api')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/charts/{type}', [AdminDashboardController::class, 'chartsData']);
    Route::get('/top-products', [AdminDashboardController::class, 'topProducts']);
    Route::get('/dashboard-stats', [AdminDashboardController::class, 'dashboardStats']);
    Route::get('/summary-stats', [AdminDashboardController::class, 'summaryStats']);
    Route::get('/live-updates', [AdminDashboardController::class, 'liveUpdates']);
});




// Route::prefix('webhooks')->group(function () {
//     // Test de connexion
//     Route::post('/wordpress/test', [WebhookController::class, 'testWordPressConnection'])
//         ->name('webhooks.wordpress.test');

//     // Création de créateur
//     Route::post('/creator-created', [WebhookController::class, 'handleCreatorCreated'])
//         ->name('webhooks.creator-created');

//     // Synchronisation des commandes
//     Route::post('/wordpress/sync-orders', [WebhookController::class, 'syncOrders'])
//         ->name('webhooks.wordpress.sync-orders');

//     // Synchronisation des produits
//     Route::post('/wordpress/sync-products', [WebhookController::class, 'syncProducts'])
//         ->name('webhooks.wordpress.sync-products');
// });
// NOTE: Les routes webhook sont définies dans web.php
// Ne pas les dupliquer ici pour éviter les conflits
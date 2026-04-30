<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreatorDashboardController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\ProductReportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CustomerSegmentController;
use App\Http\Controllers\AdminSyncController;

Route::get('/', function () {
    return view('auth.login');
});


Route::prefix('api/webhooks')->group(function () {
    Route::post('/wordpress/test', [WebhookController::class, 'testWordPressConnection'])
        ->name('webhooks.wordpress.test');

    Route::post('/creator-created', [WebhookController::class, 'handleCreatorCreated'])
        ->name('webhooks.creator-created');

    Route::post('/wordpress/sync-orders', [WebhookController::class, 'syncOrders'])
        ->name('webhooks.wordpress.sync-orders');

    Route::post('/sync-orders-with-creators', [WebhookController::class, 'syncOrdersWithCreators'])
        ->name('webhooks.sync-orders-with-creators');

    Route::post('/wordpress/sync-products', [WebhookController::class, 'syncProducts'])
        ->name('webhooks.wordpress.sync-products');

    Route::post('/sync-products-by-brand', [WebhookController::class, 'syncProductsByBrand'])
        ->name('webhooks.sync-products-by-brand');

    Route::post('/full-sync', [WebhookController::class, 'fullSync'])
        ->name('webhooks.full-sync');

    Route::post('/sync-brands', [WebhookController::class, 'syncBrands'])
        ->name('webhooks.sync-brands');

    Route::post('/creator-deleted', [WebhookController::class, 'handleCreatorDeleted'])
        ->name('webhooks.creator-deleted');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/password/change', [ProfileController::class, 'changePassword'])->name('profile.password.change');
    Route::post('/profile/password/update', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

Route::middleware('auth')->group(function () {
    Route::middleware('role:creator')->group(function () {
        Route::get('/creator/dashboard', [CreatorDashboardController::class, 'dashboard'])
            ->name('creator.dashboard');

        Route::get('/creator/setup', [CreatorDashboardController::class, 'setup'])
            ->name('creator.setup');

        Route::post('/creator/setup', [CreatorDashboardController::class, 'processSetup'])
            ->name('creator.setup.process');

        Route::get('/creator/orders', [CreatorDashboardController::class, 'orders'])
            ->name('creator.orders');

        Route::get('/creator/orders/{id}', [CreatorDashboardController::class, 'showOrder'])
            ->name('creator.orders.show');

        Route::patch('/creator/orders/{id}/complete', [CreatorDashboardController::class, 'completeOrder'])
            ->name('creator.orders.complete');

        Route::patch(
            '/creator/orders/{order}/transfer-logistics',
            [CreatorDashboardController::class, 'transferToLogistics']
        )->name('creator.orders.transfer.logistics');



        Route::get('/creator/products', [CreatorDashboardController::class, 'products'])
            ->name('creator.products');

        Route::get('/creator/products/{id}', [CreatorDashboardController::class, 'showProduct'])
            ->name('creator.products.show');

        Route::get('/creator/analytics', [CreatorDashboardController::class, 'analytics'])
            ->name('creator.analytics');

        Route::get('/creator/stats', [CreatorDashboardController::class, 'stats'])
            ->name('creator.stats');

        Route::get('/creator/profile', [CreatorDashboardController::class, 'profile'])
            ->name('creator.profile');

        Route::put('/creator/profile', [CreatorDashboardController::class, 'updateProfile'])
            ->name('creator.profile.update');

        Route::get('/creator/api/stats', [CreatorDashboardController::class, 'statsApi'])
            ->name('creator.api.stats');

        Route::get('/creator/api/recent-orders', [CreatorDashboardController::class, 'recentOrdersApi'])
            ->name('creator.api.recent-orders');
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('/sync', [AdminSyncController::class, 'index'])
            ->name('admin.sync.index');

        Route::post('/sync/run', [AdminSyncController::class, 'runSync'])
            ->name('admin.sync.run');

        Route::post('/sync/orders', [AdminDashboardController::class, 'syncOrders'])
            ->name('admin.orders.sync');

        Route::get('/sync/logs', [AdminSyncController::class, 'logs'])
            ->name('admin.sync.logs');

        Route::get('/sync/logs/{log}', [AdminSyncController::class, 'logDetails'])
            ->name('admin.sync.logs.details');

        Route::get('/sync/stats', [AdminSyncController::class, 'stats'])
            ->name('admin.sync.stats');

        Route::get('/orders', [AdminDashboardController::class, 'orders'])
            ->name('admin.orders');

        Route::get('/orders/{id}', [AdminDashboardController::class, 'showOrder'])
            ->name('admin.orders.show');

        Route::put('/orders/{id}/status', [AdminDashboardController::class, 'updateOrderStatus'])
            ->name('admin.orders.update-status');

        Route::get('/customers', [CustomerController::class, 'index'])
            ->name('admin.customers.index');

        Route::get('/customers/search', [CustomerController::class, 'search'])
            ->name('admin.customers.search');

        Route::get('/customers/export', [CustomerController::class, 'export'])
            ->name('admin.customers.export');

        Route::get('/customers/{customer}', [CustomerController::class, 'show'])
            ->where('customer', '.*')
            ->name('admin.customers.show');

        Route::get('/creators', [AdminDashboardController::class, 'creators'])
            ->name('admin.creators');

        Route::get('/creators/{creator}', [AdminDashboardController::class, 'showCreator'])->name('admin.creators.show');
        Route::get('/products', [AdminDashboardController::class, 'products'])
            ->name('admin.products');

        Route::get('/products/brand/{brandSlug}', [ProductReportController::class, 'byBrand'])
            ->name('products.by-brand');

        Route::get('/api/products/brand/{brandSlug}', [ProductReportController::class, 'getBrandProductsAjax'])
            ->name('products.brand.ajax');

        Route::get('/products/{id}', [ProductReportController::class, 'showProduct'])
            ->name('products.show');

        Route::get('/reports/product-sales', [ProductReportController::class, 'index'])
            ->name('admin.reports.product-sales');

        Route::get('/users', [UserController::class, 'index'])
            ->name('admin.users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('admin.users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->name('admin.users.store');

        Route::get('/users/{user}', [UserController::class, 'show'])
            ->name('admin.users.show');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('admin.users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('admin.users.update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('admin.users.destroy');

        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('admin.users.toggle-status');

        Route::get('/roles', [RoleController::class, 'index'])
            ->name('admin.roles.index');

        Route::post('/roles/permissions', [RoleController::class, 'updatePermissions'])
            ->name('admin.roles.update-permissions');

        Route::get('/roles/{role}/users', [RoleController::class, 'showUsers'])
            ->name('admin.roles.users');

        Route::get('/settings', [AdminDashboardController::class, 'settings'])
            ->name('admin.settings');

        Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])
            ->name('admin.settings.update');

        Route::get('/settings/test-wordpress', [AdminDashboardController::class, 'testWordPressConnection'])
            ->name('admin.settings.test');

        Route::get('/api/recent-orders', [AdminDashboardController::class, 'recentOrdersApi'])
            ->name('admin.api.recent-orders');

        Route::get('/api/sync-stats', [AdminDashboardController::class, 'syncStats'])
            ->name('admin.api.sync-stats');

        Route::get('/segments', [CustomerSegmentController::class, 'index'])
            ->name('segments.index');

        Route::get('/segments/export/{segment}', [CustomerSegmentController::class, 'export'])
            ->name('segments.export');

        Route::get('/segments/custom', [CustomerSegmentController::class, 'customFilter'])
            ->name('segments.custom');
    });

    Route::middleware('role:logistic')->group(function () {
        Route::get('/logistics/dashboard', [LogisticsController::class, 'dashboard'])
            ->name('logistics.dashboard');
        
        Route::get('/logistics/orders', [LogisticsController::class, 'orders'])
            ->name('logistics.orders');
        
        Route::get('/logistics/orders/{order}', [LogisticsController::class, 'showOrder'])
            ->name('logistics.orders.show');
        
        Route::post('/logistics/orders/{order}/pickup', [LogisticsController::class, 'createPickup'])
            ->name('logistics.orders.create-pickup');
        
        Route::post('/logistics/orders/{order}/refresh-status', [LogisticsController::class, 'refreshStatus'])
            ->name('logistics.orders.refresh-status');
        
        Route::post('/logistics/orders/{order}/calculate-fees', [LogisticsController::class, 'calculateFees'])
            ->name('logistics.orders.calculate-fees');
    });

    // Webhook PAPS (sans authentification car appelé par PAPS)
    Route::post('/api/webhooks/paps/status', [LogisticsController::class, 'handleWebhook'])
        ->name('webhooks.paps.status');
});

require __DIR__ . '/auth.php';
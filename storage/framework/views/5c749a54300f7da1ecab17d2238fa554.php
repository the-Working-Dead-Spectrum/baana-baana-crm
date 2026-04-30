

<?php $__env->startSection('title', $product->name); ?>

<?php $__env->startSection('content'); ?>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <a href="<?php echo e(route('creator.products')); ?>" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Retour aux produits
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900"><?php echo e($product->name); ?></h1>
                    <p class="text-gray-600 mt-2"><?php echo e($creator->brand_name); ?></p>
                </div>
                <div>
                    <?php if($salesStats && ($salesStats->total_quantity ?? 0) > 0): ?>
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Produit actif
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-800">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Aucune vente
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Statistiques de vente -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Performances de vente</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Total des ventes -->
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-green-800 mb-1">Chiffre d'affaires total</p>
                                        <p class="text-3xl font-bold text-green-900"><?php echo e(number_format($salesStats->total_sales ?? 0, 0, ',', ' ')); ?> CFA</p>
                                    </div>
                                    <div class="h-16 w-16 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Unités vendues -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-blue-800 mb-1">Unités vendues</p>
                                        <p class="text-3xl font-bold text-blue-900"><?php echo e($salesStats->total_quantity ?? 0); ?></p>
                                    </div>
                                    <div class="h-16 w-16 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Nombre de commandes -->
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-purple-800 mb-1">Nombre de commandes</p>
                                        <p class="text-3xl font-bold text-purple-900"><?php echo e($salesStats->order_count ?? 0); ?></p>
                                    </div>
                                    <div class="h-16 w-16 bg-purple-500 rounded-full flex items-center justify-center">
                                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Prix moyen -->
                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6 border border-yellow-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-yellow-800 mb-1">Prix unitaire moyen</p>
                                        <p class="text-3xl font-bold text-yellow-900"><?php echo e(number_format($salesStats->average_unit_price ?? 0, 0, ',', ' ')); ?> CFA</p>
                                    </div>
                                    <div class="h-16 w-16 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphique des ventes mensuelles -->
                <?php if(!empty($monthlyStats) && count($monthlyStats) > 0): ?>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Évolution des ventes (6 derniers mois)</h2>
                        <div class="h-80">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Commandes récentes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Commandes récentes</h2>
                        
                        <?php if($recentOrders && $recentOrders->count() > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                N° Commande
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Client
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Quantité
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Statut
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $productItem = $order->items->where('wp_product_id', $product->wp_product_id)->first();
                                            ?>
                                            <?php if($productItem): ?>
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="text-sm font-medium text-gray-900">#<?php echo e($order->order_number); ?></span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900"><?php echo e($order->customer_name); ?></div>
                                                        <div class="text-xs text-gray-500"><?php echo e($order->customer_email); ?></div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <?php echo e($productItem->quantity); ?> unité<?php echo e($productItem->quantity > 1 ? 's' : ''); ?>

                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <?php echo e($order->order_date?->format('d/m/Y')); ?>

                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?php echo e($order->status == 'completed' ? 'green' : ($order->status == 'pending' ? 'yellow' : 'gray')); ?>-100 text-<?php echo e($order->status == 'completed' ? 'green' : ($order->status == 'pending' ? 'yellow' : 'gray')); ?>-800">
                                                            <?php echo e(ucfirst($order->status)); ?>

                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="<?php echo e(route('creator.orders.show', $order->id)); ?>" class="text-blue-600 hover:text-blue-900">
                                                            Voir →
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune commande</h3>
                                <p class="mt-1 text-sm text-gray-500">Ce produit n'a pas encore été commandé.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">
                <!-- Image du produit -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="aspect-square bg-gray-200 rounded-lg flex items-center justify-center mb-4">
                            <svg class="h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <p class="text-xs text-center text-gray-500">Image du produit non disponible</p>
                    </div>
                </div>

                <!-- Informations du produit -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations produit</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Prix</p>
                                <p class="text-lg font-bold text-gray-900"><?php echo e(number_format($product->price, 0, ',', ' ')); ?> CFA</p>
                            </div>
                            
                            <?php if($product->sku): ?>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">SKU</p>
                                <p class="text-sm font-medium text-gray-900"><?php echo e($product->sku); ?></p>
                            </div>
                            <?php endif; ?>

                            <div>
                                <p class="text-xs text-gray-500 mb-1">ID WordPress</p>
                                <p class="text-sm font-medium text-gray-900"><?php echo e($product->wp_product_id); ?></p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 mb-1">Marque</p>
                                <p class="text-sm font-medium text-gray-900"><?php echo e($product->brand_slug); ?></p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 mb-1">Date d'ajout</p>
                                <p class="text-sm text-gray-900"><?php echo e($product->created_at?->format('d/m/Y')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <?php if($product->description): ?>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
                        <p class="text-sm text-gray-700 leading-relaxed"><?php echo e($product->description); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Insights -->
                <?php if($salesStats && ($salesStats->total_quantity ?? 0) > 0): ?>
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 overflow-hidden shadow-sm sm:rounded-lg border border-indigo-200">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-indigo-900 mb-4">Insights</h2>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="ml-3 text-sm text-indigo-900">
                                    Vendu dans <span class="font-semibold"><?php echo e($salesStats->order_count); ?></span> commande<?php echo e($salesStats->order_count > 1 ? 's' : ''); ?>

                                </p>
                            </div>

                            <?php if($salesStats->total_quantity > 0): ?>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="ml-3 text-sm text-indigo-900">
                                    Moyenne de <span class="font-semibold"><?php echo e(round($salesStats->total_quantity / $salesStats->order_count, 1)); ?></span> unité<?php echo e(($salesStats->total_quantity / $salesStats->order_count) > 1 ? 's' : ''); ?> par commande
                                </p>
                            </div>
                            <?php endif; ?>

                            <?php if($salesStats->total_sales > 0): ?>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="ml-3 text-sm text-indigo-900">
                                    Génère en moyenne <span class="font-semibold"><?php echo e(number_format($salesStats->total_sales / $salesStats->order_count, 0, ',', ' ')); ?> CFA</span> par commande
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                        <div class="space-y-2">
                            <a href="<?php echo e(route('creator.products')); ?>" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Retour à la liste
                            </a>
                            <?php if($recentOrders && $recentOrders->count() > 0): ?>
                            <a href="<?php echo e(route('creator.orders', ['search' => $product->name])); ?>" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Voir les commandes
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<?php if(!empty($monthlyStats) && count($monthlyStats) > 0): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        const monthlyStats = <?php echo json_encode($monthlyStats, 15, 512) ?>;
        
        if (monthlyStats && monthlyStats.length > 0) {
            const labels = monthlyStats.map(item => item.label || '');
            const salesData = monthlyStats.map(item => item.sales || 0);
            const quantityData = monthlyStats.map(item => item.quantity || 0);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventes (CFA)',
                        data: salesData,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        yAxisID: 'y'
                    }, {
                        label: 'Quantité',
                        data: quantityData,
                        backgroundColor: 'rgba(16, 185, 129, 0.5)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 2,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Ventes (CFA)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('fr-FR').format(value) + ' CFA';
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Quantité'
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        if (context.datasetIndex === 0) {
                                            label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' CFA';
                                        } else {
                                            label += context.parsed.y + ' unité' + (context.parsed.y > 1 ? 's' : '');
                                        }
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/creator/product/show.blade.php ENDPATH**/ ?>
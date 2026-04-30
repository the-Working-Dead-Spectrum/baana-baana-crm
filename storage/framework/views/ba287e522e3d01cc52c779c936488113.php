

<?php $__env->startSection('title', 'Détails - ' . $product->name); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="<?php echo e(route('products.by-brand', $product->brand_slug)); ?>" 
                       class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900"><?php echo e($product->name); ?></h1>
                        <p class="text-gray-600 mt-1">Détails du produit</p>
                    </div>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="<?php echo e(route('products.by-brand', $product->brand_slug)); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    ← Retour à <?php echo e($product->brand_slug); ?>

                </a>
                <a href="<?php echo e(route('admin.products')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    ← Toutes les marques
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Détails du produit -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations produit</h2>
                        
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="border-b border-gray-200 pb-4">
                                <dt class="text-sm font-medium text-gray-500">ID WordPress</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo e($product->wp_product_id); ?></dd>
                            </div>
                            
                            <div class="border-b border-gray-200 pb-4">
                                <dt class="text-sm font-medium text-gray-500">ID Local</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo e($product->id); ?></dd>
                            </div>
                            
                            <div class="border-b border-gray-200 pb-4">
                                <dt class="text-sm font-medium text-gray-500">SKU</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo e($product->sku ?? 'Non défini'); ?></dd>
                            </div>
                            
                            <div class="border-b border-gray-200 pb-4">
                                <dt class="text-sm font-medium text-gray-500">Marque</dt>
                                <dd class="mt-1">
                                    <a href="<?php echo e(route('products.by-brand', $product->brand_slug)); ?>" 
                                       class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200">
                                        <?php echo e($product->brand_slug); ?>

                                    </a>
                                </dd>
                            </div>
                            
                            <div class="border-b border-gray-200 pb-4">
                                <dt class="text-sm font-medium text-gray-500">Prix</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    <?php echo e(number_format($product->price ?? 0, 0, ',', ' ')); ?> CFA
                                </dd>
                            </div>
                            
                            <div class="border-b border-gray-200 pb-4">
                                <dt class="text-sm font-medium text-gray-500">Stock</dt>
                                <dd class="mt-1">
                                    
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        En stock
                                    </span>
                                </dd>
                            </div>
                            
                            <div class="border-b border-gray-200 pb-4">
                                <dt class="text-sm font-medium text-gray-500">Valeur en stock</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    <?php echo e(number_format(($product->price ?? 0) * ($product->stock_quantity ?? 0), 0, ',', ' ')); ?> CFA
                                </dd>
                            </div>
                            
                            <div class="border-b border-gray-200 pb-4">
                                <dt class="text-sm font-medium text-gray-500">Dernière mise à jour</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <?php echo e($product->updated_at ? $product->updated_at->format('d/m/Y H:i') : 'N/A'); ?>

                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Historique des ventes -->
                <?php if($recentSales && $recentSales->count() > 0): ?>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Dernières commandes</h2>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                            Date
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                            Commande
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                            Quantité
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                            Prix unitaire
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo e($sale->created_at ? $sale->created_at->format('d/m/Y') : 'N/A'); ?>

                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <a href="#" class="text-blue-600 hover:text-blue-900">
                                                #<?php echo e($sale->order_id); ?>

                                            </a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo e($sale->quantity); ?>

                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo e(number_format($sale->unit_price ?? 0, 0, ',', ' ')); ?> CFA
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            <?php echo e(number_format($sale->total ?? 0, 0, ',', ' ')); ?> CFA
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Statistiques de vente -->
            <div class="space-y-6">
                <!-- Statistiques globales -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques de vente</h3>
                        
                        <div class="space-y-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-blue-600 mb-1">Quantité commandée</p>
                                <p class="text-2xl font-bold text-blue-900">
                                    <?php echo e(number_format($salesStats->total_quantity ?? 0, 0, ',', ' ')); ?>

                                </p>
                                <p class="text-xs text-blue-600 mt-1">unités</p>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-blue-600 mb-1">Quantité vendue</p>
                                <p class="text-2xl font-bold text-blue-900">
                                    <?php echo e(number_format($orderClosed->completed_quantity  ?? 0, 0, ',', ' ')); ?>

                                </p>
                                <p class="text-xs text-blue-600 mt-1">unités</p>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-green-600 mb-1">Chiffre d'affaires</p>
                                <p class="text-2xl font-bold text-green-900">
                                    <?php echo e(number_format($orderClosed->completed_sales ?? 0, 0, ',', ' ')); ?>

                                </p>
                                <p class="text-xs text-green-600 mt-1">CFA</p>
                            </div>
                            
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-purple-600 mb-1">Nombre de commandes</p>
                                <p class="text-2xl font-bold text-purple-900">
                                    <?php echo e(number_format($salesStats->order_count ?? 0, 0, ',', ' ')); ?>

                                </p>
                                <p class="text-xs text-purple-600 mt-1">commandes</p>
                            </div>
                            
                            <div class="bg-orange-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-orange-600 mb-1">Prix moyen de vente</p>
                                <p class="text-2xl font-bold text-orange-900">
                                    <?php echo e(number_format($salesStats->average_price ?? 0, 0, ',', ' ')); ?>

                                </p>
                                <p class="text-xs text-orange-600 mt-1">CFA</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Indicateurs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Indicateurs</h3>
                        
                        <div class="space-y-3">
                            <?php
                                $stock = $product->stock_quantity ?? 0;
                                $totalSales = $salesStats->total_quantity ?? 0;
                                $avgSales = $totalSales > 0 && $salesStats->order_count > 0 
                                    ? $totalSales / $salesStats->order_count 
                                    : 0;
                                $stockDays = $avgSales > 0 ? round($stock / $avgSales) : null;
                            ?>
                            
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-600">Statut stock</span>
                                <span class="text-sm font-medium <?php echo e($stock > 20 ? 'text-green-600' : ($stock > 0 ? 'text-yellow-600' : 'text-red-600')); ?>">
                                    <?php echo e($stock > 20 ? 'En stock' : ($stock > 0 ? 'Stock faible' : 'Rupture')); ?>

                                </span>
                            </div>
                            
                            <?php if($totalSales > 0): ?>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-600">Ventes/commande</span>
                                <span class="text-sm font-medium text-gray-900">
                                    <?php echo e(number_format($avgSales, 2, ',', ' ')); ?>

                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($stockDays !== null && $totalSales > 0): ?>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-600">Jours de stock restants</span>
                                <span class="text-sm font-medium <?php echo e($stockDays < 7 ? 'text-red-600' : ($stockDays < 30 ? 'text-yellow-600' : 'text-green-600')); ?>">
                                    ~<?php echo e($stockDays); ?> jours
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-600">Performance</span>
                                <span class="text-sm font-medium <?php echo e($totalSales > 50 ? 'text-green-600' : ($totalSales > 10 ? 'text-blue-600' : 'text-gray-600')); ?>">
                                    <?php echo e($totalSales > 50 ? 'Excellent' : ($totalSales > 10 ? 'Bon' : 'Faible')); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/admin/products/show.blade.php ENDPATH**/ ?>
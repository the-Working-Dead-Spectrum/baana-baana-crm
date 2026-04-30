

<?php $__env->startSection('title', 'Mes Produits'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Mes Produits</h1>
                    <p class="text-gray-600 mt-2">Gérez et suivez les performances de vos produits</p>
                    <p class="text-sm text-gray-500 mt-1">Marque: <?php echo e($creator->brand_name); ?> (<?php echo e($creator->brand_slug); ?>)</p>
                </div>
                <div>
                    <a href="<?php echo e(route('creator.dashboard')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques récapitulatives -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total produits</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($summary['total_products'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Avec ventes</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($summary['products_with_sales'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Ventes totales</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e(number_format($summary['total_products_sales'] ?? 0, 0, ',', ' ')); ?> CFA</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Unités vendues</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($summary['total_products_quantity'] ?? 0); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meilleurs produits -->
        <?php if($bestSellingProducts && $bestSellingProducts->count() > 0): ?>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Top 5 des produits</h2>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <?php $__currentLoopData = $bestSellingProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="relative bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border-2 border-<?php echo e($index == 0 ? 'yellow' : ($index == 1 ? 'gray' : 'orange')); ?>-<?php echo e($index == 0 ? '400' : '300'); ?>">
                        <div class="absolute -top-3 -left-3 w-8 h-8 bg-<?php echo e($index == 0 ? 'yellow' : ($index == 1 ? 'gray' : 'orange')); ?>-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            <?php echo e($index + 1); ?>

                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2"><?php echo e($product['name']); ?></h3>
                        <div class="space-y-1">
                            <p class="text-xs text-gray-500">Ventes: <span class="font-semibold text-gray-900"><?php echo e(number_format($product['total_sales'], 0, ',', ' ')); ?> CFA</span></p>
                            <p class="text-xs text-gray-500">Vendus: <span class="font-semibold text-gray-900"><?php echo e($product['total_quantity']); ?></span></p>
                        </div>
                        <a href="<?php echo e(route('creator.products.show', $product['wp_product_id'])); ?>" class="mt-3 block text-center text-xs text-blue-600 hover:text-blue-800">
                            Voir détails →
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Filtres et recherche -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
            <form method="GET" action="<?php echo e(route('creator.products')); ?>" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Recherche -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   id="search" 
                                   value="<?php echo e($filters['q'] ?? ''); ?>"
                                   placeholder="Nom, SKU, description..."
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Filtre par ventes -->
                    <div>
                        <label for="has_sales" class="block text-sm font-medium text-gray-700 mb-2">Statut des ventes</label>
                        <select name="has_sales" id="has_sales" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous</option>
                            <option value="yes" <?php echo e(($filters['has_sales'] ?? '') == 'yes' ? 'selected' : ''); ?>>Avec ventes</option>
                            <option value="no" <?php echo e(($filters['has_sales'] ?? '') == 'no' ? 'selected' : ''); ?>>Sans ventes</option>
                        </select>
                    </div>

                    <!-- Tri -->
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                        <select name="sort_by" id="sort_by" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="name" <?php echo e($sort_by == 'name' ? 'selected' : ''); ?>>Nom</option>
                            <option value="price" <?php echo e($sort_by == 'price' ? 'selected' : ''); ?>>Prix</option>
                            <option value="created_at" <?php echo e($sort_by == 'created_at' ? 'selected' : ''); ?>>Date d'ajout</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center text-sm text-gray-700">
                            <input type="radio" name="sort_order" value="asc" <?php echo e($sort_order == 'asc' ? 'checked' : ''); ?> class="mr-2">
                            Croissant
                        </label>
                        <label class="inline-flex items-center text-sm text-gray-700">
                            <input type="radio" name="sort_order" value="desc" <?php echo e($sort_order == 'desc' ? 'checked' : ''); ?> class="mr-2">
                            Décroissant
                        </label>
                    </div>
                    <div class="flex space-x-2">
                        <a href="<?php echo e(route('creator.products')); ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Réinitialiser
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Appliquer les filtres
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Liste des produits -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produit
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                SKU
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prix
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ventes
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantité vendue
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Commandes
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($product['name']); ?></div>
                                            <?php if($product['description']): ?>
                                                <div class="text-xs text-gray-500 line-clamp-1"><?php echo e($product['description']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900"><?php echo e($product['sku'] ?? 'N/A'); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900"><?php echo e(number_format($product['price'] ?? 0, 0, ',', ' ')); ?> CFA</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($product['has_sales']): ?>
                                        <span class="text-sm font-semibold text-green-600"><?php echo e(number_format($product['total_sales'], 0, ',', ' ')); ?> CFA</span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($product['has_sales']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo e($product['total_quantity']); ?> unités
                                        </span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($product['has_sales']): ?>
                                        <span class="text-sm text-gray-900"><?php echo e($product['order_count']); ?></span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo e(route('creator.products.show', $product['wp_product_id'])); ?>" class="text-blue-600 hover:text-blue-900">
                                        Voir détails →
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun produit trouvé</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        <?php if(!empty($filters['q']) || !empty($filters['has_sales'])): ?>
                                            Essayez de modifier vos filtres de recherche.
                                        <?php else: ?>
                                            Vous n'avez pas encore de produits.
                                        <?php endif; ?>
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($allProducts->hasPages()): ?>
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <?php echo e($allProducts->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/creator/products.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'Produits'); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Produits</h1>
                    <p class="text-gray-600 mt-2">Gérez les produits et leurs statistiques</p>
                </div>
                <div>
                    <a href="<?php echo e(route('admin.dashboard')); ?>"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        ← Retour
                    </a>
                </div>
            </div>

            <!-- Statistiques par marque -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Statistiques par marque</h2>

                    <?php if(isset($allBrands) && count($allBrands) > 0): ?>
                        <div class="mb-4">
                            <div class="text-sm text-gray-600">
                                Total : <span class="font-semibold"><?php echo e(count($allBrands)); ?></span> marques |
                                Produits avec marque : <span
                                    class="font-semibold"><?php echo e($summary['total_products_with_brand'] ?? 0); ?></span> |
                                Produits sans marque : <span
                                    class="font-semibold"><?php echo e($summary['total_products_without_brand'] ?? 0); ?></span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Marque
                                        </th>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nombre de produits
                                        </th>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Commandes
                                        </th>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__currentLoopData = $allBrands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brandSlug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            // Trouver les statistiques pour cette marque
                                            $brandData = $brandStats->firstWhere('brand_slug', $brandSlug) ?? [
                                                'product_count' => 0,
                                                'in_stock_count' => 0,
                                                'out_of_stock_count' => 0,
                                                'total_stock' => 0,
                                                'low_stock_count' => 0,
                                                'total_quantity' => 0,
                                                'has_sales' => false,
                                            ];

                                            $productCount = $brandData['product_count'] ?? 0;
                                            $hasProducts = $productCount > 0;
                                            $hasSales = $brandData['has_sales'] ?? false;
                                        ?>

                                        <tr class="hover:bg-gray-50 <?php echo e(!$hasProducts ? 'bg-gray-50' : ''); ?>">
                                            <!-- Marque -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo e($brandSlug); ?>

                                                    </div>
                                                    <?php if(!$hasProducts): ?>
                                                        <span
                                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                            Sans produits
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if($hasSales && $hasProducts): ?>
                                                        <span
                                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                            Avec ventes
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <!-- Nombre de produits -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-center">
                                                    <span
                                                        class="text-lg font-semibold <?php echo e($hasProducts ? 'text-gray-900' : 'text-gray-400'); ?>">
                                                        <?php echo e($productCount); ?>

                                                    </span>
                                                    <div class="text-xs text-gray-500">
                                                        produits
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Ventes -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-center">
                                                    <?php if(($brandData['total_quantity'] ?? 0) > 0): ?>
                                                        <div class="text-sm font-medium text-blue-600">
                                                            <?php echo e(number_format($brandData['total_quantity'], 0, ',', ' ')); ?>

                                                        </div>
                                                        <div class="text-xs text-blue-500">
                                                            unités commandées
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-sm text-gray-400">-</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <?php if($hasProducts): ?>
                                                    <a href="<?php echo e(route('products.by-brand', $brandSlug)); ?>"
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        Voir produits
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-gray-400 text-sm">Aucun produit</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Marques sans produits (si vous voulez les séparer) -->
                        <?php
                            $brandsWithoutProducts = collect($allBrands)->filter(function ($brandSlug) use (
                                $brandStats,
                            ) {
                                $brandData = $brandStats->firstWhere('brand_slug', $brandSlug);
                                return ($brandData['product_count'] ?? 0) === 0;
                            });
                        ?>

                        <?php if($brandsWithoutProducts->count() > 0): ?>
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Marques sans produits enregistrés</h3>
                                <div class="flex flex-wrap gap-2">
                                    <?php $__currentLoopData = $brandsWithoutProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brandSlug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <?php echo e($brandSlug); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    Ces marques existent dans le système mais n'ont aucun produit associé.
                                </p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune marque trouvée</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Aucune marque n'a été synchronisée depuis WordPress.
                            </p>
                            <div class="mt-6">
                                <button type="button" onclick="syncBrands()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Synchroniser les marques
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Liste des produits -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- En-tête avec compteurs -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Tous les produits</h2>
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-500">
                                <?php echo e($allProducts->total()); ?> produit(s) au total
                            </div>
                            <div class="text-xs text-gray-400">
                                <?php echo e($summary['products_with_sales'] ?? 0); ?> avec ventes
                            </div>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form method="GET" action="<?php echo e(route('admin.products')); ?>" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                                <!-- Recherche -->
                                <div>
                                    <label for="search"
                                        class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                                    <input type="text" name="search" id="search"
                                        value="<?php echo e($filters['search'] ?? ''); ?>" placeholder="Nom, SKU, ID..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>

                                <!-- Filtre par marque -->
                                <div>
                                    <label for="brand"
                                        class="block text-sm font-medium text-gray-700 mb-1">Marque</label>
                                    <select name="brand" id="brand"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">Toutes les marques</option>
                                        <?php $__currentLoopData = $allBrands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brandSlug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($brandSlug); ?>"
                                                <?php echo e(($filters['brand'] ?? '') === $brandSlug ? 'selected' : ''); ?>>
                                                <?php echo e($brandSlug); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <!-- Filtre par ventes -->
                                <div>
                                    <label for="has_sales" class="block text-sm font-medium text-gray-700 mb-1">Statut
                                        ventes</label>
                                    <select name="has_sales" id="has_sales"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">Tous les produits</option>
                                        <option value="yes"
                                            <?php echo e(($filters['has_sales'] ?? '') === 'yes' ? 'selected' : ''); ?>>Avec ventes
                                        </option>
                                        <option value="no"
                                            <?php echo e(($filters['has_sales'] ?? '') === 'no' ? 'selected' : ''); ?>>Sans vente
                                        </option>
                                    </select>
                                </div>

                                <!-- Tri -->
                                <div>
                                    <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Trier
                                        par</label>
                                    <div class="flex space-x-2">
                                        <select name="sort_by" id="sort_by"
                                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="name"
                                                <?php echo e(($filters['sort_by'] ?? 'name') === 'name' ? 'selected' : ''); ?>>Nom
                                            </option>
                                            <option value="price"
                                                <?php echo e(($filters['sort_by'] ?? '') === 'price' ? 'selected' : ''); ?>>Prix
                                            </option>
                                            <option value="brand"
                                                <?php echo e(($filters['sort_by'] ?? '') === 'brand' ? 'selected' : ''); ?>>Marque
                                            </option>
                                            <option value="sales"
                                                <?php echo e(($filters['sort_by'] ?? '') === 'sales' ? 'selected' : ''); ?>>CA</option>
                                            <option value="quantity"
                                                <?php echo e(($filters['sort_by'] ?? '') === 'quantity' ? 'selected' : ''); ?>>Quantité
                                            </option>
                                        </select>
                                        <select name="sort_order"
                                            class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="asc"
                                                <?php echo e(($filters['sort_order'] ?? 'asc') === 'asc' ? 'selected' : ''); ?>>↑ Asc
                                            </option>
                                            <option value="desc"
                                                <?php echo e(($filters['sort_order'] ?? '') === 'desc' ? 'selected' : ''); ?>>↓ Desc
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="flex items-center space-x-3">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Filtrer
                                </button>
                                <a href="<?php echo e(route('admin.products')); ?>"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Réinitialiser
                                </a>

                                <?php if($filters['search'] || $filters['brand'] || $filters['has_sales']): ?>
                                    <span class="text-xs text-gray-500 ml-2">
                                        Filtres actifs
                                    </span>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <?php if(!empty($products) && count($products) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Produit
                                        </th>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            SKU
                                        </th>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Marque
                                        </th>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Prix unitaire
                                        </th>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Quantité vendue
                                        </th>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            CA généré
                                        </th>
                                        <th
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr
                                            class="hover:bg-gray-50 <?php echo e($product['has_sales'] ? '' : 'bg-gray-50 opacity-75'); ?>">
                                            <!-- Nom du produit -->
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <?php echo e($product['name'] ?? 'N/A'); ?>

                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            ID WP: <?php echo e($product['id'] ?? 'N/A'); ?>

                                                        </div>
                                                    </div>
                                                    <?php if(!$product['has_sales']): ?>
                                                        <span
                                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                            Aucune vente
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <!-- SKU -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo e($product['sku'] ?? 'N/A'); ?>

                                            </td>

                                            <!-- Marque -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if(!empty($product['brand_slug'])): ?>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        <?php echo e($product['brand_slug']); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-sm text-gray-400">Non assigné</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Prix unitaire -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    <?php echo e(number_format($product['price'], 0, ',', ' ')); ?> CFA
                                                </div>
                                                <?php if(
                                                    $product['has_sales'] &&
                                                        $product['average_price_per_unit'] > 0 &&
                                                        abs($product['price'] - $product['average_price_per_unit']) > 1): ?>
                                                    <div class="text-xs text-gray-500">
                                                        Moy:
                                                        <?php echo e(number_format($product['average_price_per_unit'], 0, ',', ' ')); ?>

                                                        CFA
                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Quantité vendue -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if($product['has_sales']): ?>
                                                    <div class="flex items-center">
                                                        <svg class="h-4 w-4 text-blue-500 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="text-sm font-medium text-gray-900">
                                                            <?php echo e(number_format($product['total_quantity'])); ?>

                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-sm text-gray-400">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- CA généré -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if($product['has_sales']): ?>
                                                    <div class="text-sm font-semibold text-green-700">
                                                        <?php echo e(number_format($product['total_sales'], 0, ',', ' ')); ?> CFA
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-sm text-gray-400">0 CFA</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <?php if($product['real_id']): ?>
                                                    <a href="<?php echo e(route('products.show', $product['real_id'])); ?>"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        Détails
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-gray-400">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <?php echo e($allProducts->links()); ?>

                        </div>
                        
                    <?php else: ?>
                        <!-- État vide -->
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                <?php if($filters['search'] || $filters['brand'] || $filters['has_sales']): ?>
                                    Aucun produit trouvé
                                <?php else: ?>
                                    Aucun produit
                                <?php endif; ?>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                <?php if($filters['search'] || $filters['brand'] || $filters['has_sales']): ?>
                                    Aucun produit ne correspond à vos critères de recherche.
                                <?php else: ?>
                                    Aucun produit n'a été trouvé dans la base de données.
                                <?php endif; ?>
                            </p>
                            <?php if($filters['search'] || $filters['brand'] || $filters['has_sales']): ?>
                                <p class="mt-1 text-xs text-gray-400">
                                    Essayez de modifier vos filtres de recherche.
                                </p>
                            <?php else: ?>
                                <p class="mt-1 text-xs text-gray-400">
                                    Les produits seront automatiquement ajoutés lors de la synchronisation des commandes.
                                </p>
                            <?php endif; ?>
                            <div class="mt-6">
                                <?php if($filters['search'] || $filters['brand'] || $filters['has_sales']): ?>
                                    <a href="<?php echo e(route('admin.products')); ?>"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Réinitialiser les filtres
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('admin.dashboard')); ?>"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                        Retour au tableau de bord
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour afficher les produits d'une marque -->
    <div id="brandProductsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900" id="modalBrandTitle">Produits de la marque</h3>
                        <button type="button" onclick="closeBrandProducts()" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div id="brandProductsContent">
                        <!-- Contenu chargé dynamiquement -->
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button type="button" onclick="closeBrandProducts()"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if(!empty($brands)): ?>
        <script>
            function showBrandProducts(brandSlug) {
                const brandData = <?php echo json_encode($brands, 15, 512) ?>;
                const products = brandData[brandSlug]?.products || [];

                const modalTitle = document.getElementById('modalBrandTitle');
                const modalContent = document.getElementById('brandProductsContent');

                modalTitle.textContent = `Produits - ${brandSlug}`;

                if (products.length > 0) {
                    let html = `
            <div class="space-y-4">
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm font-medium text-gray-500">Total produits</p>
                        <p class="text-lg font-semibold text-gray-900">${products.length}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm font-medium text-gray-500">Stock total</p>
                        <p class="text-lg font-semibold text-gray-900">
                            ${products.reduce((sum, p) => sum + (p.stock_quantity || 0), 0)}
                        </p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm font-medium text-gray-500">Valeur stock</p>
                        <p class="text-lg font-semibold text-gray-900">
                            ${products.reduce((sum, p) => sum + ((p.price || 0) * (p.stock_quantity || 0)), 0).toFixed(2)} CFA
                        </p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500">Produit</th>
                                <th class="px-4 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500">SKU</th>
                                <th class="px-4 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500">Prix</th>
                                <th class="px-4 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500">Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
        `;

                    products.forEach(product => {
                        const stock = product.stock_quantity || 0;
                        const stockClass = stock <= 0 ? 'bg-red-100 text-red-800' :
                            (stock < 20 ? 'bg-yellow-100 text-yellow-800' :
                                'bg-green-100 text-green-800');

                        html += `
                <tr>
                    <td class="px-4 py-2 text-sm">${product.name || 'N/A'}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${product.sku || 'N/A'}</td>
                    <td class="px-4 py-2 text-sm">${(product.price || 0).toFixed(2)} CFA</td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${stockClass}">
                            ${stock}
                        </span>
                    </td>
                </tr>
            `;
                    });

                    html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
                } else {
                    html = `
            <div class="text-center py-8">
                <p class="text-gray-500">Aucun produit trouvé pour cette marque</p>
            </div>
        `;
                }

                modalContent.innerHTML = html;
                document.getElementById('brandProductsModal').classList.remove('hidden');
            }

            function closeBrandProducts() {
                document.getElementById('brandProductsModal').classList.add('hidden');
            }

            function showBrandProducts(brandSlug) {
                window.location.href = `/admin/products?brand=${encodeURIComponent(brandSlug)}`;
            }

            function syncBrands() {
                if (confirm('Voulez-vous synchroniser les marques depuis WordPress ?')) {
                    fetch('/admin/sync/brands', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Synchronisation des marques lancée !');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                alert('Erreur: ' + data.error);
                            }
                        })
                        .catch(error => {
                            alert('Erreur réseau: ' + error.message);
                        });
                }
            }
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/admin/products.blade.php ENDPATH**/ ?>
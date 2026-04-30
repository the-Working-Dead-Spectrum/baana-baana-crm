

<?php $__env->startSection('title', 'Analyse des ventes par produit'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Analyse des ventes par produit</h1>
            <p class="text-gray-600 mt-2">Analysez les performances de vos produits pour optimiser votre catalogue</p>

            <a href="<?php echo e(route('admin.products')); ?>" 
                class="ml-4 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Voir tous mes produits
            </a>
        </div>

        <!-- Filtres avancés -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h5 class="font-medium text-gray-900">Filtres avancés</h5>
                </div>
            </div>
            <div class="p-6">
                <form method="GET" action="#" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Période -->
                        <div>
                            <label for="period" class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                            <select name="period" id="period" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="all" <?php echo e(request('period') == 'all' ? 'selected' : ''); ?>>Toute période</option>
                                <option value="today" <?php echo e(request('period') == 'today' ? 'selected' : ''); ?>>Aujourd'hui</option>
                                <option value="week" <?php echo e(request('period') == 'week' ? 'selected' : ''); ?>>Cette semaine</option>
                                <option value="month" <?php echo e(request('period') == 'month' ? 'selected' : ''); ?>>Ce mois</option>
                                <option value="quarter" <?php echo e(request('period') == 'quarter' ? 'selected' : ''); ?>>Ce trimestre</option>
                                <option value="year" <?php echo e(request('period') == 'year' ? 'selected' : ''); ?>>Cette année</option>
                                <option value="custom" <?php echo e(request('custom_period') ? 'selected' : ''); ?>>Personnalisée</option>
                            </select>
                        </div>

                        <!-- Période personnalisée (conditionnel) -->
                        <div class="lg:col-span-2 custom-period-fields" style="<?php echo e(!request('custom_period') ? 'display: none;' : ''); ?>">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Du</label>
                                    <input type="date" name="date_from" id="date_from" 
                                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                           value="<?php echo e(request('date_from')); ?>">
                                </div>
                                <div>
                                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Au</label>
                                    <input type="date" name="date_to" id="date_to" 
                                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                           value="<?php echo e(request('date_to')); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Marque -->
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Marque</label>
                            <select name="brand" id="brand" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Toutes les marques</option>
                                <?php if(isset($brands) && count($brands) > 0): ?>
                                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($brand); ?>" 
                                                <?php echo e(request('brand') == $brand ? 'selected' : ''); ?>>
                                            <?php echo e(ucfirst($brand)); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <option value="">Aucune marque disponible</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <!-- Statut stock -->
                        <div>
                            <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                            <select name="stock_status" id="stock_status" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Tous</option>
                                <option value="in_stock" <?php echo e(request('stock_status') == 'in_stock' ? 'selected' : ''); ?>>
                                    En stock
                                </option>
                                <option value="low_stock" <?php echo e(request('stock_status') == 'low_stock' ? 'selected' : ''); ?>>
                                    Stock faible
                                </option>
                                <option value="out_of_stock" <?php echo e(request('stock_status') == 'out_of_stock' ? 'selected' : ''); ?>>
                                    Rupture
                                </option>
                            </select>
                        </div>

                        <!-- Tri -->
                        <div>
                            <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                            <select name="sort_by" id="sort_by" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="sales_desc" <?php echo e(request('sort_by') == 'sales_desc' ? 'selected' : ''); ?>>
                                    CA décroissant
                                </option>
                                <option value="sales_asc" <?php echo e(request('sort_by') == 'sales_asc' ? 'selected' : ''); ?>>
                                    CA croissant
                                </option>
                                <option value="quantity_desc" <?php echo e(request('sort_by') == 'quantity_desc' ? 'selected' : ''); ?>>
                                    Quantité décroissante
                                </option>
                                <option value="quantity_asc" <?php echo e(request('sort_by') == 'quantity_asc' ? 'selected' : ''); ?>>
                                    Quantité croissante
                                </option>
                                <option value="name_asc" <?php echo e(request('sort_by') == 'name_asc' ? 'selected' : ''); ?>>
                                    Nom A-Z
                                </option>
                                <option value="name_desc" <?php echo e(request('sort_by') == 'name_desc' ? 'selected' : ''); ?>>
                                    Nom Z-A
                                </option>
                            </select>
                        </div>

                        <!-- Limite -->
                        <div>
                            <label for="limit" class="block text-sm font-medium text-gray-700 mb-2">Nbre résultats</label>
                            <select name="limit" id="limit" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="10" <?php echo e(request('limit', 50) == 10 ? 'selected' : ''); ?>>10</option>
                                <option value="25" <?php echo e(request('limit', 50) == 25 ? 'selected' : ''); ?>>25</option>
                                <option value="50" <?php echo e(request('limit', 50) == 50 ? 'selected' : ''); ?>>50</option>
                                <option value="100" <?php echo e(request('limit', 50) == 100 ? 'selected' : ''); ?>>100</option>
                                <option value="0" <?php echo e(request('limit', 50) == 0 ? 'selected' : ''); ?>>Tous</option>
                            </select>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap justify-between items-center mt-8 pt-6 border-t border-gray-200">
                        <div class="flex space-x-3">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Appliquer les filtres
                            </button>
                            <a href="#" 
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Réinitialiser
                            </a>
                        </div>
                        <div>
                            <button type="button" id="exportBtn" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Exporter CSV
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- KPI et statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Chiffre d'affaires total -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Chiffre d'affaires total</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            <?php echo e(isset($summary['total_sales']) ? number_format($summary['total_sales'], 0, ',', ' ') : '0'); ?> FCFA
                        </p>
                        <small class="text-gray-500"><?php echo e($products->count()); ?> produits analysés</small>
                    </div>
                </div>
            </div>

            <!-- Quantité totale vendue -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Quantité totale vendue</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            <?php echo e(isset($summary['total_quantity']) ? number_format($summary['total_quantity'], 0, ',', ' ') : '0'); ?>

                        </p>
                        <small class="text-gray-500">Unités vendues</small>
                    </div>
                </div>
            </div>

            <!-- Panier moyen -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Panier moyen</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            <?php echo e(isset($summary['average_price']) ? number_format($summary['average_price'], 0, ',', ' ') : '0'); ?> FCFA
                        </p>
                        <small class="text-gray-500">Prix moyen par unité</small>
                    </div>
                </div>
            </div>

            <!-- Produits sans vente -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Produits sans vente</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($summary['no_sales_count'] ?? 0); ?></p>
                        <small class="text-gray-500">Produits à réévaluer</small>
                    </div>
                </div>
            </div>
        </div>

        <?php if($products->isEmpty()): ?>
            <!-- Aucun résultat -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune vente</h3>
                    <p class="mt-1 text-sm text-gray-500">Aucune vente ne correspond à vos critères de recherche.</p>
                    <div class="mt-6">
                        <a href="#" 
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Réinitialiser les filtres
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Tableau principal -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Détail des ventes par produit</h2>
                        <p class="mt-1 text-sm text-gray-500"><?php echo e($products->total() ?? $products->count()); ?> résultats</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Toggle détails avancés -->
                        <div class="flex items-center">
                            <button type="button" id="toggleDetails" 
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 bg-gray-200">
                                <span class="sr-only">Détails avancés</span>
                                <span id="toggleHandle" 
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0"></span>
                            </button>
                            <span class="ml-3 text-sm font-medium text-gray-900">Détails avancés</span>
                        </div>
                        
                        <!-- Menu export -->
                        <div class="relative">
                            <button type="button" 
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    id="export-menu-button" 
                                    aria-expanded="false" 
                                    aria-haspopup="true">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Exporter
                                <svg class="ml-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" 
                                 role="menu" 
                                 id="export-menu">
                                <div class="py-1" role="none">
                                    <a href="#" id="exportCurrent" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">Vue actuelle (CSV)</a>
                                    <a href="#" id="exportAll" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">Toutes les données (CSV)</a>
                                    <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">PDF</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marque</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité vendue</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chiffre d'affaires</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix moyen</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider details-column hidden">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <!-- Produit -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <?php if(isset($product->image_url) && $product->image_url): ?>
                                                    <img class="h-10 w-10 rounded-md object-cover" 
                                                         src="<?php echo e($product->image_url); ?>" 
                                                         alt="<?php echo e($product->name); ?>">
                                                <?php else: ?>
                                                    <div class="h-10 w-10 rounded-md bg-gray-200 flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($product->name); ?></div>
                                                <?php if(isset($product->total_orders) && $product->total_orders): ?>
                                                    <div class="text-xs text-gray-500"><?php echo e($product->total_orders); ?> commande(s)</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- SKU -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-mono"><?php echo e($product->sku ?? 'N/A'); ?></div>
                                    </td>
                                    
                                    <!-- Marque -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo e($product->brand_slug ?? '-'); ?>

                                        </span>
                                    </td>
                                    
                                    <!-- Stock -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                            $stockStatus = $product->stock_quantity ?? 0;
                                            $stockClass = 'bg-gray-100 text-gray-800';
                                            $stockText = 'N/A';
                                            
                                            if ($stockStatus > 10) {
                                                $stockClass = 'bg-green-100 text-green-800';
                                                $stockText = $stockStatus;
                                            } elseif ($stockStatus > 0 && $stockStatus <= 10) {
                                                $stockClass = 'bg-yellow-100 text-yellow-800';
                                                $stockText = $stockStatus;
                                            } elseif ($stockStatus <= 0) {
                                                $stockClass = 'bg-red-100 text-red-800';
                                                $stockText = 'Rupture';
                                            }
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($stockClass); ?>">
                                            <?php echo e($stockText); ?>

                                        </span>
                                    </td>
                                    
                                    <!-- Quantité vendue -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo e(number_format($product->total_quantity ?? 0, 0, ',', ' ')); ?>

                                        </div>
                                        <?php if(isset($product->order_count) && $product->order_count): ?>
                                            <div class="text-xs text-gray-500"><?php echo e($product->order_count); ?> commande(s)</div>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Chiffre d'affaires -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo e(number_format($product->total_sales ?? 0, 0, ',', ' ')); ?> FCFA
                                        </div>
                                        <?php if(isset($product->total_sales) && $product->total_sales && isset($summary['total_sales']) && $summary['total_sales'] > 0): ?>
                                            <?php
                                                $percentage = ($product->total_sales / $summary['total_sales']) * 100;
                                            ?>
                                            <div class="mt-1">
                                                <div class="flex items-center">
                                                    <div class="flex-1">
                                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                                            <div class="h-full bg-green-600 rounded-full" 
                                                                 style="width: <?php echo e(min($percentage, 100)); ?>%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="ml-2 text-xs text-gray-500"><?php echo e(number_format($percentage, 1)); ?>%</div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Prix moyen -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo e(number_format($product->average_unit_price ?? 0, 0, ',', ' ')); ?> FCFA
                                        </div>
                                        <?php if(isset($product->price) && $product->price): ?>
                                            <div class="text-xs text-gray-500">
                                                Actuel: <?php echo e(number_format($product->price, 0, ',', ' ')); ?> FCFA
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Actions (caché par défaut) -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium details-column hidden">
                                        <div class="flex space-x-2">
                                            <?php if(isset($product->id)): ?>
                                                <a href="#" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                </a>
                                            <?php endif; ?>
                                            <a href="#" 
                                               class="text-purple-600 hover:text-purple-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if(method_exists($products, 'hasPages') && $products->hasPages()): ?>
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <?php echo e($products->withQueryString()->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Légende -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Légende</h3>
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                10+
                            </span>
                            <span class="text-sm text-gray-600">Stock > 10</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-2">
                                1-10
                            </span>
                            <span class="text-sm text-gray-600">Stock faible</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                                0
                            </span>
                            <span class="text-sm text-gray-600">Rupture de stock</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-2">
                                N/A
                            </span>
                            <span class="text-sm text-gray-600">Stock inconnu</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la période personnalisée
    const periodSelect = document.getElementById('period');
    const customPeriodFields = document.querySelector('.custom-period-fields');
    
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                if (customPeriodFields) {
                    customPeriodFields.style.display = 'block';
                    // Définir des dates par défaut
                    const today = new Date().toISOString().split('T')[0];
                    const lastMonth = new Date();
                    lastMonth.setMonth(lastMonth.getMonth() - 1);
                    const dateFrom = document.getElementById('date_from');
                    const dateTo = document.getElementById('date_to');
                    if (dateFrom) dateFrom.value = lastMonth.toISOString().split('T')[0];
                    if (dateTo) dateTo.value = today;
                }
            } else {
                if (customPeriodFields) customPeriodFields.style.display = 'none';
                const dateFrom = document.getElementById('date_from');
                const dateTo = document.getElementById('date_to');
                if (dateFrom) dateFrom.value = '';
                if (dateTo) dateTo.value = '';
            }
        });
    }

    // Toggle des colonnes détaillées
    const toggleButton = document.getElementById('toggleDetails');
    const toggleHandle = document.getElementById('toggleHandle');
    const detailColumns = document.querySelectorAll('.details-column');
    
    if (toggleButton) {
        toggleButton.addEventListener('click', function() {
            const isChecked = toggleButton.classList.contains('bg-blue-600');
            
            if (isChecked) {
                // Désactiver
                toggleButton.classList.remove('bg-blue-600');
                toggleButton.classList.add('bg-gray-200');
                toggleHandle.style.transform = 'translateX(0)';
                detailColumns.forEach(col => {
                    col.classList.add('hidden');
                });
            } else {
                // Activer
                toggleButton.classList.remove('bg-gray-200');
                toggleButton.classList.add('bg-blue-600');
                toggleHandle.style.transform = 'translateX(1.25rem)';
                detailColumns.forEach(col => {
                    col.classList.remove('hidden');
                });
            }
        });
    }

    // Menu déroulant pour l'export
    const exportMenuButton = document.getElementById('export-menu-button');
    const exportMenu = document.getElementById('export-menu');
    
    if (exportMenuButton && exportMenu) {
        exportMenuButton.addEventListener('click', function() {
            exportMenu.classList.toggle('hidden');
        });
        
        // Fermer le menu en cliquant ailleurs
        document.addEventListener('click', function(event) {
            if (!exportMenuButton.contains(event.target) && !exportMenu.contains(event.target)) {
                exportMenu.classList.add('hidden');
            }
        });
    }

    // Export CSV
    const exportCurrent = document.getElementById('exportCurrent');
    const exportAll = document.getElementById('exportAll');
    
    if (exportCurrent) {
        exportCurrent.addEventListener('click', function(e) {
            e.preventDefault();
            exportMenu.classList.add('hidden');
            alert('Fonctionnalité d\'export CSV à implémenter');
        });
    }
    
    if (exportAll) {
        exportAll.addEventListener('click', function(e) {
            e.preventDefault();
            exportMenu.classList.add('hidden');
            alert('Fonctionnalité d\'export CSV à implémenter');
        });
    }

    // Calcul automatique de la période
    function setPeriodDates(period) {
        const today = new Date();
        let dateFrom, dateTo;
        
        switch(period) {
            case 'today':
                dateFrom = dateTo = today.toISOString().split('T')[0];
                break;
            case 'week':
                const startOfWeek = new Date(today);
                startOfWeek.setDate(today.getDate() - today.getDay());
                dateFrom = startOfWeek.toISOString().split('T')[0];
                dateTo = today.toISOString().split('T')[0];
                break;
            case 'month':
                dateFrom = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                dateTo = today.toISOString().split('T')[0];
                break;
            case 'quarter':
                const quarter = Math.floor(today.getMonth() / 3);
                dateFrom = new Date(today.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
                dateTo = today.toISOString().split('T')[0];
                break;
            case 'year':
                dateFrom = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                dateTo = today.toISOString().split('T')[0];
                break;
        }
        
        if (dateFrom && dateTo && periodSelect && customPeriodFields) {
            periodSelect.value = 'custom';
            customPeriodFields.style.display = 'block';
            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');
            if (dateFromInput) dateFromInput.value = dateFrom;
            if (dateToInput) dateToInput.value = dateTo;
        }
    }

    // Appliquer la période au chargement si sélectionnée
    <?php if(request('period') && request('period') != 'custom' && request('period') != 'all'): ?>
        setPeriodDates("<?php echo e(request('period')); ?>");
    <?php endif; ?>
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/admin/products/sales.blade.php ENDPATH**/ ?>
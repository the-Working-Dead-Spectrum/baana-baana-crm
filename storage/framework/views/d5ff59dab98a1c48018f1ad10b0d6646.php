

<?php $__env->startSection('title', 'Dashboard Créateur'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Créateur</h1>
                    <p class="text-gray-600 mt-2">Bienvenue, <?php echo e($creator->name); ?> !</p>
                    <p class="text-sm text-gray-500 mt-1">Marque: <?php echo e($creator->brand_name); ?> (<?php echo e($creator->brand_slug); ?>)</p>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if($apiConnected): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            API Connectée
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            API Déconnectée
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        <?php if(($alerts['pending_orders'] ?? 0) > 0 || ($alerts['low_performing_products'] ?? 0) > 0 || ($alerts['no_sales_this_month'] ?? false)): ?>
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Notifications</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <?php if(($alerts['pending_orders'] ?? 0) > 0): ?>
                                    <li><?php echo e($alerts['pending_orders']); ?> commande(s) en attente depuis plus de 24h</li>
                                <?php endif; ?>
                                <?php if(($alerts['low_performing_products'] ?? 0) > 0): ?>
                                    <li><?php echo e($alerts['low_performing_products']); ?> produit(s) peu performant(s)</li>
                                <?php endif; ?>
                                <?php if($alerts['no_sales_this_month'] ?? false): ?>
                                    <li>Aucune vente ce mois-ci</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Stats Cards - Ligne 1 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Sales -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500"><?php echo e($personalStats['total_sales']['label'] ?? 'Chiffre d\'affaires total'); ?></p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e(number_format($personalStats['total_sales']['value'] ?? 0, 0, ',', ' ')); ?> CFA</p>
                    </div>
                </div>
            </div>

            <!-- Month Sales -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500"><?php echo e($personalStats['month_sales']['label'] ?? 'CA du mois'); ?></p>
                        <div class="flex items-center justify-between">
                            <p class="text-2xl font-semibold text-gray-900"><?php echo e(number_format($personalStats['month_sales']['value'] ?? 0, 0, ',', ' ')); ?> CFA</p>
                            <?php if(isset($personalStats['month_sales']['growth'])): ?>
                                <span class="inline-flex items-center text-sm font-medium <?php echo e(($personalStats['month_sales']['growth']['trend'] ?? 'neutral') == 'up' ? 'text-green-600' : (($personalStats['month_sales']['growth']['trend'] ?? 'neutral') == 'down' ? 'text-red-600' : 'text-gray-600')); ?>">
                                    <?php if(($personalStats['month_sales']['growth']['trend'] ?? 'neutral') == 'up'): ?>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    <?php elseif(($personalStats['month_sales']['growth']['trend'] ?? 'neutral') == 'down'): ?>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    <?php endif; ?>
                                    <?php echo e(abs($personalStats['month_sales']['growth']['percentage'] ?? 0)); ?>%
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Orders -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 01118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500"><?php echo e($personalStats['total_orders']['label'] ?? 'Commandes totales'); ?></p>
                        <div class="flex items-center justify-between">
                            <p class="text-2xl font-semibold text-gray-900"><?php echo e($personalStats['total_orders']['value'] ?? 0); ?></p>
                            <?php if(isset($personalStats['total_orders']['growth'])): ?>
                                <span class="inline-flex items-center text-sm font-medium <?php echo e(($personalStats['total_orders']['growth']['trend'] ?? 'neutral') == 'up' ? 'text-green-600' : (($personalStats['total_orders']['growth']['trend'] ?? 'neutral') == 'down' ? 'text-red-600' : 'text-gray-600')); ?>">
                                    <?php if(($personalStats['total_orders']['growth']['trend'] ?? 'neutral') == 'up'): ?>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    <?php elseif(($personalStats['total_orders']['growth']['trend'] ?? 'neutral') == 'down'): ?>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    <?php endif; ?>
                                    <?php echo e(abs($personalStats['total_orders']['growth']['percentage'] ?? 0)); ?>%
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e($personalStats['total_orders']['month_value'] ?? 0); ?> ce mois</p>
                    </div>
                </div>
            </div>

            <!-- Average Order Value -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500"><?php echo e($personalStats['average_order_value']['label'] ?? 'Panier moyen'); ?></p>
                        <div class="flex items-center justify-between">
                            <p class="text-2xl font-semibold text-gray-900"><?php echo e(number_format($personalStats['average_order_value']['value'] ?? 0, 0, ',', ' ')); ?> CFA</p>
                            <?php if(isset($personalStats['average_order_value']['growth'])): ?>
                                <span class="inline-flex items-center text-sm font-medium <?php echo e(($personalStats['average_order_value']['growth']['trend'] ?? 'neutral') == 'up' ? 'text-green-600' : (($personalStats['average_order_value']['growth']['trend'] ?? 'neutral') == 'down' ? 'text-red-600' : 'text-gray-600')); ?>">
                                    <?php if(($personalStats['average_order_value']['growth']['trend'] ?? 'neutral') == 'up'): ?>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    <?php elseif(($personalStats['average_order_value']['growth']['trend'] ?? 'neutral') == 'down'): ?>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    <?php endif; ?>
                                    <?php echo e(abs($personalStats['average_order_value']['growth']['percentage'] ?? 0)); ?>%
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards - Ligne 2 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Products Sold -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500"><?php echo e($personalStats['products_sold']['label'] ?? 'Produits vendus'); ?></p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($personalStats['products_sold']['value'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <!-- Unique Customers -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-pink-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500"><?php echo e($personalStats['unique_customers']['label'] ?? 'Clients uniques'); ?></p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($personalStats['unique_customers']['value'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <!-- Today Stats -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Aujourd'hui</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($personalStats['today_orders'] ?? 0); ?> commandes</p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e(number_format($personalStats['today_sales'] ?? 0, 0, ',', ' ')); ?> CFA</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Meilleurs produits -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Meilleurs Produits</h2>
                    <a href="<?php echo e(route('creator.products')); ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                        Voir tous →
                    </a>
                </div>
                
                <?php if(!empty($creatorProducts['best_selling_products']) && count($creatorProducts['best_selling_products']) > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $creatorProducts['best_selling_products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900"><?php echo e($product['name'] ?? 'Nom inconnu'); ?></h4>
                                    <p class="text-xs text-gray-500"><?php echo e($product['sku'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900"><?php echo e(number_format($product['total_sales'] ?? 0, 0, ',', ' ')); ?> CFA</p>
                                    <p class="text-xs text-gray-500"><?php echo e($product['total_quantity'] ?? 0); ?> vendus</p>
                                    <p class="text-xs text-gray-400"><?php echo e($product['order_count'] ?? 0); ?> commandes</p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <p>Aucun produit vendu pour le moment</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Commandes récentes locales -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Commandes Récentes</h2>
                    <a href="<?php echo e(route('creator.orders')); ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                        Voir toutes →
                    </a>
                </div>
                
                <?php if(!empty($recentLocalOrders) && count($recentLocalOrders) > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $recentLocalOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="border-l-4 border-<?php echo e(($order['status'] ?? 'pending') == 'completed' ? 'green' : (($order['status'] ?? 'pending') == 'pending' ? 'yellow' : 'gray')); ?>-400 pl-4 py-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900"><?php echo e($order['order_number'] ?? 'N/A'); ?></h4>
                                        <p class="text-xs text-gray-500"><?php echo e($order['customer_name'] ?? 'Client inconnu'); ?></p>
                                        <p class="text-xs text-gray-400"><?php echo e($order['date'] ?? 'Date inconnue'); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-900"><?php echo e(number_format($order['total'] ?? 0, 0, ',', ' ')); ?> CFA</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-<?php echo e(($order['status'] ?? 'pending') == 'completed' ? 'green' : (($order['status'] ?? 'pending') == 'pending' ? 'yellow' : 'gray')); ?>-100 text-<?php echo e(($order['status'] ?? 'pending') == 'completed' ? 'green' : (($order['status'] ?? 'pending') == 'pending' ? 'yellow' : 'gray')); ?>-800">
                                            <?php echo e(ucfirst($order['status'] ?? 'pending')); ?>

                                        </span>
                                    </div>
                                </div>
                                <?php if(!empty($order['items']) && count($order['items']) > 0): ?>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span><?php echo e($item['name'] ?? 'Produit'); ?> (x<?php echo e($item['quantity'] ?? 0); ?>)</span><?php echo e(!$loop->last ? ', ' : ''); ?>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune commande</h3>
                        <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore de commandes.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Brand Info -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations sur votre marque</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Nom de la marque</p>
                    <p class="text-base font-medium text-gray-900"><?php echo e($creator->brand_name); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Slug de la marque</p>
                    <p class="text-base font-medium text-gray-900"><?php echo e($creator->brand_slug); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nombre de produits</p>
                    <p class="text-base font-medium text-gray-900"><?php echo e($creatorProducts['total_products'] ?? 0); ?> produits</p>
                    <p class="text-xs text-gray-500"><?php echo e($creatorProducts['products_with_sales'] ?? 0); ?> avec ventes</p>
                </div>
            </div>
            <?php if($creator->description): ?>
                <div class="mt-4">
                    <p class="text-sm text-gray-500">Description</p>
                    <p class="text-base text-gray-900 mt-1"><?php echo e($creator->description); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Liens rapides -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Accès rapide</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="<?php echo e(route('creator.products')); ?>" class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                    <svg class="h-8 w-8 mx-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="mt-2 text-sm font-medium text-gray-900">Produits</p>
                    <p class="text-xs text-gray-500"><?php echo e($creatorProducts['total_products'] ?? 0); ?> produits</p>
                </a>
                <a href="<?php echo e(route('creator.orders')); ?>" class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                    <svg class="h-8 w-8 mx-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-2 text-sm font-medium text-gray-900">Commandes</p>
                    <p class="text-xs text-gray-500"><?php echo e($personalStats['total_orders']['value'] ?? 0); ?> commandes</p>
                </a>
                <a href="<?php echo e(route('creator.analytics')); ?>" class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                    <svg class="h-8 w-8 mx-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="mt-3 text-sm font-medium text-gray-900">Analytiques</p>
                    <p class="text-xs text-gray-500">Statistiques détaillées</p>
                </a>
                <a href="<?php echo e(route('creator.profile')); ?>" class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                    <svg class="h-8 w-8 mx-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <p class="mt-2 text-sm font-medium text-gray-900">Profil</p>
                    <p class="text-xs text-gray-500">Gérer votre compte</p>
                </a>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        // Préparer les données de manière sécurisée
        const last7DaysStats = <?php echo json_encode($last7DaysStats ?? [], 15, 512) ?>;
        
        if (last7DaysStats && last7DaysStats.length > 0) {
            // Extraire les données de manière sécurisée
            const labels = last7DaysStats.map(item => item.label || '');
            const salesData = last7DaysStats.map(item => item.sales || 0);
            const ordersData = last7DaysStats.map(item => item.orders || 0);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventes (CFA)',
                        data: salesData,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y'
                    }, {
                        label: 'Commandes',
                        data: ordersData,
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.3,
                        fill: true,
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
                                text: 'Commandes'
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
                                            label += context.parsed.y + ' commande' + (context.parsed.y > 1 ? 's' : '');
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/creator/dashboard.blade.php ENDPATH**/ ?>
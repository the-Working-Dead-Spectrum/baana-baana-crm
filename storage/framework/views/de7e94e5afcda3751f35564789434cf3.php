<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Tableau de Bord Admin</h1>
                <p class="text-gray-600 mt-2">Vue d'ensemble de votre plateforme</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

                <!-- KPI 1: Chiffre d'affaires total -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500"><?php echo e($kpis['total_revenue']['label']); ?></h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-1">
                        <?php echo e(number_format($kpis['total_revenue']['value'], 0, ',', ' ')); ?> CFA
                    </p>
                    <p class="text-xs text-gray-500">Basé sur les commandes complétées</p>
                </div>

                <!-- KPI 2: CA du mois -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500"><?php echo e($kpis['month_revenue']['label']); ?></h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-1">
                        <?php echo e(number_format($kpis['month_revenue']['value'], 0, ',', ' ')); ?> CFA
                    </p>
                    <div class="flex items-center text-xs text-gray-600">
                        <?php if($kpis['month_revenue']['growth']['trend'] === 'up'): ?>
                            <svg class="h-4 w-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span
                                class="text-green-600 font-medium">+<?php echo e($kpis['month_revenue']['growth']['percentage']); ?>%</span>
                            <span class="ml-1">vs mois dernier</span>
                        <?php elseif($kpis['month_revenue']['growth']['trend'] === 'down'): ?>
                            <svg class="h-4 w-4 mr-1 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span
                                class="text-red-600 font-medium"><?php echo e($kpis['month_revenue']['growth']['percentage']); ?>%</span>
                            <span class="ml-1">vs mois dernier</span>
                        <?php else: ?>
                            <span class="text-gray-500">Stable</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- KPI 3: Commandes complétées -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500"><?php echo e($kpis['completed_orders']['label']); ?></h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-1"><?php echo e(number_format($kpis['completed_orders']['value'])); ?>

                    </p>
                    <div class="flex items-center text-xs text-gray-600">
                        <span><?php echo e($kpis['completed_orders']['month_value']); ?> ce mois</span>
                        <?php if($kpis['completed_orders']['growth']['percentage'] != 0): ?>
                            <span
                                class="ml-2 px-2 py-0.5 <?php echo e($kpis['completed_orders']['growth']['percentage'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?> rounded text-xs font-medium">
                                <?php echo e($kpis['completed_orders']['growth']['percentage'] > 0 ? '+' : ''); ?><?php echo e($kpis['completed_orders']['growth']['percentage']); ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- KPI 4: Panier moyen -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500"><?php echo e($kpis['average_order_value']['label']); ?></h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-1">
                        <?php echo e(number_format($kpis['average_order_value']['value'], 0, ',', ' ')); ?> CFA
                    </p>
                    <div class="flex items-center text-xs text-gray-600">
                        <span><?php echo e(number_format($kpis['average_order_value']['month_value'], 0, ',', ' ')); ?> CFA ce
                            mois</span>
                        <?php if($kpis['average_order_value']['growth']['percentage'] != 0): ?>
                            <span
                                class="ml-2 px-2 py-0.5 <?php echo e($kpis['average_order_value']['growth']['percentage'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?> rounded text-xs font-medium">
                                <?php echo e($kpis['average_order_value']['growth']['percentage'] > 0 ? '+' : ''); ?><?php echo e($kpis['average_order_value']['growth']['percentage']); ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- KPI 5: Taux de conversion -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500"><?php echo e($kpis['conversion_rate']['label']); ?></h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-1">
                        <?php echo e(number_format($kpis['conversion_rate']['value'], 1)); ?>%</p>
                    <div class="flex items-center text-xs text-gray-600">
                        <span><?php echo e(number_format($kpis['conversion_rate']['month_value'], 1)); ?>% ce mois</span>
                        <?php if($kpis['conversion_rate']['growth']['percentage'] != 0): ?>
                            <span
                                class="ml-2 px-2 py-0.5 <?php echo e($kpis['conversion_rate']['growth']['percentage'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?> rounded text-xs font-medium">
                                <?php echo e($kpis['conversion_rate']['growth']['percentage'] > 0 ? '+' : ''); ?><?php echo e($kpis['conversion_rate']['growth']['percentage']); ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- KPI 6: Clients uniques -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-shrink-0 bg-pink-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500"><?php echo e($kpis['total_customers']['label']); ?></h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-1">
                        <?php echo e(number_format($kpis['total_customers']['value'])); ?></p>
                    <div class="flex items-center text-xs text-gray-600">
                        <span><?php echo e($kpis['total_customers']['month_value']); ?> nouveaux ce mois</span>
                        <?php if($kpis['total_customers']['growth']['percentage'] != 0): ?>
                            <span
                                class="ml-2 px-2 py-0.5 <?php echo e($kpis['total_customers']['growth']['percentage'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?> rounded text-xs font-medium">
                                <?php echo e($kpis['total_customers']['growth']['percentage'] > 0 ? '+' : ''); ?><?php echo e($kpis['total_customers']['growth']['percentage']); ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- KPI Bonus: Fidélisation -->
            <div class="mb-8 bg-white overflow-hidden shadow-lg sm:rounded-lg p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-500 mb-2"><?php echo e($kpis['customer_retention']['label']); ?></h3>
                        <p class="text-4xl font-bold text-gray-900">
                            <?php echo e(number_format($kpis['customer_retention']['value'], 1)); ?>%</p>
                        <p class="text-sm text-gray-600 mt-2">
                            <?php echo e($kpis['customer_retention']['repeat_customers']); ?> clients fidèles sur
                            <?php echo e($kpis['total_customers']['value']); ?> clients uniques
                        </p>
                    </div>
                    <div class="flex-shrink-0 ml-6">
                        <div class="h-20 w-20 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="h-12 w-12 text-red-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats rapides du jour -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Aujourd'hui -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Aujourd'hui</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1"><?php echo e($kpis['today_completed']); ?></p>
                            <p class="text-xs text-gray-400 mt-1">commandes complétées</p>
                        </div>
                        <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- CA aujourd'hui -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">CA aujourd'hui</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1">
                                <?php echo e(number_format($kpis['today_revenue'], 0, ',', ' ')); ?> CFA
                            </p>
                            <p class="text-xs text-gray-400 mt-1">ventes du jour</p>
                        </div>
                        <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- En attente -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">En attente</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1"><?php echo e($localStats['pending_orders']); ?></p>
                            <p class="text-xs text-gray-400 mt-1">commandes à traiter</p>
                        </div>
                        <div class="h-12 w-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique des 7 derniers jours -->
            

            <!-- Top créateurs -->
            

            
            <br>
            <!-- Deux colonnes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Derniers créateurs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Derniers Créateurs</h2>
                            <a href="<?php echo e(route('admin.creators')); ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                                Voir tous →
                            </a>
                        </div>
                        <?php if(isset($recentCreators)): ?>
                            <?php if($recentCreators->count() > 0): ?>
                                <div class="space-y-4">
                                    <?php $__currentLoopData = $recentCreators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $creator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div
                                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-blue-600 font-semibold">
                                                            <?php echo e(strtoupper(substr($creator->name, 0, 1))); ?>

                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium text-gray-900"><?php echo e($creator->name); ?></p>
                                                    <p class="text-xs text-gray-500"><?php echo e($creator->email); ?></p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php echo e($creator->status === 'active'
                                                ? 'bg-green-100 text-green-800'
                                                : ($creator->status === 'inactive'
                                                    ? 'bg-gray-100 text-gray-800'
                                                    : 'bg-red-100 text-red-800')); ?>">
                                                    <?php echo e($creator->status === 'active' ? 'Actif' : ($creator->status === 'inactive' ? 'Inactif' : 'Suspendu')); ?>

                                                </span>
                                                <p class="text-xs text-gray-500 mt-1"><?php echo e($creator->brand_slug); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-8">
                                    <p class="text-gray-500">Aucun créateur trouvé</p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Actions Rapides</h2>

                        <div class="space-y-4">
                            <a href="<?php echo e(route('admin.creators')); ?>"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0H21m-4.5 0H15m4.5 0h.008v.008h-.008V15zm0 0h.008v.008h-.008V15z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Gérer les créateurs</p>
                                        <p class="text-xs text-gray-500">Ajouter, modifier ou suspendre des créateurs</p>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            <a href="<?php echo e(route('admin.orders')); ?>"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Voir les commandes</p>
                                        <p class="text-xs text-gray-500">Consulter toutes les commandes</p>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            <a href="<?php echo e(route('admin.reports.product-sales')); ?>"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Produits et stocks</p>
                                        <p class="text-xs text-gray-500">Voir les ventes par produit</p>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            <a href="<?php echo e(route('admin.settings')); ?>"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Paramètres</p>
                                        <p class="text-xs text-gray-500">Configurer WordPress, webhooks, etc.</p>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Synchronisation -->
            

            <!-- Connexion WordPress -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Statut WordPress</h3>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                        <?php echo e($apiConnected ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                            <?php echo e($apiConnected ? 'Connecté' : 'Non connecté'); ?>

                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div
                                class="h-3 w-3 rounded-full 
                            <?php echo e($apiConnected ? 'bg-green-500' : 'bg-red-500'); ?> 
                            mr-2">
                            </div>
                            <span class="text-sm text-gray-700">
                                <?php if(config('services.wordpress.url')): ?>
                                    <?php echo e(config('services.wordpress.url')); ?>

                                <?php else: ?>
                                    Non configuré
                                <?php endif; ?>
                            </span>
                        </div>
                        <button id="testWordPressBtn"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Tester la connexion
                        </button>
                    </div>
                    <div id="testResult" class="mt-4 hidden"></div>
                </div>
            </div>

            <!-- Dernières commandes synchronisées -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Dernières commandes synchronisées</h3>
                        <button onclick="refreshRecentOrders()"
                            class="inline-flex items-center px-3 py-1 bg-gray-100 border border-gray-300 rounded-md text-xs text-gray-700 hover:bg-gray-200">
                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Actualiser
                        </button>
                    </div>

                    <div id="recentOrdersContainer">
                        <!-- Les commandes seront chargées via AJAX -->
                        <div class="text-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="mt-2 text-gray-500">Chargement des commandes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tester la connexion WordPress
        document.getElementById('testWordPressBtn').addEventListener('click', testWordPressConnection);

        // Charger les commandes récentes au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentOrders();
        });

        // Tester la connexion WordPress
        function testWordPressConnection() {
            const btn = document.getElementById('testWordPressBtn');
            const originalText = btn.textContent;

            btn.disabled = true;
            btn.textContent = 'Test en cours...';

            fetch('<?php echo e(route('admin.settings.test')); ?>')
                .then(response => response.json())
                .then(data => {
                    const resultDiv = document.getElementById('testResult');
                    resultDiv.classList.remove('hidden');

                    if (data.success) {
                        resultDiv.innerHTML = `
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Connexion réussie !</span>
                        </div>
                        <p class="mt-1">WordPress API est opérationnel.</p>
                        ${data.data?.url ? `<p class="text-sm mt-2">URL: ${data.data.url}</p>` : ''}
                    </div>
                `;
                    } else {
                        resultDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Échec de connexion</span>
                        </div>
                        <p class="mt-1">${data.error || 'Erreur inconnue'}</p>
                    </div>
                `;
                    }
                })
                .catch(error => {
                    const resultDiv = document.getElementById('testResult');
                    resultDiv.classList.remove('hidden');
                    resultDiv.innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <p class="font-medium">Erreur réseau</p>
                    <p class="mt-1">${error.message}</p>
                </div>
            `;
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = originalText;
                });
        }

        // Déclencher une synchronisation
        function triggerSync(type = 'orders', force = false) {
            if (!confirm(`Voulez-vous lancer une synchronisation ${force ? 'complète' : 'incrémentielle'} des ${type} ?`)) {
                return;
            }

            const syncBtn = event.target;
            const originalText = syncBtn.innerHTML;

            syncBtn.disabled = true;
            syncBtn.innerHTML = `
        <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Lancement...
    `;

            // Afficher la barre de progression
            const progressDiv = document.getElementById('syncProgress');
            const progressBar = document.getElementById('syncProgressBar');
            const progressPercent = document.getElementById('syncProgressPercent');

            progressDiv.classList.remove('hidden');
            progressBar.style.width = '10%';
            progressPercent.textContent = '10%';

            fetch('<?php echo e(route('admin.orders.sync')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({
                        type: type,
                        force: force
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const resultDiv = document.getElementById('syncResult');
                    resultDiv.classList.remove('hidden');

                    if (data.success) {
                        resultDiv.innerHTML = `
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Synchronisation lancée</span>
                    </div>
                    <p class="mt-1">${data.message}</p>
                </div>
            `;

                        // Simuler la progression
                        simulateProgress(progressBar, progressPercent, syncBtn, originalText);

                        // Recharger les commandes après un délai
                        setTimeout(() => {
                            loadRecentOrders();
                            // Rafraîchir la page après 30 secondes pour voir les nouvelles stats
                            setTimeout(() => {
                                window.location.reload();
                            }, 30000);
                        }, 2000);

                    } else {
                        resultDiv.innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Échec</span>
                    </div>
                    <p class="mt-1">${data.error || 'Erreur inconnue'}</p>
                </div>
            `;

                        syncBtn.disabled = false;
                        syncBtn.innerHTML = originalText;
                        progressDiv.classList.add('hidden');
                    }
                })
                .catch(error => {
                    const resultDiv = document.getElementById('syncResult');
                    resultDiv.classList.remove('hidden');
                    resultDiv.innerHTML = `
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <p class="font-medium">Erreur réseau</p>
                <p class="mt-1">${error.message}</p>
            </div>
        `;

                    syncBtn.disabled = false;
                    syncBtn.innerHTML = originalText;
                    progressDiv.classList.add('hidden');
                });
        }

        // Simuler la progression de la synchronisation
        function simulateProgress(progressBar, progressPercent, button, originalText) {
            let progress = 10;
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 95) {
                    progress = 95; // Ne jamais atteindre 100% (attente job réel)
                }

                progressBar.style.width = progress + '%';
                progressPercent.textContent = Math.round(progress) + '%';

                if (progress >= 95) {
                    clearInterval(interval);
                    // Le bouton reste désactivé jusqu'à la fin réelle
                }
            }, 500);
        }

        // Charger les commandes récentes
        function loadRecentOrders() {
            const container = document.getElementById('recentOrdersContainer');

            fetch('<?php echo e(route('admin.api.recent-orders')); ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        let html = `
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commande</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Créateurs</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                `;

                        data.data.forEach(order => {
                            html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${order.order_number || '#' + order.id}</div>
                                <div class="text-xs text-gray-500">${order.date}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${order.customer_name || 'N/A'}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${formatCurrency(order.total)}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-700">${order.creators || 'Aucun'}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    ${getStatusColor(order.status)}">
                                    ${formatStatus(order.status)}
                                </span>
                            </td>
                        </tr>
                    `;
                        });

                        html += `
                            </tbody>
                        </table>
                    </div>
                `;

                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune commande</h3>
                        <p class="mt-1 text-sm text-gray-500">Les commandes apparaîtront ici après synchronisation.</p>
                    </div>
                `;
                    }
                })
                .catch(error => {
                    container.innerHTML = `
                <div class="text-center py-8">
                    <div class="text-red-600">
                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <p class="mt-2">Erreur lors du chargement</p>
                    </div>
                </div>
            `;
                });
        }

        // Rafraîchir les commandes récentes
        function refreshRecentOrders() {
            loadRecentOrders();
        }

        // Helper functions
        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XOF',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        function formatStatus(status) {
            const statusMap = {
                'pending': 'En attente',
                'processing': 'En traitement',
                'completed': 'Terminée',
                'cancelled': 'Annulée',
                'refunded': 'Remboursée'
            };
            return statusMap[status] || status;
        }

        function getStatusColor(status) {
            const colorMap = {
                'completed': 'bg-green-100 text-green-800',
                'processing': 'bg-blue-100 text-blue-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'cancelled': 'bg-red-100 text-red-800',
                'refunded': 'bg-gray-100 text-gray-800'
            };
            return colorMap[status] || 'bg-gray-100 text-gray-800';
        }
    </script>
<?php $__env->stopSection(); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let salesChart;
    let chartType = 'sales';

    document.addEventListener('DOMContentLoaded', function() {
        initChart();
    });

    function initChart() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const data = <?php echo json_encode($last7DaysStats, 15, 512) ?>;

        const chartData = {
            labels: data.map(d => d.label),
            datasets: [{
                label: chartType === 'sales' ? 'Ventes (CFA)' : 'Commandes',
                data: chartType === 'sales' ? data.map(d => d.sales) : data.map(d => d.orders),
                borderColor: chartType === 'sales' ? '#3B82F6' : '#10B981',
                backgroundColor: chartType === 'sales' ? 'rgba(59, 130, 246, 0.1)' :
                    'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        };

        if (salesChart) {
            salesChart.destroy();
        }

        salesChart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (chartType === 'sales') {
                                    return new Intl.NumberFormat('fr-FR', {
                                        style: 'currency',
                                        currency: 'XOF',
                                        minimumFractionDigits: 0
                                    }).format(context.raw);
                                } else {
                                    return context.raw + ' commande(s)';
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (chartType === 'sales') {
                                    if (value >= 1000) {
                                        return (value / 1000) + 'k';
                                    }
                                    return value;
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }

    function toggleChartType(type) {
        chartType = type;

        // Update button styles
        document.querySelectorAll('button[onclick^="toggleChartType"]').forEach(btn => {
            if (btn.textContent.includes(type === 'sales' ? 'Ventes' : 'Commandes')) {
                btn.classList.remove('bg-gray-100', 'text-gray-700');
                btn.classList.add('bg-blue-100', 'text-blue-700');
            } else {
                btn.classList.remove('bg-blue-100', 'text-blue-700');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            }
        });

        initChart();
    }
</script>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>
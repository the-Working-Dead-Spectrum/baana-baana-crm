

<?php $__env->startSection('title', 'Synchronisation'); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages de session -->
            <?php if(session('success')): ?>
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Succès!</strong>
                    <span class="block sm:inline"><?php echo session('success'); ?></span>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Erreur!</strong>
                    <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?>

            <!-- Loader overlay -->
            <div id="sync-loader" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                            <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Synchronisation en cours...</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500" id="sync-status">Préparation de la synchronisation...</p>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full animate-pulse" style="width: 45%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Synchronisation des données</h1>
                <p class="text-gray-600 mt-2">Gérez la synchronisation entre WordPress et le CRM</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Commandes</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo e(number_format($syncStats['total_orders'])); ?></p>
                            <p class="text-xs text-gray-500 mt-1"><?php echo e($syncStats['today_orders']); ?> aujourd'hui</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Créateurs</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo e(number_format($syncStats['total_creators'])); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Produits</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo e(number_format($syncStats['total_products'])); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Dernière sync</p>
                            <p class="text-lg font-semibold text-gray-900">
                                <?php if($syncStats['last_sync']): ?>
                                    <?php echo e($syncStats['last_sync']->started_at->diffForHumans()); ?>

                                <?php else: ?>
                                    Jamais
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions de sync -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions de synchronisation</h2>

                    <form method="POST" action="<?php echo e(route('admin.sync.run')); ?>" id="sync-form" class="space-y-4">
                        <?php echo csrf_field(); ?>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de synchronisation</label>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="type" value="all" checked class="mr-3">
                                    <div>
                                        <span class="font-medium">Tout synchroniser</span>
                                        <p class="text-xs text-gray-500 mt-1">Commandes, produits et créateurs</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="type" value="orders" class="mr-3">
                                    <div>
                                        <span class="font-medium">Commandes</span>
                                        <p class="text-xs text-gray-500 mt-1">Seulement les commandes</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="type" value="products" class="mr-3">
                                    <div>
                                        <span class="font-medium">Produits</span>
                                        <p class="text-xs text-gray-500 mt-1">Seulement les produits</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="type" value="creators" class="mr-3">
                                    <div>
                                        <span class="font-medium">Créateurs</span>
                                        <p class="text-xs text-gray-500 mt-1">Seulement les créateurs</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <input type="checkbox" name="force" id="force_sync" value="1" class="h-4 w-4 text-yellow-600">
                            <label for="force_sync" class="ml-3">
                                <span class="text-sm font-medium text-yellow-800">Forcer une synchronisation complète</span>
                                <p class="text-xs text-yellow-600 mt-1">Ignore les dates de modification et synchronise toutes les données</p>
                            </label>
                        </div>

                        <div class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <input type="checkbox" name="use_queue" id="use_queue" value="1" class="h-4 w-4 text-blue-600">
                            <label for="use_queue" class="ml-3">
                                <span class="text-sm font-medium text-blue-800">Utiliser la file d'attente (queue)</span>
                                <p class="text-xs text-blue-600 mt-1">
                                    ⚠️ <strong>Recommandé seulement si vous avez un worker actif</strong> (<code>php artisan queue:work --queue=sync</code>).
                                    <br>Sinon, laissez <strong>décoché</strong> pour une exécution immédiate.
                                </p>
                            </label>
                        </div>

                        <div class="flex items-center space-x-4">
                            <button type="submit" id="sync-btn"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Lancer la synchronisation
                            </button>

                            <a href="<?php echo e(route('admin.sync.logs')); ?>"
                                class="inline-flex items-center px-4 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                Voir les logs
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Queue Status -->
            <?php if(isset($syncStats['queue_status']['warning'])): ?>
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-8">
                    <strong>⚠️ Attention:</strong> <?php echo e($syncStats['queue_status']['warning']); ?>

                </div>
            <?php endif; ?>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">État de la file d'attente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-gray-500">Jobs en attente</p>
                            <p class="text-2xl font-semibold <?php echo e($syncStats['queue_status']['pending'] > 0 ? 'text-yellow-600' : 'text-gray-900'); ?>">
                                <?php echo e($syncStats['queue_status']['pending'] ?? 0); ?>

                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-gray-500">Jobs échoués</p>
                            <p class="text-2xl font-semibold <?php echo e(($syncStats['queue_status']['failed'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-900'); ?>">
                                <?php echo e($syncStats['queue_status']['failed'] ?? 0); ?>

                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-gray-500">Dernier échec</p>
                            <p class="text-lg font-semibold text-gray-900">
                                <?php if(isset($syncStats['queue_status']['last_failed'])): ?>
                                    <?php echo e(\Carbon\Carbon::parse($syncStats['queue_status']['last_failed']->failed_at)->diffForHumans()); ?>

                                <?php else: ?>
                                    Jamais
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sync-form').addEventListener('submit', function(e) {
            const forceSync = document.getElementById('force_sync').checked;
            const useQueue = document.getElementById('use_queue').checked;
            const loader = document.getElementById('sync-loader');
            const btn = document.getElementById('sync-btn');

            // Confirmation si sync forcée
            if (forceSync) {
                if (!confirm('Vous êtes sur le point de forcer une synchronisation complète.\n\nCela peut prendre du temps.\n\nContinuer ?')) {
                    e.preventDefault();
                    return;
                }
            }

            // Afficher le loader seulement si on n'utilise PAS la queue
            if (!useQueue) {
                loader.classList.remove('hidden');
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Synchronisation en cours...';
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/admin/sync/index.blade.php ENDPATH**/ ?>
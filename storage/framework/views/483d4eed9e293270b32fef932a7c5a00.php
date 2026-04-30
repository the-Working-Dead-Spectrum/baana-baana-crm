

<?php $__env->startSection('title', 'Gestion des Créateurs'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestion des Créateurs</h1>
                <p class="text-gray-600 mt-2">Gérez tous les créateurs de votre plateforme</p>
            </div>
            <div>
                <a href="<?php echo e(route('admin.dashboard')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Retour au Dashboard
                </a>
            </div>
        </div>
        
        <!-- Filtres et recherche -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Recherche</label>
                        <input type="text" name="search" value="<?php echo e($search); ?>" 
                               placeholder="Nom, email ou marque..."
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Tous les statuts</option>
                            <option value="active" <?php echo e($status == 'active' ? 'selected' : ''); ?>>Actifs</option>
                            <option value="inactive" <?php echo e($status == 'inactive' ? 'selected' : ''); ?>>Inactifs</option>
                            <option value="suspended" <?php echo e($status == 'suspended' ? 'selected' : ''); ?>>Suspendus</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Filtrer
                        </button>
                        <?php if($search || $status): ?>
                            <a href="<?php echo e(route('admin.creators')); ?>" 
                               class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                Réinitialiser
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Table des créateurs -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <?php if($creators->count() > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Créateur
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contact
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Marque
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date d'ajout
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $creators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $creator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-blue-600 font-semibold">
                                                            <?php echo e(strtoupper(substr($creator->name, 0, 1))); ?>

                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo e($creator->name); ?>

                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        ID: <?php echo e($creator->wp_creator_id); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo e($creator->email); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo e($creator->phone ?? 'N/A'); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <?php echo e($creator->brand_slug); ?>

                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php echo e($creator->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                   ($creator->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 
                                                   'bg-red-100 text-red-800')); ?>">
                                                <?php echo e($creator->status === 'active' ? 'Actif' : 
                                                   ($creator->status === 'inactive' ? 'Inactif' : 'Suspendu')); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo e($creator->created_at->format('d/m/Y')); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="<?php echo e(route('admin.creators.show', $creator)); ?>" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    Voir
                                                </a>
                                                <a href="#" 
                                                   class="text-green-600 hover:text-green-900">
                                                    Éditer
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        <?php echo e($creators->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0H21m-4.5 0H15m4.5 0h.008v.008h-.008V15zm0 0h.008v.008h-.008V15z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun créateur trouvé</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            <?php if($search || $status): ?>
                                Essayez de modifier vos critères de recherche.
                            <?php else: ?>
                                Aucun créateur n'a été synchronisé depuis WordPress.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/admin/creators.blade.php ENDPATH**/ ?>
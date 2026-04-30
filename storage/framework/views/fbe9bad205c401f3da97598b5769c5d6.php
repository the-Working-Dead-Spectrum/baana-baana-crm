

<?php $__env->startSection('title', 'Segmentation Client'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Segmentation Client</h1>
            <p class="text-gray-600 mt-1">Analysez et ciblez vos clients par segments</p>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php $__currentLoopData = $segments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('segments.index', ['segment' => $key])); ?>" 
                   class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200 <?php echo e($selectedSegment === $key ? 'ring-2 ring-blue-500' : ''); ?>">
                    
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                <?php echo e($segment['name']); ?>

                            </h3>
                            <p class="text-sm text-gray-600"><?php echo e($segment['description']); ?></p>
                        </div>
                        
                        <div class="ml-4">
                            <div class="w-12 h-12 rounded-full bg-<?php echo e($segment['color']); ?>-100 flex items-center justify-center">
                                <span class="text-2xl">
                                    <?php switch($segment['icon']):
                                        case ('users'): ?>
                                            👥
                                            <?php break; ?>
                                        <?php case ('star'): ?>
                                            ⭐
                                            <?php break; ?>
                                        <?php case ('user-plus'): ?>
                                            ✨
                                            <?php break; ?>
                                        <?php case ('user-x'): ?>
                                            😴
                                            <?php break; ?>
                                        <?php case ('shopping-bag'): ?>
                                            🛍️
                                            <?php break; ?>
                                        <?php case ('clock'): ?>
                                            ⏰
                                            <?php break; ?>
                                    <?php endswitch; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <span class="text-2xl font-bold text-<?php echo e($segment['color']); ?>-600">
                            <?php echo e(number_format($segment['count'])); ?>

                        </span>
                        <span class="text-sm text-gray-500">clients</span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <?php if($selectedSegment !== 'all'): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            <?php echo e($segments[$selectedSegment]['name']); ?>

                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <?php echo e($customers->total()); ?> client(s) dans ce segment
                        </p>
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="<?php echo e(route('segments.export', $selectedSegment)); ?>" 
                           class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                            📥 Exporter en CSV
                        </a>
                        <button onclick="openCampaignModal()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                            📧 Lancer une campagne
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Client
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Commandes
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total dépensé
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Panier moyen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dernière commande
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-100 to-purple-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-sm font-bold text-blue-600">
                                                <?php echo e(strtoupper(substr($customer->customer_name ?? 'C', 0, 1))); ?>

                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo e($customer->customer_name ?? 'Client inconnu'); ?>

                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo e($customer->customer_email); ?></div>
                                    <?php if($customer->customer_phone): ?>
                                        <div class="text-sm text-gray-500"><?php echo e($customer->customer_phone); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?php echo e($customer->order_count); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e(number_format($customer->total_spent, 0, ',', ' ')); ?> FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e(number_format($customer->average_order ?? 0, 0, ',', ' ')); ?> FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e(\Carbon\Carbon::parse($customer->last_order_date)->format('d/m/Y')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <?php
                                        $emailKey = $customer->customer_email
                                            ? str_replace('@', '', hash('md5', strtolower($customer->customer_email)))
                                            : '';
                                    ?>
                                    <a href="<?php echo e(route('admin.customers.show', urlencode($emailKey))); ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        Voir profil
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p class="text-sm">Aucun client dans ce segment</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($customers->hasPages()): ?>
                <div class="px-6 py-4 border-t border-gray-200">
                    <?php echo e($customers->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div id="campaignModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Lancer une campagne</h3>
            <p class="text-sm text-gray-600 mb-4">
                Fonctionnalité à venir : envoi d'emails, SMS ou notifications push à ce segment.
            </p>
            <div class="flex justify-end">
                <button onclick="closeCampaignModal()" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function openCampaignModal() {
    document.getElementById('campaignModal').classList.remove('hidden');
}

function closeCampaignModal() {
    document.getElementById('campaignModal').classList.add('hidden');
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/admin/segments/index.blade.php ENDPATH**/ ?>
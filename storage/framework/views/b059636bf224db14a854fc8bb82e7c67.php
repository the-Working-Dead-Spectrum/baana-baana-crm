<?php $__env->startSection('title', 'Commandes'); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Commandes</h1>
                    <p class="text-gray-600 mt-1">Filtrer, analyser et gérer les commandes</p>
                </div>
                <a href="<?php echo e(route('admin.dashboard')); ?>"
                    class="px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300">
                    ← Retour
                </a>
            </div>

            
            <form method="GET" class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                    
                    <div class="md:col-span-2">
                        <label class="text-xs font-medium text-gray-500">Recherche</label>
                        <input type="text" name="q" value="<?php echo e(isset($filters['q']) ? $filters['q'] : ''); ?>"
                            placeholder="Commande, client, email..." class="w-full mt-1 rounded-md border-gray-300 text-sm">
                    </div>

                    
                    <div>
                        <label class="text-xs font-medium text-gray-500">Statut</label>
                        <select name="status" class="w-full mt-1 rounded-md border-gray-300 text-sm">
                            <option value="">Tous</option>
                            <?php $__currentLoopData = ['pending', 'processing', 'completed', 'cancelled', 'on-hold']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>" 
                                        <?php if((isset($filters['status']) && $filters['status'] === $status)): echo 'selected'; endif; ?>>
                                    <?php echo e(ucfirst($status)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div>
                        <label class="text-xs font-medium text-gray-500">Nombre d'articles</label>
                        <select name="order_count" class="w-full mt-1 rounded-md border-gray-300 text-sm">
                            <option value="">Tous</option>
                            <option value="1-2" <?php if((isset($filters['order_count']) && $filters['order_count'] === '1-2')): echo 'selected'; endif; ?>>1 à 2</option>
                            <option value="3-5" <?php if((isset($filters['order_count']) && $filters['order_count'] === '3-5')): echo 'selected'; endif; ?>>3 à 5</option>
                            <option value="5+" <?php if((isset($filters['order_count']) && $filters['order_count'] === '5+')): echo 'selected'; endif; ?>>Plus de 5</option>
                        </select>
                    </div>

                    
                    <div>
                        <label class="text-xs font-medium text-gray-500">Du</label>
                        <input type="date" name="from" value="<?php echo e(isset($filters['from']) ? $filters['from'] : ''); ?>"
                            class="w-full mt-1 rounded-md border-gray-300 text-sm">
                    </div>

                    
                    <div>
                        <label class="text-xs font-medium text-gray-500">Au</label>
                        <input type="date" name="to" value="<?php echo e(isset($filters['to']) ? $filters['to'] : ''); ?>"
                            class="w-full mt-1 rounded-md border-gray-300 text-sm">
                    </div>
                </div>

                <div class="flex justify-between items-center mt-6">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md text-xs font-semibold hover:bg-blue-700">
                        Filtrer
                    </button>

                    <a href="<?php echo e(route('admin.orders')); ?>" class="text-xs text-gray-500 hover:underline">
                        Réinitialiser
                    </a>
                </div>

                
                <input type="hidden" name="sort_by" value="<?php echo e(isset($sort_by) ? $sort_by : 'order_date'); ?>">
                <input type="hidden" name="sort_order" value="<?php echo e(isset($sort_order) ? $sort_order : 'desc'); ?>">
            </form>

            
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">
                                <a href="<?php echo e(request()->fullUrlWithQuery(['sort_by' => 'order_number', 'sort_order' => (isset($sort_by) && $sort_by === 'order_number' && isset($sort_order) && $sort_order === 'asc') ? 'desc' : 'asc'])); ?>"
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 flex items-center gap-1">
                                    N° Commande
                                    <?php if(isset($sort_by) && $sort_by === 'order_number'): ?>
                                        <span class="text-blue-600"><?php echo e(isset($sort_order) && $sort_order === 'asc' ? '↑' : '↓'); ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left">
                                <a href="<?php echo e(request()->fullUrlWithQuery(['sort_by' => 'customer_name', 'sort_order' => (isset($sort_by) && $sort_by === 'customer_name' && isset($sort_order) && $sort_order === 'asc') ? 'desc' : 'asc'])); ?>"
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 flex items-center gap-1">
                                    Client
                                    <?php if(isset($sort_by) && $sort_by === 'customer_name'): ?>
                                        <span class="text-blue-600"><?php echo e(isset($sort_order) && $sort_order === 'asc' ? '↑' : '↓'); ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left">
                                <a href="<?php echo e(request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_order' => (isset($sort_by) && $sort_by === 'status' && isset($sort_order) && $sort_order === 'asc') ? 'desc' : 'asc'])); ?>"
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 flex items-center gap-1">
                                    Statut
                                    <?php if(isset($sort_by) && $sort_by === 'status'): ?>
                                        <span class="text-blue-600"><?php echo e(isset($sort_order) && $sort_order === 'asc' ? '↑' : '↓'); ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left">
                                <a href="<?php echo e(request()->fullUrlWithQuery(['sort_by' => 'total', 'sort_order' => (isset($sort_by) && $sort_by === 'total' && isset($sort_order) && $sort_order === 'asc') ? 'desc' : 'asc'])); ?>"
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 flex items-center gap-1">
                                    Montant
                                    <?php if(isset($sort_by) && $sort_by === 'total'): ?>
                                        <span class="text-blue-600"><?php echo e(isset($sort_order) && $sort_order === 'asc' ? '↑' : '↓'); ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left">
                                <a href="<?php echo e(request()->fullUrlWithQuery(['sort_by' => 'order_date', 'sort_order' => (isset($sort_by) && $sort_by === 'order_date' && isset($sort_order) && $sort_order === 'asc') ? 'desc' : 'asc'])); ?>"
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 flex items-center gap-1">
                                    Date
                                    <?php if(isset($sort_by) && $sort_by === 'order_date'): ?>
                                        <span class="text-blue-600"><?php echo e(isset($sort_order) && $sort_order === 'asc' ? '↑' : '↓'); ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo e($order->order_number); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($order->customer_name); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e($order->customer_email); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php if($order->status === 'completed'): ?> bg-green-100 text-green-800
                            <?php elseif($order->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                            <?php elseif($order->status === 'processing'): ?> bg-blue-100 text-blue-800
                            <?php elseif($order->status === 'cancelled'): ?> bg-red-100 text-red-800
                            <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                        <?php echo e(ucfirst($order->status)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e(number_format($order->total, 0, ',', ' ')); ?> FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($order->order_date?->format('d/m/Y H:i')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>"
                                        class="text-blue-600 hover:text-blue-900">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Aucune commande trouvée
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                
                <div class="px-6 py-4 border-t border-gray-200">
                    <?php echo e($orders->links()); ?>

                </div>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/admin/orders.blade.php ENDPATH**/ ?>
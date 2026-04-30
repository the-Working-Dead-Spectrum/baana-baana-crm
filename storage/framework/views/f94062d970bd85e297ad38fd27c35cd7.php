

<?php $__env->startSection('title', 'Détail de la commande #' . $order->order_number); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            <?php if(session('success')): ?>
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative"
                    role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium"><?php echo e(session('success')); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative"
                    role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium"><?php echo e(session('error')); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(session('info')): ?>
                <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative"
                    role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium"><?php echo e(session('info')); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <a href="<?php echo e(route('creator.orders')); ?>"
                            class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Retour aux commandes
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">Commande #<?php echo e($order->order_number); ?></h1>
                        <p class="text-gray-600 mt-2">Passée le <?php echo e($order->order_date?->format('d/m/Y à H:i')); ?></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <?php if($order->status === 'processing'): ?>
                            <form action="<?php echo e(route('creator.orders.complete', $order->id)); ?>" method="POST"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir terminer cette commande ?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-150">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Marquer la commande comme traitée
                                </button>
                            </form>
                        <?php endif; ?>

                        <span
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-<?php echo e($order->status == 'completed' ? 'green' : ($order->status == 'pending' ? 'yellow' : ($order->status == 'processing' ? 'blue' : 'gray'))); ?>-100 text-<?php echo e($order->status == 'completed' ? 'green' : ($order->status == 'pending' ? 'yellow' : ($order->status == 'processing' ? 'blue' : 'gray'))); ?>-800"
                            style="    display: flex;
    flex-direction: column;
    justify-content: space-around;
    align-items: center;">
                            <?php switch($order->status):
                                case ('completed'): ?>
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Complétée
                                    <button onclick="document.getElementById('transferModal').classList.remove('hidden')"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-150">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        Transférer vers Logistique
                                    </button>
                                <?php break; ?>

                                <?php case ('pending'): ?>
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    En attente
                                <?php break; ?>

                                <?php case ('logistics'): ?>
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    En Cours de Livraison
                                <?php break; ?>

                                <?php case ('processing'): ?>
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    En traitement
                                <?php break; ?>

                                <?php default: ?>
                                    <?php echo e(ucfirst($order->status)); ?>

                            <?php endswitch; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Produits -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Produits de votre marque
                                (<?php echo e($creator->brand_name); ?>)</h2>

                            <?php if($creatorItems && $creatorItems->count() > 0): ?>
                                <div class="space-y-4">
                                    <?php $__currentLoopData = $creatorItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            // Safe property access for both objects and arrays
                                            $productName = is_object($item)
                                                ? $item->product_name
                                                : $item['product_name'] ?? 'N/A';
                                            $sku = is_object($item) ? $item->sku ?? 'N/A' : $item['sku'] ?? 'N/A';
                                            $quantity = is_object($item) ? $item->quantity : $item['quantity'] ?? 0;
                                            $unitPrice = is_object($item)
                                                ? $item->unit_price
                                                : $item['unit_price'] ?? 0;
                                            $total = is_object($item) ? $item->total : $item['total'] ?? 0;
                                        ?>

                                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                            <div
                                                class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-base font-medium text-gray-900"><?php echo e($productName); ?></h3>
                                                <p class="text-sm text-gray-500 mt-1">SKU: <?php echo e($sku); ?></p>
                                                <div class="mt-2 flex items-center space-x-4">
                                                    <span class="text-sm text-gray-600">Quantité: <span
                                                            class="font-medium"><?php echo e($quantity); ?></span></span>
                                                    <span class="text-sm text-gray-600">Prix unitaire: <span
                                                            class="font-medium"><?php echo e(number_format($unitPrice, 0, ',', ' ')); ?>

                                                            CFA</span></span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-semibold text-gray-900">
                                                    <?php echo e(number_format($total, 0, ',', ' ')); ?> CFA</p>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                <!-- Résumé des totaux -->
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Nombre de produits:</span>
                                            <span class="font-medium text-gray-900"><?php echo e($productCount); ?></span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Quantité totale:</span>
                                            <span class="font-medium text-gray-900"><?php echo e($totalQuantity); ?></span>
                                        </div>
                                        <div class="flex justify-between text-base pt-2 border-t border-gray-200">
                                            <span class="font-semibold text-gray-900">Total
                                                (<?php echo e($creator->brand_name); ?>):</span>
                                            <span
                                                class="font-bold text-gray-900 text-lg"><?php echo e(number_format($creatorTotal, 0, ',', ' ')); ?>

                                                CFA</span>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun produit de votre marque</h3>
                                    <p class="mt-1 text-sm text-gray-500">Cette commande ne contient aucun produit de
                                        <?php echo e($creator->brand_name); ?>.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Timeline / Historique -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Historique de la commande</h2>
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    <li>
                                        <div class="relative pb-8">
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span
                                                        class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                            <path fill-rule="evenodd"
                                                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-900">Commande créée</p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                        <time><?php echo e($order->created_at?->format('d/m/Y H:i')); ?></time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <?php if($order->status == 'processing' || $order->status == 'completed' || $order->status == 'logistics'): ?>
                                        <li>
                                            <div class="relative pb-8">
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                    aria-hidden="true"></span>
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span
                                                            class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-5 w-5 text-white" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-gray-900">Commande en traitement</p>
                                                        </div>
                                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                            <time><?php echo e($order->updated_at?->format('d/m/Y H:i')); ?></time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($order->status == 'logistics'): ?>
                                        <li>
                                            <div class="relative pb-8">
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                    aria-hidden="true"></span>
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span
                                                            class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-5 w-5 text-white" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path
                                                                    d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                                                <path
                                                                    d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-gray-900">Transférée à la logistique</p>
                                                            <p class="text-xs text-gray-500 mt-1">En cours d'expédition</p>
                                                        </div>
                                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                            <time><?php echo e($order->updated_at?->format('d/m/Y H:i')); ?></time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($order->status == 'completed'): ?>
                                        <li>
                                            <div class="relative pb-8">
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span
                                                            class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-5 w-5 text-white" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-gray-900">Commande complétée</p>
                                                        </div>
                                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                            <time><?php echo e($order->order_date?->format('d/m/Y H:i')); ?></time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne latérale -->
                <div class="space-y-6">
                    <!-- Informations client -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations client</h2>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-500">Nom</p>
                                    <p class="text-sm font-medium text-gray-900"><?php echo e($order->customer_name ?? 'N/A'); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Email</p>
                                    <p class="text-sm font-medium text-gray-900"><?php echo e($order->customer_email ?? 'N/A'); ?></p>
                                </div>
                                <?php if($order->customer_phone): ?>
                                    <div>
                                        <p class="text-xs text-gray-500">Téléphone</p>
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($order->customer_phone); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Adresse de livraison -->
                    <?php if($order->shipping_address): ?>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Adresse de livraison</h2>
                                <div class="text-sm text-gray-900 whitespace-pre-line"><?php echo e($order->shipping_address); ?>

                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Résumé financier -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Résumé financier</h2>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Votre part (<?php echo e($creator->brand_name); ?>):</span>
                                    <span
                                        class="font-semibold text-gray-900"><?php echo e(number_format($creatorTotal, 0, ',', ' ')); ?>

                                        CFA</span>
                                </div>
                                <div class="flex justify-between text-sm pt-3 border-t border-gray-200">
                                    <span class="text-gray-600">Total de la commande:</span>
                                    <span
                                        class="font-medium text-gray-900"><?php echo e(number_format($order->total, 0, ',', ' ')); ?>

                                        CFA</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Méthode de paiement -->
                    <?php if($order->payment_method): ?>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Paiement</h2>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Méthode:</span>
                                        <span
                                            class="font-medium text-gray-900"><?php echo e(ucfirst($order->payment_method)); ?></span>
                                    </div>
                                    <?php if($order->payment_status): ?>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Statut:</span>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-<?php echo e($order->payment_status == 'paid' ? 'green' : 'yellow'); ?>-100 text-<?php echo e($order->payment_status == 'paid' ? 'green' : 'yellow'); ?>-800">
                                                <?php echo e(ucfirst($order->payment_status)); ?>

                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Notes -->
                    <?php if($order->notes): ?>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes</h2>
                                <p class="text-sm text-gray-700"><?php echo e($order->notes); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Transfert Logistique -->
    <div id="transferModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-60" onclick="if(event.target === this) this.classList.add('hidden')">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white shadow-2xl w-3/5" style="border-radius: 24px; overflow: hidden;">

            <!-- Header avec dégradé -->
            <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);" class="px-8 py-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center"
                            style="background: rgba(255,255,255,0.2);">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">Choisir un transporteur</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.7);">
                                Commande <span class="font-semibold text-white">#<?php echo e($order->order_number); ?></span>
                            </p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('transferModal').classList.add('hidden')"
                        class="w-8 h-8 flex items-center justify-center rounded-full transition-all"
                        style="background: rgba(255,255,255,0.15);">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Intro -->
            <div class="px-8 pt-6 pb-2">
                <p class="text-sm text-gray-400 text-center">
                    Sélectionnez le service logistique approprié. Un email de confirmation sera transmis au prestataire.
                </p>
            </div>

            <!-- Options en 3 colonnes -->
            <div class="px-8 py-5 grid grid-cols-3 gap-4">

                <!-- PAPS -->
                <form method="POST" action="<?php echo e(route('creator.orders.transfer.logistics', $order->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="logistics_provider" value="paps">
                    <button type="submit" class="w-full group text-center transition-all duration-200"
                        style="border-radius: 16px;">
                        <div class="border-2 border-transparent group-hover:border-blue-500 group-hover:shadow-lg transition-all duration-200 p-4 flex flex-col items-center gap-3"
                            style="border-radius: 16px; background: #f8faff;">
                            <!-- Image -->
                            <div
                                class="w-full h-20 rounded-xl overflow-hidden flex items-center justify-center bg-white shadow-sm">
                                <img src="https://siecledigital.fr/wp-content/uploads/2020/01/Paps-livraison-senegal-1.jpg"
                                    alt="PAPS" class="w-full h-full object-cover" />
                            </div>
                            <!-- Infos -->
                            <div>
                                <p class="font-bold text-sm text-gray-800">PAPS</p>
                                <p class="text-xs text-gray-400 mt-0.5 leading-snug">Livraison urbaine<br>Délai : 24–48h
                                </p>
                            </div>
                            <!-- Badge -->
                            <span class="text-xs font-semibold px-3 py-1 rounded-full text-blue-700"
                                style="background: #dbeafe;">
                                Local
                            </span>
                        </div>
                    </button>
                </form>

                <!-- DHL -->
                <form method="POST" action="<?php echo e(route('creator.orders.transfer.logistics', $order->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="logistics_provider" value="dhl">
                    <button type="submit" class="w-full group text-center transition-all duration-200"
                        style="border-radius: 16px;">
                        <div class="border-2 border-transparent group-hover:border-yellow-400 group-hover:shadow-lg transition-all duration-200 p-4 flex flex-col items-center gap-3"
                            style="border-radius: 16px; background: #fffdf5;">
                            <!-- Image -->
                            <div
                                class="w-full h-20 rounded-xl overflow-hidden flex items-center justify-center bg-white shadow-sm px-3">
                                <img src="https://lofrev.net/wp-content/photos/2016/06/dhl-logo.png" alt="DHL"
                                    class="w-full h-full object-contain" />
                            </div>
                            <!-- Infos -->
                            <div>
                                <p class="font-bold text-sm text-gray-800">DHL</p>
                                <p class="text-xs text-gray-400 mt-0.5 leading-snug">International<br>Suivi temps réel</p>
                            </div>
                            <!-- Badge -->
                            <span class="text-xs font-semibold px-3 py-1 rounded-full text-yellow-700"
                                style="background: #fef3c7;">
                                Express
                            </span>
                        </div>
                    </button>
                </form>

                <!-- Fret -->
                <form method="POST" action="<?php echo e(route('creator.orders.transfer.logistics', $order->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="logistics_provider" value="fret">
                    <button type="submit" class="w-full group text-center transition-all duration-200"
                        style="border-radius: 16px;">
                        <div class="border-2 border-transparent group-hover:border-green-500 group-hover:shadow-lg transition-all duration-200 p-4 flex flex-col items-center gap-3"
                            style="border-radius: 16px; background: #f4fdf6;">
                            <!-- Image -->
                            <div
                                class="w-full h-20 rounded-xl overflow-hidden flex items-center justify-center bg-white shadow-sm">
                                <img src="https://cdni.iconscout.com/illustration/premium/thumb/fret-aerien-5850949-4883066.png"
                                    alt="Fret" class="w-full h-full object-contain" />
                            </div>
                            <!-- Infos -->
                            <div>
                                <p class="font-bold text-sm text-gray-800">Fret</p>
                                <p class="text-xs text-gray-400 mt-0.5 leading-snug">Transport cargo<br>Grandes quantités
                                </p>
                            </div>
                            <!-- Badge -->
                            <span class="text-xs font-semibold px-3 py-1 rounded-full text-green-700"
                                style="background: #dcfce7;">
                                Cargo
                            </span>
                        </div>
                    </button>
                </form>

            </div>

            <!-- Footer -->
            <div class="px-8 pb-7">
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5 inline-block mr-1 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        Cette action est irréversible une fois confirmée.
                    </p>
                    <button onclick="document.getElementById('transferModal').classList.add('hidden')"
                        class="px-5 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                        style="border-radius: 10px;">
                        Annuler
                    </button>
                </div>
            </div>

        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wp-cs-crm\resources\views/creator/orders/show.blade.php ENDPATH**/ ?>
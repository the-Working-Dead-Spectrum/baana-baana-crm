@extends('layouts.app')

@section('title', 'Détail de la commande #' . $order->order_number)

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative"
                    role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative"
                    role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative"
                    role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <a href="{{ route('creator.orders') }}"
                            class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Retour aux commandes
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">Commande #{{ $order->order_number }}</h1>
                        <p class="text-gray-600 mt-2">Passée le {{ $order->order_date?->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($order->status === 'processing')
                            @php
                                $hasCreatorCompleted = $order->hasCreatorCompleted($creator);
                                $progress = $order->getCompletionProgress();
                            @endphp

                            @if ($hasCreatorCompleted)
                                <!-- Le créateur a déjà terminé sa partie -->
                                <div
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Votre partie est terminée
                                </div>

                                @if ($progress['pending'] > 0)
                                    <div
                                        class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-lg">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        En attente de {{ $progress['pending'] }} autre(s) créateur(s)
                                    </div>
                                @else
                                    <div
                                        class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 text-sm font-medium rounded-lg">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Tous les créateurs ont terminé
                                    </div>
                                @endif
                            @else
                                <!-- Le créateur n'a pas encore terminé -->
                                <form action="{{ route('creator.orders.complete', $order->id) }}" method="POST"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir marquer votre partie comme terminée ?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-150">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Marquer ma partie comme traitée
                                    </button>
                                </form>

                                @if ($progress['total'] > 1 && $progress['completed'] > 0)
                                    <div
                                        class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded">
                                        {{ $progress['completed'] }}/{{ $progress['total'] }} créateur(s) ont terminé
                                    </div>
                                @endif
                            @endif
                        @endif

                        <span
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-{{ $order->status == 'completed' ? 'green' : ($order->status == 'pending' ? 'yellow' : ($order->status == 'processing' ? 'blue' : 'gray')) }}-100 text-{{ $order->status == 'completed' ? 'green' : ($order->status == 'pending' ? 'yellow' : ($order->status == 'processing' ? 'blue' : 'gray')) }}-800"
                            style="    display: flex;
    flex-direction: column;
    justify-content: space-around;
    align-items: center;">
                            @switch($order->status)
                                @case('completed')
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
                                @break

                                @case('pending')
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    En attente
                                @break

                                @case('logistics')
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    En Cours de Livraison
                                @break

                                @case('processing')
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    En traitement
                                @break

                                @default
                                    {{ ucfirst($order->status) }}
                            @endswitch
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
                                ({{ $creator->brand_name }})</h2>

                            @if ($creatorItems && $creatorItems->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($creatorItems as $item)
                                        @php
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
                                        @endphp

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
                                                <h3 class="text-base font-medium text-gray-900">{{ $productName }}</h3>
                                                <p class="text-sm text-gray-500 mt-1">SKU: {{ $sku }}</p>
                                                <div class="mt-2 flex items-center space-x-4">
                                                    <span class="text-sm text-gray-600">Quantité: <span
                                                            class="font-medium">{{ $quantity }}</span></span>
                                                    <span class="text-sm text-gray-600">Prix unitaire: <span
                                                            class="font-medium">{{ number_format($unitPrice, 0, ',', ' ') }}
                                                            CFA</span></span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-semibold text-gray-900">
                                                    {{ number_format($total, 0, ',', ' ') }} CFA</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Résumé des totaux -->
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Nombre de produits:</span>
                                            <span class="font-medium text-gray-900">{{ $productCount }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Quantité totale:</span>
                                            <span class="font-medium text-gray-900">{{ $totalQuantity }}</span>
                                        </div>
                                        <div class="flex justify-between text-base pt-2 border-t border-gray-200">
                                            <span class="font-semibold text-gray-900">Total
                                                ({{ $creator->brand_name }}):</span>
                                            <span
                                                class="font-bold text-gray-900 text-lg">{{ number_format($creatorTotal, 0, ',', ' ') }}
                                                CFA</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun produit de votre marque</h3>
                                    <p class="mt-1 text-sm text-gray-500">Cette commande ne contient aucun produit de
                                        {{ $creator->brand_name }}.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline / Historique -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Historique de la commande</h2>
                            @if ($order->status === 'processing')
                                @php
                                    $progress = $order->getCompletionProgress();
                                    $completedCreators = $order->getCompletedCreators();
                                    $pendingCreators = $order->getPendingCreators();
                                @endphp

                                @if ($progress['total'] > 1)
                                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h3 class="text-sm font-semibold text-blue-900 mb-3">Progression des créateurs</h3>

                                        @if ($completedCreators->count() > 0)
                                            <div class="mb-3">
                                                <p class="text-xs text-blue-700 font-medium mb-2">✅ Créateurs ayant terminé
                                                    :</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($completedCreators as $c)
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ $c->name }}
                                                            @if ($c->pivot->completed_at)
                                                                <span
                                                                    class="ml-1 text-gray-500">({{ $c->pivot->completed_at }})</span>
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if ($pendingCreators->count() > 0)
                                            <div>
                                                <p class="text-xs text-blue-700 font-medium mb-2">⏳ En attente :</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($pendingCreators as $c)
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            {{ $c->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mt-3 pt-3 border-t border-blue-200">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-blue-700">Progression globale</span>
                                                <span
                                                    class="text-xs font-bold text-blue-900">{{ $progress['completed'] }}/{{ $progress['total'] }}</span>
                                            </div>
                                            <div class="mt-2 w-full bg-blue-200 rounded-full h-2.5">
                                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                                                    style="width: {{ ($progress['completed'] / $progress['total']) * 100 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
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
                                                        <time>{{ $order->created_at?->format('d/m/Y H:i') }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    @if ($order->status == 'processing' || $order->status == 'completed' || $order->status == 'logistics')
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
                                                            <time>{{ $order->updated_at?->format('d/m/Y H:i') }}</time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif

                                    @if ($order->status == 'logistics')
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
                                                            <time>{{ $order->updated_at?->format('d/m/Y H:i') }}</time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif

                                    @if ($order->status == 'completed')
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
                                                            <time>{{ $order->order_date?->format('d/m/Y H:i') }}</time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
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
                                    <p class="text-sm font-medium text-gray-900">{{ $order->customer_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Email</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $order->customer_email ?? 'N/A' }}</p>
                                </div>
                                @if ($order->customer_phone)
                                    <div>
                                        <p class="text-xs text-gray-500">Téléphone</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $order->customer_phone }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Adresse de livraison -->
                    @if ($order->shipping_address)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Adresse de livraison</h2>
                                <div class="text-sm text-gray-900 whitespace-pre-line">{{ $order->shipping_address }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Résumé financier -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Résumé financier</h2>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Votre part ({{ $creator->brand_name }}):</span>
                                    <span
                                        class="font-semibold text-gray-900">{{ number_format($creatorTotal, 0, ',', ' ') }}
                                        CFA</span>
                                </div>
                                <div class="flex justify-between text-sm pt-3 border-t border-gray-200">
                                    <span class="text-gray-600">Total de la commande:</span>
                                    <span
                                        class="font-medium text-gray-900">{{ number_format($order->total, 0, ',', ' ') }}
                                        CFA</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Méthode de paiement -->
                    @if ($order->payment_method)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Paiement</h2>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Méthode:</span>
                                        <span
                                            class="font-medium text-gray-900">{{ ucfirst($order->payment_method) }}</span>
                                    </div>
                                    @if ($order->payment_status)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Statut:</span>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $order->payment_status == 'paid' ? 'green' : 'yellow' }}-100 text-{{ $order->payment_status == 'paid' ? 'green' : 'yellow' }}-800">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if ($order->notes)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes</h2>
                                <p class="text-sm text-gray-700">{{ $order->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Transfert Logistique -->
    <div id="transferModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-60"
        onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white shadow-2xl w-3/5" style="border-radius: 24px; overflow: hidden;">

                <!-- Header avec dégradé -->
                <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);" class="px-8 py-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center"
                                style="background: rgba(255,255,255,0.2);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-white">Choisir un transporteur</h2>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.7);">
                                    Commande <span class="font-semibold text-white">#{{ $order->order_number }}</span>
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
                    <form method="POST" action="{{ route('creator.orders.transfer.logistics', $order->id) }}">
                        @csrf
                        @method('PATCH')
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
                                    <p class="text-xs text-gray-400 mt-0.5 leading-snug">Livraison urbaine<br>Délai :
                                        24–48h
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
                    <form method="POST" action="{{ route('creator.orders.transfer.logistics', $order->id) }}">
                        @csrf
                        @method('PATCH')
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
                                    <p class="text-xs text-gray-400 mt-0.5 leading-snug">International<br>Suivi temps réel
                                    </p>
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
                    <form method="POST" action="{{ route('creator.orders.transfer.logistics', $order->id) }}">
                        @csrf
                        @method('PATCH')
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
                                    <p class="text-xs text-gray-400 mt-0.5 leading-snug">Transport cargo<br>Grandes
                                        quantités
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
                            <svg class="w-3.5 h-3.5 inline-block mr-1 text-gray-300" fill="currentColor"
                                viewBox="0 0 20 20">
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

    @endsection

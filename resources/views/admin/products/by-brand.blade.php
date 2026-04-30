@extends('layouts.app')

@section('title', 'Produits - ' . $brandSlug)

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header avec retour -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.products') }}" 
                       class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $brandSlug }}</h1>
                        <p class="text-gray-600 mt-1">Produits de la marque</p>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.products') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    ← Retour aux marques
                </a>
            </div>
        </div>

        <!-- Statistiques de la marque -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total produits -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total produits</dt>
                                <dd class="text-2xl font-semibold text-gray-900">
                                    {{ $brandStats->product_count ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock total -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Stock total</dt>
                                <dd class="text-2xl font-semibold text-gray-900">
                                    {{-- {{ number_format($brandStats->total_stock ?? 0, 0, ',', ' ') }} --}}
                                    En stock
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prix moyen -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Prix moyen</dt>
                                <dd class="text-2xl font-semibold text-gray-900">
                                    {{ number_format($brandStats->average_price ?? 0, 0, ',', ' ') }} CFA
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventes totales -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ventes totales</dt>
                                <dd class="text-2xl font-semibold text-gray-900">
                                    {{ number_format($salesStats->total_sales ?? 0, 0, ',', ' ') }} CFA
                                </dd>
                                <dd class="text-xs text-gray-500 mt-1">
                                    {{ number_format($salesStats->total_quantity ?? 0, 0, ',', ' ') }} unités vendues
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top produits vendus -->
        @if($topProducts && $topProducts->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Top 5 - Produits les plus commandés </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rang
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produit
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantité commandée
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Montant de la commande
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Commandes
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($topProducts as $index => $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-100 text-gray-800' : 'bg-orange-100 text-orange-800') }} font-semibold">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $product->product_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ID: {{ $product->wp_product_id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ number_format($product->total_quantity, 0, ',', ' ') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ number_format($product->total_sales, 0, ',', ' ') }} CFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->order_count }} commande(s)
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Liste des produits -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Tous les produits ({{ $products->total() }})</h2>
                    
                    <!-- Filtres rapides -->
                    {{-- <div class="flex space-x-2">
                        <button onclick="filterByStock('all')" 
                                class="px-3 py-1 text-xs font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">
                            Tous
                        </button>
                        <button onclick="filterByStock('in_stock')" 
                                class="px-3 py-1 text-xs font-medium rounded-md bg-green-100 text-green-700 hover:bg-green-200">
                            En stock
                        </button>
                        <button onclick="filterByStock('low_stock')" 
                                class="px-3 py-1 text-xs font-medium rounded-md bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                            Stock bas
                        </button>
                        <button onclick="filterByStock('out_of_stock')" 
                                class="px-3 py-1 text-xs font-medium rounded-md bg-red-100 text-red-700 hover:bg-red-200">
                            Rupture
                        </button>
                    </div> --}}
                </div>

                @if($products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="productsTable">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produit
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        SKU
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prix
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock
                                    </th>
                                    {{-- <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Valeur stock
                                    </th> --}}
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($products as $product)
                                    @php
                                        $stock = $product->stock_quantity ?? 0;
                                        $stockClass = $stock <= 0 ? 'bg-green-100 text-green-800' : 
                                                     ($stock < 20 ? 'bg-yellow-100 text-yellow-800' : 
                                                     'bg-green-100 text-green-800');
                                        $stockStatus = $stock <= 0 ? 'out_of_stock' : ($stock < 20 ? 'low_stock' : 'in_stock');
                                    @endphp
                                    <tr class="hover:bg-gray-50" data-stock-status="{{ $stockStatus }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $product->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        ID WP: {{ $product->wp_product_id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $product->sku ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ number_format($product->price ?? 0, 0, ',', ' ') }} CFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $stockClass }}">
                                                {{-- {{ $stock }} --}}
                                                En Stock
                                            </span>
                                        </td>
                                        {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format(($product->price ?? 0) * $stock, 0, ',', ' ') }} CFA
                                        </td> --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('products.show', $product->id) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                Détails
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            {{-- <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-sm font-semibold text-gray-900">
                                        Totaux (page actuelle)
                                    </td>
                                    <td class="px-6 py-3 text-sm font-semibold text-gray-900">
                                        {{ number_format($products->sum(function($p) { return ($p->price ?? 0) * ($p->stock_quantity ?? 0); }), 0, ',', ' ') }} CFA
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot> --}}
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun produit</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Aucun produit trouvé pour cette marque.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function filterByStock(status) {
    const rows = document.querySelectorAll('#productsTable tbody tr');
    
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-stock-status');
        
        if (status === 'all') {
            row.style.display = '';
        } else {
            row.style.display = rowStatus === status ? '' : 'none';
        }
    });
}
</script>
@endsection
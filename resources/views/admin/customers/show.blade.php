@extends('layouts.app')

@section('title', 'Fiche Client - ' . ($customer->customer_name ?? 'Client inconnu'))

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Fiche Client
                </h1>
                <p class="text-gray-600 mt-1">
                    Informations et historique des commandes
                </p>
            </div>
            <a href="{{ route('admin.orders') }}"
               class="px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300">
                ← Retour aux commandes
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- ================= COLONNE LATÉRALE (Informations client) ================= --}}
            <div class="space-y-6">
                {{-- Carte profil client --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex flex-col items-center text-center mb-6">
                        {{-- Avatar --}}
                        <div class="w-24 h-24 bg-gradient-to-r from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-4">
                            <span class="text-2xl font-bold text-blue-600">
                                {{ strtoupper(substr($customer->customer_name ?? 'C', 0, 1)) }}
                            </span>
                        </div>
                        
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $customer->customer_name ?? 'Client inconnu' }}
                        </h2>
                        
                        @if($customer->customer_email)
                            <p class="text-sm text-gray-600 mt-1">{{ $customer->customer_email }}</p>
                        @endif
                        
                        {{-- Badge client régulier --}}
                        @if($customer->order_count >= 5)
                            <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                                </svg>
                                Client fidèle
                            </span>
                        @endif
                    </div>

                    {{-- Informations de contact --}}
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Informations de contact</h3>
                        
                        <div class="space-y-3">
                            @if($customer->customer_email)
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <a href="mailto:{{ $customer->customer_email }}" 
                                           class="text-blue-600 hover:underline font-medium">
                                            {{ $customer->customer_email }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-1">Email principal</p>
                                    </div>
                                </div>
                            @endif

                            @if($customer->customer_phone)
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <div>
                                        <a href="tel:{{ $customer->customer_phone }}" 
                                           class="text-blue-600 hover:underline font-medium">
                                            {{ $customer->customer_phone }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-1">Téléphone</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Dernière adresse connue --}}
                            @if($customer->last_shipping_address)
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-700">{{ $customer->last_shipping_address }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Dernière adresse de livraison</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Séparateur --}}
                    <div class="border-t border-gray-200 my-6"></div>

                    {{-- Statistiques client --}}
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Statistiques</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-700">{{ $customer->order_count }}</div>
                                <div class="text-xs text-blue-600 mt-1">Commandes</div>
                            </div>
                            
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-700">{{ number_format($customer->total_spent, 0, ',', ' ') }} FCFA</div>
                                <div class="text-xs text-green-600 mt-1">Total dépensé</div>
                            </div>
                            
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-700">{{ number_format($customer->average_order, 0, ',', ' ') }} FCFA</div>
                                <div class="text-xs text-purple-600 mt-1">Panier moyen</div>
                            </div>
                            
                        </div>
                    </div>
                </div>

                {{-- Marques favorites --}}
                @if(count($customer->favorite_brands) > 0)
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Marques favorites</h3>
                        
                        <div class="space-y-2">
                            @foreach($customer->favorite_brands as $brand => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700">{{ $brand }}</span>
                                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded-full">
                                        {{ $count }} {{ $count > 1 ? 'commandes' : 'commande' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ================= COLONNE PRINCIPALE (Historique commandes) ================= --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Header historique --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Historique des commandes</h2>
                        <span class="text-sm text-gray-500">{{ $customer->orders->total() }} commande(s) trouvée(s)</span>
                    </div>

                    {{-- Filtres rapides --}}
                    <div class="flex flex-wrap gap-2 mb-6">
                        <button onclick="filterOrders('all')" 
                                class="px-3 py-1.5 text-sm bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200">
                            Toutes ({{ $customer->order_count }})
                        </button>
                        <button onclick="filterOrders('completed')" 
                                class="px-3 py-1.5 text-sm bg-green-100 text-green-700 rounded-full hover:bg-green-200">
                            Terminées ({{ $customer->completed_orders_count }})
                        </button>
                        <button onclick="filterOrders('processing')" 
                                class="px-3 py-1.5 text-sm bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200">
                            En traitement ({{ $customer->processing_orders_count }})
                        </button>
                        <button onclick="filterOrders('pending')" 
                                class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200">
                            En attente ({{ $customer->pending_orders_count }})
                        </button>
                    </div>
                </div>

                {{-- Liste des commandes --}}
                @forelse($customer->orders as $order)
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                        {{-- En-tête de la commande --}}
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                           class="hover:text-blue-600">
                                            Commande #{{ $order->order_number ?? $order->wp_order_id }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Passée le {{ $order->order_date->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' :
                                           ($order->status === 'processing' ? 'bg-yellow-100 text-yellow-700' :
                                           ($order->status === 'pending' ? 'bg-gray-100 text-gray-700' :
                                           'bg-blue-100 text-blue-700')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    
                                    <span class="text-sm font-bold text-gray-900">
                                        {{ number_format($order->total, 0, ',', ' ') }} FCFA
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Articles de la commande --}}
                        <div class="p-6">
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Articles ({{ $order->items->count() }})</h4>
                                <div class="space-y-2">
                                    @foreach($order->items->take(3) as $item)
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-100 rounded-md flex items-center justify-center mr-3">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                                <span class="text-gray-700 truncate">{{ $item->product_name }}</span>
                                            </div>
                                            <div class="text-gray-900 font-medium">
                                                {{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($order->items->count() > 3)
                                        <div class="text-center pt-2">
                                            <span class="text-xs text-gray-500">
                                                +{{ $order->items->count() - 3 }} autre(s) article(s)
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Infos complémentaires --}}
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-200">
                                <div>
                                    <span class="text-xs text-gray-500">Paiement</span>
                                    <div class="text-sm font-medium {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                                        {{ $order->payment_status === 'paid' ? 'Payé' : 'En attente' }}
                                    </div>
                                </div>
                                
                                <div>
                                    <span class="text-xs text-gray-500">Livraison</span>
                                    <div class="text-sm font-medium">
                                        {{ ucfirst($order->logistics_status ?? 'En préparation') }}
                                    </div>
                                </div>
                                
                                <div>
                                    <span class="text-xs text-gray-500">Suivi</span>
                                    @if($order->tracking_number)
                                        <div class="text-sm font-medium text-blue-600">{{ $order->tracking_number }}</div>
                                    @else
                                        <div class="text-sm text-gray-400">Non disponible</div>
                                    @endif
                                </div>
                                
                                <div>
                                    <span class="text-xs text-gray-500">Créateurs</span>
                                    <div class="text-sm font-medium">
                                        {{ $order->creators->count() }} créateur(s)
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                            <div class="flex justify-between">
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    Voir les détails →
                                </a>
                                
                                @if($order->status === 'processing')
                                    <button onclick="markAsShipped({{ $order->id }})" 
                                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Marquer comme expédié
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow-sm rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune commande</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Ce client n'a pas encore passé de commande.
                        </p>
                    </div>
                @endforelse

                {{-- Pagination --}}
                @if($customer->orders->hasPages())
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <div class="flex justify-center">
                            {{ $customer->orders->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterOrders(status) {
    // Vous pouvez implémenter un filtrage AJAX ou rediriger vers une URL filtrée
    const url = new URL(window.location.href);
    url.searchParams.set('status', status);
    window.location.href = url.toString();
}

function markAsShipped(orderId) {
    if (confirm('Marquer cette commande comme expédiée ?')) {
        fetch(`/admin/orders/${orderId}/ship`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            alert('Erreur réseau: ' + error.message);
        });
    }
}
</script>
@endpush
@endsection
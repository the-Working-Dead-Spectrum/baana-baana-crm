@extends('layouts.app')

@section('title', 'Détails de la commande #' . ($order->order_number ?? $order->wp_order_id))

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ================= HEADER ================= --}}
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Commande #{{ $order->order_number ?? $order->wp_order_id }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Passée le {{ $order->order_date->format('d/m/Y à H:i') }}
                    </p>
                </div>
                <a href="{{ route('admin.orders') }}"
                    class="px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300">
                    ← Retour aux commandes
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ================= COLONNE PRINCIPALE ================= --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Statuts de la commande --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Statuts</h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Statut général --}}
                            <div>
                                <label class="text-xs font-medium text-gray-500">Statut de la commande</label>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                                    {{ $order->status === 'completed'
                                        ? 'bg-green-100 text-green-700'
                                        : ($order->status === 'processing'
                                            ? 'bg-yellow-100 text-yellow-700'
                                            : ($order->status === 'cancelled'
                                                ? 'bg-red-100 text-red-700'
                                                : 'bg-gray-100 text-gray-700')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Statut logistique --}}
                            <div>
                                <label class="text-xs font-medium text-gray-500">Statut logistique</label>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                                    {{ $order->logistics_status === 'delivered'
                                        ? 'bg-green-100 text-green-700'
                                        : ($order->logistics_status === 'shipped'
                                            ? 'bg-blue-100 text-blue-700'
                                            : ($order->logistics_status === 'pending'
                                                ? 'bg-yellow-100 text-yellow-700'
                                                : 'bg-gray-100 text-gray-700')) }}">
                                        {{ ucfirst($order->logistics_status ?? 'pending') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Statut paiement --}}
                            <div>
                                <label class="text-xs font-medium text-gray-500">Statut paiement</label>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                                    {{ $order->payment_status === 'paid'
                                        ? 'bg-green-100 text-green-700'
                                        : ($order->payment_status === 'pending'
                                            ? 'bg-yellow-100 text-yellow-700'
                                            : 'bg-gray-100 text-gray-700') }}">
                                        {{ ucfirst($order->payment_status ?? 'unpaid') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Dates importantes --}}
                        @if ($order->shipped_at || $order->delivered_at || $order->payment_date)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    @if ($order->payment_date)
                                        <div>
                                            <span class="text-gray-500">Payée le :</span>
                                            <div class="font-medium">{{ $order->payment_date->format('d/m/Y') }}</div>
                                        </div>
                                    @endif
                                    @if ($order->shipped_at)
                                        <div>
                                            <span class="text-gray-500">Expédiée le :</span>
                                            <div class="font-medium">{{ $order->shipped_at->format('d/m/Y') }}</div>
                                        </div>
                                    @endif
                                    @if ($order->delivered_at)
                                        <div>
                                            <span class="text-gray-500">Livrée le :</span>
                                            <div class="font-medium">{{ $order->delivered_at->format('d/m/Y') }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Articles de la commande --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Articles ({{ $order->items->count() }})</h2>

                        <div class="space-y-4">
                            @forelse($order->items as $item)
                                <div class="flex items-center space-x-4 pb-4 border-b border-gray-200 last:border-0">
                                    {{-- Image produit --}}
                                    <div
                                        class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-md flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>

                                    {{-- Infos produit --}}
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900 truncate">
                                            {{ $item->product_name }}
                                        </h3>
                                        <div class="flex items-center space-x-3 mt-1 text-xs text-gray-500">
                                            @if ($item->sku)
                                                <span class="font-mono">SKU: {{ $item->sku }}</span>
                                            @endif
                                            @if ($item->brand_slug)
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">
                                                    {{ $item->brand_slug }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Données de variation --}}
                                        @if ($item->variation_data && is_array($item->variation_data))
                                            <div class="mt-2 text-xs text-gray-500">
                                                @foreach ($item->variation_data as $key => $value)
                                                    <span class="mr-3">{{ ucfirst($key) }}:
                                                        <strong>{{ $value }}</strong></span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Quantité et prix --}}
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($item->total, 0, ',', ' ') }} FCFA
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }}
                                            FCFA
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-400 py-6">Aucun article dans cette commande</p>
                            @endforelse
                        </div>

                        {{-- Totaux --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Sous-total</span>
                                <span class="font-medium">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span>
                            </div>

                            @if ($order->tax > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Taxes</span>
                                    <span class="font-medium">{{ number_format($order->tax, 0, ',', ' ') }} FCFA</span>
                                </div>
                            @endif

                            @if ($order->shipping > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Frais de livraison</span>
                                    <span class="font-medium">{{ number_format($order->shipping, 0, ',', ' ') }}
                                        FCFA</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                                <span class="text-gray-900">Total</span>
                                <span class="text-gray-900">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                            </div>

                            @if ($order->creator_total)
                                <div class="flex justify-between text-sm text-blue-600 pt-2">
                                    <span>Total créateur(s)</span>
                                    <span class="font-semibold">{{ number_format($order->creator_total, 0, ',', ' ') }}
                                        FCFA</span>
                                </div>
                            @endif

                            @if ($order->commission_amount)
                                <div class="flex justify-between text-sm text-green-600">
                                    <span>Commission</span>
                                    <span class="font-semibold">{{ number_format($order->commission_amount, 0, ',', ' ') }}
                                        FCFA</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Notes --}}
                    @if ($order->notes)
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes</h2>
                            <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $order->notes }}</div>
                        </div>
                    @endif

                </div>

                {{-- ================= COLONNE LATÉRALE ================= --}}
                <div class="space-y-6">

                    {{-- Informations client --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Client
                        </h2>

                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-500">Nom :</span>
                                @php
                                    $emailKey = $order->customer_email
                                        ? str_replace('@', '', hash('md5', strtolower($order->customer_email)))
                                        : '';
                                @endphp
                                <a href="{{ route('admin.customers.show', $emailKey) }}"
                                    class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $order->customer_name ?? 'Non renseigné' }}
                                </a>
                            </div>

                            @if ($order->customer_email)
                                <div>
                                    <span class="text-gray-500">Email :</span>
                                    <div class="font-medium">
                                        <a href="mailto:{{ $order->customer_email }}"
                                            class="text-blue-600 hover:underline">
                                            {{ $order->customer_email }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if ($order->customer_phone)
                                <div>
                                    <span class="text-gray-500">Téléphone :</span>
                                    <div class="font-medium">
                                        <a href="tel:{{ $order->customer_phone }}" class="text-blue-600 hover:underline">
                                            {{ $order->customer_phone }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Adresse de livraison --}}
                    @if ($order->shipping_address)
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Livraison
                            </h2>

                            <div class="text-sm text-gray-700 whitespace-pre-wrap">
                                {{ $order->shipping_address }}
                            </div>

                            @if ($order->tracking_number)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <span class="text-xs text-gray-500">N° de suivi :</span>
                                    <div class="font-mono text-sm font-medium mt-1">{{ $order->tracking_number }}</div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Créateurs associés --}}
                    @if ($order->creators->count() > 0)
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Créateurs ({{ $order->creators->count() }})
                            </h2>

                            <div class="space-y-3">
                                @foreach ($order->creators as $creator)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                        <div>
                                            <div class="font-medium text-sm">{{ $creator->name }}</div>
                                            @if ($creator->pivot->creator_total)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ number_format($creator->pivot->creator_total, 0, ',', ' ') }} FCFA
                                                </div>
                                            @endif
                                        </div>

                                        @if ($creator->commission_rate)
                                            <span
                                                class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full font-semibold">
                                                {{ $creator->commission_rate }}%
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Informations supplémentaires --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>

                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-500">ID WordPress :</span>
                                <div class="font-mono font-medium">{{ $order->wp_order_id }}</div>
                            </div>

                            @if ($order->assigned_to)
                                <div>
                                    <span class="text-gray-500">Assigné à :</span>
                                    <div class="font-medium">{{ $order->assignedUser->name ?? 'Utilisateur inconnu' }}
                                    </div>
                                </div>
                            @endif

                            @if ($order->last_synced_at)
                                <div>
                                    <span class="text-gray-500">Dernière synchro :</span>
                                    <div class="font-medium text-xs">{{ $order->last_synced_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            @endif

                            @if ($order->wp_updated_at)
                                <div>
                                    <span class="text-gray-500">Modifiée (WP) :</span>
                                    <div class="font-medium text-xs">{{ $order->wp_updated_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>


                    {{-- Actions rapides --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>

                        <div class="space-y-2">
                            <button
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 transition">
                                Imprimer la commande
                            </button>

                            <button
                                class="w-full px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-50 transition">
                                Envoyer un email au client
                            </button>

                            @if ($order->status !== 'cancelled')
                                <button
                                    class="w-full px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm font-semibold hover:bg-red-100 transition">
                                    Annuler la commande
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

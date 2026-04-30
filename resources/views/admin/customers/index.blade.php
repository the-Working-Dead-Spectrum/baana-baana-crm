@extends('layouts.app')

@section('title', 'Tous les clients')

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tous les clients</h1>
                    <p class="text-gray-600 mt-2">Gérez et consultez vos clients</p>
                </div>
            </div>

            {{-- Segments prédéfinis --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach ($segments as $key => $segment)
                    <a href="#"
                    {{-- <a href="{{ route('segments.index', ['segment' => $key]) }}" --}}
                        class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200 {{ isset($selectedSegment) && $selectedSegment === $key ? 'ring-2 ring-blue-500' : '' }}">

                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $segment['name'] }}</h3>
                                <p class="text-sm text-gray-600">{{ $segment['description'] }}</p>
                            </div>
                            <div class="ml-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-{{ $segment['color'] }}-100 flex items-center justify-center">
                                    <span class="text-2xl">
                                        @switch($segment['icon'])
                                            @case('users')
                                                👥
                                            @break

                                            @case('star')
                                                ⭐
                                            @break

                                            @case('user-plus')
                                                ✨
                                            @break

                                            @case('user-x')
                                                😴
                                            @break

                                            @case('shopping-bag')
                                                🛍️
                                            @break

                                            @case('clock')
                                                ⏰
                                            @break
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <span class="text-2xl font-bold text-{{ $segment['color'] }}-600">
                                {{ number_format($segment['count']) }}
                            </span>
                            <span class="text-sm text-gray-500">clients</span>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Barre de recherche et filtres --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.customers.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            {{-- Recherche --}}
                            <div>
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    placeholder="Nom, email, téléphone..."
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Filtre par nombre de commandes --}}
                            <div>
                                <label for="orders_filter" class="block text-sm font-medium text-gray-700 mb-2">Nombre de
                                    commandes</label>
                                <select name="orders_filter" id="orders_filter"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Tous</option>
                                    <option value="1-2" {{ request('orders_filter') === '1-2' ? 'selected' : '' }}>
                                        1 à 2 commandes ({{ $orderRanges['1-2'] }})
                                    </option>
                                    <option value="3-5" {{ request('orders_filter') === '3-5' ? 'selected' : '' }}>
                                        3 à 5 commandes ({{ $orderRanges['3-5'] }})
                                    </option>
                                    <option value="5+" {{ request('orders_filter') === '5+' ? 'selected' : '' }}>
                                        Plus de 5 commandes ({{ $orderRanges['5+'] }})
                                    </option>
                                </select>
                            </div>

                            {{-- Tri --}}
                            <div>
                                <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                                <select name="sort" id="sort"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="last_order_date"
                                        {{ request('sort') === 'last_order_date' || !request('sort') ? 'selected' : '' }}>
                                        Dernière commande</option>
                                    <option value="order_count" {{ request('sort') === 'order_count' ? 'selected' : '' }}>
                                        Nombre de commandes</option>
                                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom A-Z
                                    </option>
                                    <option value="first_order_date"
                                        {{ request('sort') === 'first_order_date' ? 'selected' : '' }}>Client depuis
                                    </option>
                                </select>
                            </div>

                            {{-- Nombre par page --}}
                            <div>
                                <label for="per_page" class="block text-sm font-medium text-gray-700 mb-2">Afficher</label>
                                <select name="per_page" id="per_page"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10
                                        clients</option>
                                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15
                                        clients</option>
                                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25
                                        clients</option>
                                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50
                                        clients</option>
                                </select>
                            </div>
                        </div>

                        {{-- Direction du tri (masqué) --}}
                        <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">

                        <div class="flex items-center space-x-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Filtrer
                            </button>

                            @if (request()->hasAny(['search', 'orders_filter', 'sort', 'per_page']))
                                <a href="{{ route('admin.customers.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                    Réinitialiser
                                </a>
                            @endif

                            <a href="{{ route('admin.customers.export') }}"
                                class="ml-auto inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Exporter CSV
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Liste des clients --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <a href="{{ route('admin.customers.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="group inline-flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                        Client
                                        @if (request('sort') === 'name')
                                            <span
                                                class="ml-2 text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                        @else
                                            <span class="ml-2 text-gray-400 opacity-0 group-hover:opacity-100">↕</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contact
                                </th>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <a href="{{ route('admin.customers.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'order_count', 'direction' => request('sort') === 'order_count' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="group inline-flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                        Total commandes
                                        @if (request('sort') === 'order_count')
                                            <span
                                                class="ml-2 text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                        @else
                                            <span class="ml-2 text-gray-400 opacity-0 group-hover:opacity-100">↕</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Commandes validées
                                </th>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <a href="{{ route('admin.customers.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'last_order_date', 'direction' => request('sort') === 'last_order_date' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="group inline-flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                        Dernière commande
                                        @if (request('sort') === 'last_order_date' || !request('sort'))
                                            <span
                                                class="ml-2 text-blue-600">{{ request('direction', 'desc') === 'asc' ? '↑' : '↓' }}</span>
                                        @else
                                            <span class="ml-2 text-gray-400 opacity-0 group-hover:opacity-100">↕</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($customers as $customer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                                    {{ strtoupper(substr($customer->customer_name ?? 'C', 0, 2)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $customer->customer_name ?? 'Client inconnu' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Client depuis
                                                    {{ $customer->first_order_date?->format('d/m/Y') ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 break-words max-w-xs">
                                            {{ $customer->customer_email }}</div>
                                        @if ($customer->customer_phone)
                                            <div class="text-sm text-gray-500">{{ $customer->customer_phone }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span
                                                class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $customer->order_count }}
                                            </span>
                                            @if ($customer->order_count >= 5)
                                                <span
                                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    ⭐ VIP
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-1 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm font-medium text-green-600">
                                                {{ $customer->completed_order_count ?? 0 }}
                                            </span>
                                            @if ($customer->order_count > 0)
                                                <span class="ml-2 text-xs text-gray-500">
                                                    ({{ round(($customer->completed_order_count / $customer->order_count) * 100) }}%)
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $customer->last_order_date?->format('d/m/Y') ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $customer->last_order_date?->diffForHumans() ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @php
                                            $emailKey = $customer->customer_email
                                                ? str_replace(
                                                    '@',
                                                    '',
                                                    hash('md5', strtolower($customer->customer_email)),
                                                )
                                                : '';
                                        @endphp

                                        <a href="{{ route('admin.customers.show', $emailKey) }}"
                                            class="text-blue-600 hover:text-blue-900 hover:underline">
                                            Voir détails
                                        </a>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun client</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if (request('search'))
                                                Aucun client ne correspond à votre recherche.
                                            @else
                                                Aucun client n'a encore passé de commande.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($customers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $customers->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            {{-- Résumé de la page --}}
            <div class="mt-4 text-sm text-gray-600">
                Affichage de {{ $customers->firstItem() ?? 0 }} à {{ $customers->lastItem() ?? 0 }} sur
                {{ $customers->total() }} clients
            </div>
        </div>
    </div>
@endsection

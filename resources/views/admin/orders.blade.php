@extends('layouts.app')

@section('title', 'Commandes')

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ================= HEADER ================= --}}
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Commandes</h1>
                    <p class="text-gray-600 mt-1">Filtrer, analyser et gérer les commandes</p>
                </div>
                <a href="{{ route('admin.dashboard') }}"
                    class="px-4 py-2 bg-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-300">
                    ← Retour
                </a>
            </div>

            {{-- ================= FILTRES AVANCÉS ================= --}}
            <form method="GET" class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                    {{-- Recherche --}}
                    <div class="md:col-span-2">
                        <label class="text-xs font-medium text-gray-500">Recherche</label>
                        <input type="text" name="q" value="{{ isset($filters['q']) ? $filters['q'] : '' }}"
                            placeholder="Commande, client, email..." class="w-full mt-1 rounded-md border-gray-300 text-sm">
                    </div>

                    {{-- Statut --}}
                    <div>
                        <label class="text-xs font-medium text-gray-500">Statut</label>
                        <select name="status" class="w-full mt-1 rounded-md border-gray-300 text-sm">
                            <option value="">Tous</option>
                            @foreach (['pending', 'processing', 'completed', 'cancelled', 'on-hold'] as $status)
                                <option value="{{ $status }}" 
                                        @selected((isset($filters['status']) && $filters['status'] === $status))>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nombre de commandes --}}
                    <div>
                        <label class="text-xs font-medium text-gray-500">Nombre d'articles</label>
                        <select name="order_count" class="w-full mt-1 rounded-md border-gray-300 text-sm">
                            <option value="">Tous</option>
                            <option value="1-2" @selected((isset($filters['order_count']) && $filters['order_count'] === '1-2'))>1 à 2</option>
                            <option value="3-5" @selected((isset($filters['order_count']) && $filters['order_count'] === '3-5'))>3 à 5</option>
                            <option value="5+" @selected((isset($filters['order_count']) && $filters['order_count'] === '5+'))>Plus de 5</option>
                        </select>
                    </div>

                    {{-- Date début --}}
                    <div>
                        <label class="text-xs font-medium text-gray-500">Du</label>
                        <input type="date" name="from" value="{{ isset($filters['from']) ? $filters['from'] : '' }}"
                            class="w-full mt-1 rounded-md border-gray-300 text-sm">
                    </div>

                    {{-- Date fin --}}
                    <div>
                        <label class="text-xs font-medium text-gray-500">Au</label>
                        <input type="date" name="to" value="{{ isset($filters['to']) ? $filters['to'] : '' }}"
                            class="w-full mt-1 rounded-md border-gray-300 text-sm">
                    </div>
                </div>

                <div class="flex justify-between items-center mt-6">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md text-xs font-semibold hover:bg-blue-700">
                        Filtrer
                    </button>

                    <a href="{{ route('admin.orders') }}" class="text-xs text-gray-500 hover:underline">
                        Réinitialiser
                    </a>
                </div>

                {{-- Champs cachés pour conserver le tri --}}
                <input type="hidden" name="sort_by" value="{{ isset($sort_by) ? $sort_by : 'order_date' }}">
                <input type="hidden" name="sort_order" value="{{ isset($sort_order) ? $sort_order : 'desc' }}">
            </form>

            {{-- Tableau des commandes --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'order_number', 'sort_order' => (isset($sort_by) && $sort_by === 'order_number' && isset($sort_order) && $sort_order === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 flex items-center gap-1">
                                    N° Commande
                                    @if (isset($sort_by) && $sort_by === 'order_number')
                                        <span class="text-blue-600">{{ isset($sort_order) && $sort_order === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'customer_name', 'sort_order' => (isset($sort_by) && $sort_by === 'customer_name' && isset($sort_order) && $sort_order === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 flex items-center gap-1">
                                    Client
                                    @if (isset($sort_by) && $sort_by === 'customer_name')
                                        <span class="text-blue-600">{{ isset($sort_order) && $sort_order === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_order' => (isset($sort_by) && $sort_by === 'status' && isset($sort_order) && $sort_order === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 flex items-center gap-1">
                                    Statut
                                    @if (isset($sort_by) && $sort_by === 'status')
                                        <span class="text-blue-600">{{ isset($sort_order) && $sort_order === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total', 'sort_order' => (isset($sort_by) && $sort_by === 'total' && isset($sort_order) && $sort_order === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 flex items-center gap-1">
                                    Montant
                                    @if (isset($sort_by) && $sort_by === 'total')
                                        <span class="text-blue-600">{{ isset($sort_order) && $sort_order === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'order_date', 'sort_order' => (isset($sort_by) && $sort_by === 'order_date' && isset($sort_order) && $sort_order === 'asc') ? 'desc' : 'asc']) }}"
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 flex items-center gap-1">
                                    Date
                                    @if (isset($sort_by) && $sort_by === 'order_date')
                                        <span class="text-blue-600">{{ isset($sort_order) && $sort_order === 'asc' ? '↑' : '↓' }}</span>
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
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $order->order_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->customer_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->customer_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if ($order->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($order->total, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->order_date?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Aucune commande trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            </div>

        </div>
    </div>
@endsection
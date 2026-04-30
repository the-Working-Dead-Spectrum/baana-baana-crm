@extends('layouts.app')

@section('title', 'Segmentation Client')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Segmentation Client</h1>
            <p class="text-gray-600 mt-1">Analysez et ciblez vos clients par segments</p>
        </div>

        {{-- Segments prédéfinis --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($segments as $key => $segment)
                <a href="{{ route('segments.index', ['segment' => $key]) }}" 
                   class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200 {{ $selectedSegment === $key ? 'ring-2 ring-blue-500' : '' }}">
                    
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                {{ $segment['name'] }}
                            </h3>
                            <p class="text-sm text-gray-600">{{ $segment['description'] }}</p>
                        </div>
                        
                        <div class="ml-4">
                            <div class="w-12 h-12 rounded-full bg-{{ $segment['color'] }}-100 flex items-center justify-center">
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

        {{-- Actions et filtres --}}
        @if($selectedSegment !== 'all')
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ $segments[$selectedSegment]['name'] }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $customers->total() }} client(s) dans ce segment
                        </p>
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('segments.export', $selectedSegment) }}" 
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
        @endif

        {{-- Liste des clients --}}
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
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-100 to-purple-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-sm font-bold text-blue-600">
                                                {{ strtoupper(substr($customer->customer_name ?? 'C', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $customer->customer_name ?? 'Client inconnu' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $customer->customer_email }}</div>
                                    @if($customer->customer_phone)
                                        <div class="text-sm text-gray-500">{{ $customer->customer_phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $customer->order_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($customer->total_spent, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($customer->average_order ?? 0, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($customer->last_order_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @php
                                        $emailKey = $customer->customer_email
                                            ? str_replace('@', '', hash('md5', strtolower($customer->customer_email)))
                                            : '';
                                    @endphp
                                    <a href="{{ route('admin.customers.show', urlencode($emailKey)) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        Voir profil
                                    </a>
                                </td>
                            </tr>
                        @empty
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
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($customers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal pour lancer une campagne (placeholder) --}}
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

@push('scripts')
<script>
function openCampaignModal() {
    document.getElementById('campaignModal').classList.remove('hidden');
}

function closeCampaignModal() {
    document.getElementById('campaignModal').classList.add('hidden');
}
</script>
@endpush
@endsection
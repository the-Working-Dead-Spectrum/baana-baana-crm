@extends('layouts.app')

@section('title', 'Logs de Synchronisation')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Logs de Synchronisation</h1>
                <p class="text-gray-600 mt-2">Historique des synchronisations automatiques et manuelles</p>
            </div>
            <div>
                <a href="{{ route('admin.sync.logs') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    ← Retour
                </a>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Tous les types</option>
                            <option value="orders" {{ request('type') == 'orders' ? 'selected' : '' }}>Commandes</option>
                            <option value="products" {{ request('type') == 'products' ? 'selected' : '' }}>Produits</option>
                            <option value="creators" {{ request('type') == 'creators' ? 'selected' : '' }}>Créateurs</option>
                            <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Tout</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Tous les statuts</option>
                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Succès</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Échec</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partiel</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date début</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Filtrer
                        </button>
                        @if(request()->anyFilled(['type', 'status', 'start_date']))
                            <a href="{{ route('admin.sync.logs') }}" 
                               class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Table des logs -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($logs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date/Heure
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Enregistrements
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Durée
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $log->started_at->format('d/m/Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $log->started_at->format('H:i:s') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $log->sync_type === 'orders' ? 'bg-blue-100 text-blue-800' : 
                                                   ($log->sync_type === 'products' ? 'bg-purple-100 text-purple-800' : 
                                                   ($log->sync_type === 'creators' ? 'bg-green-100 text-green-800' : 
                                                   'bg-gray-100 text-gray-800')) }}">
                                                {{ ucfirst($log->sync_type) }}
                                            </span>
                                            @if(isset($log->metadata['trigger']) && $log->metadata['trigger'] === 'manual')
                                                <span class="ml-1 text-xs text-yellow-600" title="Sync manuelle">👤</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $log->status === 'success' ? 'bg-green-100 text-green-800' : 
                                                   ($log->status === 'failed' ? 'bg-red-100 text-red-800' : 
                                                   ($log->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 
                                                   'bg-gray-100 text-gray-800')) }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($log->total_records) }}
                                            </div>
                                            @if($log->failed_records > 0)
                                                <div class="text-xs text-red-500">
                                                    {{ $log->failed_records }} échecs
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log->duration_ms }} ms
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button type="button" 
                                                    onclick="showLogDetails({{ $log->id }})"
                                                    class="text-blue-600 hover:text-blue-900">
                                                Détails
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun log de synchronisation</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Aucune synchronisation n'a été effectuée pour le moment.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les détails -->
<div id="logDetailsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Détails de la synchronisation</h3>
                    <button type="button" 
                            onclick="closeLogDetails()"
                            class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4">
                <div id="logDetailsContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button type="button" 
                        onclick="closeLogDetails()"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showLogDetails(logId) {
    fetch(`/admin/sync/logs/${logId}/details`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('logDetailsContent');
            content.innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Type</p>
                            <p class="text-sm text-gray-900">${data.sync_type}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Statut</p>
                            <p class="text-sm ${data.status === 'success' ? 'text-green-600' : data.status === 'failed' ? 'text-red-600' : 'text-yellow-600'}">
                                ${data.status}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Début</p>
                            <p class="text-sm text-gray-900">${new Date(data.started_at).toLocaleString()}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Fin</p>
                            <p class="text-sm text-gray-900">${data.completed_at ? new Date(data.completed_at).toLocaleString() : 'En cours'}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Durée</p>
                            <p class="text-sm text-gray-900">${data.duration_ms} ms</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Enregistrements traités</p>
                            <p class="text-sm text-gray-900">${data.total_records}</p>
                        </div>
                    </div>
                    
                    ${data.error_message ? `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-sm font-medium text-red-800">Erreur</p>
                            <p class="text-sm text-red-600 mt-1">${data.error_message}</p>
                        </div>
                    ` : ''}
                    
                    ${data.metadata ? `
                        <div>
                            <p class="text-sm font-medium text-gray-500">Métadonnées</p>
                            <pre class="mt-1 text-xs bg-gray-50 p-3 rounded overflow-auto max-h-40">${JSON.stringify(data.metadata, null, 2)}</pre>
                        </div>
                    ` : ''}
                </div>
            `;
            
            document.getElementById('logDetailsModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des détails');
        });
}

function closeLogDetails() {
    document.getElementById('logDetailsModal').classList.add('hidden');
}

// Fermer avec ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLogDetails();
    }
});
</script>
@endsection
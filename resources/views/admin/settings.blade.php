@extends('layouts.app')

@section('title', 'Paramètres de Synchronisation')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Paramètres</h1>
                <p class="text-gray-600 mt-2">Gérez la synchronisation avec WordPress</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Retour au Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Configuration WordPress -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Carte de configuration -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Configuration WordPress</h2>
                        
                        <div class="space-y-4">
                            <!-- URL WordPress -->
                            <div class="border-b border-gray-200 pb-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">URL WordPress</h3>
                                        <p class="text-sm text-gray-500 mt-1">URL de votre site WordPress</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                            {{ !empty($wordpressUrl) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ !empty($wordpressUrl) ? 'Configurée' : 'Non configurée' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <code class="text-sm bg-gray-100 px-3 py-2 rounded-md text-gray-800 block overflow-x-auto">
                                        {{ $wordpressUrl ?? 'Non définie' }}
                                    </code>
                                </div>
                            </div>

                            <!-- Webhook -->
                            <div class="border-b border-gray-200 pb-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">Webhook</h3>
                                        <p class="text-sm text-gray-500 mt-1">Configuration du webhook pour les mises à jour en temps réel</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                            {{ $webhookConfigured ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $webhookConfigured ? 'Configuré' : 'À configurer' }}
                                        </span>
                                    </div>
                                </div>
                                @if(!$webhookConfigured)
                                    <div class="mt-3 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700">
                                                    Le secret du webhook n'est pas configuré. Les mises à jour en temps réel ne fonctionneront pas.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Actions</h3>
                                <div class="flex space-x-3">
                                    <button type="button"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Synchroniser manuellement
                                    </button>
                                    <button type="button"
                                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Régénérer la clé API
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documentation -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Documentation API</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <code class="text-sm text-gray-800 block space-y-2">
                                <span class="block">// Endpoint pour la synchronisation manuelle</span>
                                <span class="block text-blue-600">POST /api/sync/creators</span>
                                <span class="block">Headers: X-API-Key: votre_clé_secrète</span>
                                <br>
                                <span class="block">// Webhook WordPress</span>
                                <span class="block text-blue-600">POST /api/webhook/wordpress</span>
                                <span class="block">Headers: X-WP-Webhook-Signature: signature</span>
                            </code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logs de synchronisation -->
            <div class="space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Dernières synchronisations</h2>
                            <span class="text-xs text-gray-500">{{ $syncLogs->count() }} logs</span>
                        </div>
                        
                        @if($syncLogs->count() > 0)
                            <div class="space-y-4">
                                @foreach($syncLogs as $log)
                                    <div class="border-l-4 {{ $log->status === 'success' ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50' }} pl-4 py-3">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $log->operation_type }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $log->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                {{ $log->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $log->status === 'success' ? 'Succès' : 'Erreur' }}
                                            </span>
                                        </div>
                                        @if($log->details)
                                            <p class="text-sm text-gray-600 mt-2">
                                                {{ Str::limit($log->details, 60) }}
                                            </p>
                                        @endif
                                        @if($log->record_count)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $log->record_count }} enregistrement(s)
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="#" 
                                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-900">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Voir tous les logs
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun log disponible</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Aucune synchronisation n'a encore été effectuée.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations système -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Informations système</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Dernière synchro :</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $syncLogs->first() ? $syncLogs->first()->created_at->diffForHumans() : 'Jamais' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Environnement :</span>
                                <span class="text-sm font-medium text-gray-900">{{ app()->environment() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Version Laravel :</span>
                                <span class="text-sm font-medium text-gray-900">{{ app()->version() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
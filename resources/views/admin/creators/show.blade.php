@extends('layouts.app')

@section('title', 'Détails du créateur - ' . $creator->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-6">
        <a href="{{ route('admin.creators') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Retour à la liste
        </a>
        <h1 class="text-3xl font-bold text-gray-800">{{ $creator->name }}</h1>
    </div>

    <!-- Card principale -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Badge de statut -->
        <div class="bg-gray-50 px-6 py-4 border-b">
            <span class="px-3 py-1 rounded-full text-sm font-semibold
                {{ $creator->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ ucfirst($creator->status) }}
            </span>
        </div>

        <!-- Informations du créateur -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations personnelles -->
                <div>
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Informations personnelles</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <span class="font-medium text-gray-600">ID:</span>
                            <span class="text-gray-800">{{ $creator->id }}</span>
                        </div>

                        <div>
                            <span class="font-medium text-gray-600">Email:</span>
                            <a href="mailto:{{ $creator->email }}" class="text-blue-600 hover:underline">
                                {{ $creator->email }}
                            </a>
                        </div>

                        @if($creator->phone)
                        <div>
                            <span class="font-medium text-gray-600">Téléphone:</span>
                            <a href="tel:{{ $creator->phone }}" class="text-blue-600 hover:underline">
                                {{ $creator->phone }}
                            </a>
                        </div>
                        @endif

                        @if($creator->address)
                        <div>
                            <span class="font-medium text-gray-600">Adresse:</span>
                            <span class="text-gray-800">{{ $creator->address }}</span>
                        </div>
                        @endif

                        <div>
                            <span class="font-medium text-gray-600">Marque:</span>
                            <span class="text-gray-800">{{ $creator->brand_slug }}</span>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div>
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Statistiques</h2>
                    
                    <div class="space-y-3">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Total des ventes</div>
                            <div class="text-2xl font-bold text-blue-600">
                                {{ number_format($creator->total_sales, 2) }} FCFA
                            </div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Nombre de commandes</div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ $creator->total_orders }}
                            </div>
                        </div>

                        @if($creator->last_order_date)
                        <div>
                            <span class="font-medium text-gray-600">Dernière commande:</span>
                            <span class="text-gray-800">
                                {{ $creator->last_order_date->format('d/m/Y à H:i') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations techniques -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Informations techniques</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    @if($creator->wp_creator_id)
                    <div>
                        <span class="font-medium text-gray-600">ID WordPress:</span>
                        <span class="text-gray-800">{{ $creator->wp_creator_id }}</span>
                    </div>
                    @endif

                    @if($creator->user_id)
                    <div>
                        <span class="font-medium text-gray-600">ID Utilisateur:</span>
                        <span class="text-gray-800">{{ $creator->user_id }}</span>
                    </div>
                    @endif

                    @if($creator->last_synced_at)
                    <div>
                        <span class="font-medium text-gray-600">Dernière synchro:</span>
                        <span class="text-gray-800">
                            {{ $creator->last_synced_at->format('d/m/Y à H:i') }}
                        </span>
                    </div>
                    @endif

                    <div>
                        <span class="font-medium text-gray-600">Créé le:</span>
                        <span class="text-gray-800">
                            {{ $creator->created_at->format('d/m/Y à H:i') }}
                        </span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-600">Modifié le:</span>
                        <span class="text-gray-800">
                            {{ $creator->updated_at->format('d/m/Y à H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t flex gap-3">
            <a href="#"
                {{-- {{ route('creators.edit', $creator->id) }}  --}}
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                Modifier
            </a>
            
            <form action="#" 
                  method="POST" 
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce créateur ?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
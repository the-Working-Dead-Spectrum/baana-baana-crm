@extends('layouts.app')

@section('header', 'Détails de l\'utilisateur')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['title' => 'Administration', 'url' => '#'],
            ['title' => 'Utilisateurs', 'url' => route('admin.users.index')],
            ['title' => $user->name, 'url' => route('admin.users.show', $user)],
        ];
    @endphp
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Détails de l'utilisateur
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Informations complètes sur {{ $user->name }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 
                               ($user->role === 'creator' ? 'bg-green-100 text-green-800' : 
                               ($user->role === 'logistic' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ $user->role === 'admin' ? 'Administrateur' : 
                               ($user->role === 'creator' ? 'Créateur' : 
                               ($user->role === 'logistic' ? 'Logistique' : 'Utilisateur')) }}
                        </span>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Informations principales -->
                    <div class="lg:col-span-2">
                        <div class="space-y-8">
                            <!-- Informations personnelles -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h4>
                                
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Adresse email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Rôle</dt>
                                        <dd class="mt-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                                   ($user->role === 'creator' ? 'bg-green-100 text-green-800' : 
                                                   ($user->role === 'logistic' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                                {{ $user->role === 'admin' ? 'Administrateur' : 
                                                   ($user->role === 'creator' ? 'Créateur' : 
                                                   ($user->role === 'logistic' ? 'Logistique' : 'Utilisateur')) }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Statut du compte</dt>
                                        <dd class="mt-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Métadonnées -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Métadonnées</h4>
                                
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Date d'inscription</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $user->created_at->format('d/m/Y H:i') }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Dernière mise à jour</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $user->updated_at->format('d/m/Y H:i') }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">ID Utilisateur</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->id }}</dd>
                                    </div>
                                    @if($user->email_verified_at)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Email vérifié</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                {{ $user->email_verified_at->format('d/m/Y H:i') }}
                                            </dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Avatar et actions -->
                    <div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <!-- Avatar -->
                            <div class="text-center mb-6">
                                <div class="mx-auto h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 text-3xl font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="space-y-3">
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Modifier l'utilisateur
                                </a>
                                
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                onclick="return confirm('{{ $user->is_active ? 'Désactiver' : 'Activer' }} cet utilisateur ?')">
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($user->is_active)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                @endif
                                            </svg>
                                            {{ $user->is_active ? 'Désactiver le compte' : 'Activer le compte' }}
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('admin.users.index') }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Si c'est un créateur, afficher les informations du profil créateur -->
        @if($user->role === 'creator' && $user->creator)
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Informations du créateur
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Profil créateur associé à cet utilisateur
                </p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-3">Détails du créateur</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nom de marque</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->creator->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Slug de marque</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->creator->brand_slug }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email du créateur</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->creator->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $user->creator->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           ($user->creator->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $user->creator->status === 'active' ? 'Actif' : 
                                           ($user->creator->status === 'inactive' ? 'Inactif' : 'Suspendu') }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-3">Dates</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $user->creator->created_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dernière mise à jour</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $user->creator->updated_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID du créateur</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->creator->id }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('admin.creators') }}" 
                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-500">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Gérer tous les créateurs
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
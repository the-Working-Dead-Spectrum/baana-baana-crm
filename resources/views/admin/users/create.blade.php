@extends('layouts.app')

@section('header', 'Créer un nouvel utilisateur')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['title' => 'Administration', 'url' => '#'],
            ['title' => 'Utilisateurs', 'url' => route('admin.users.index')],
            ['title' => 'Créer', 'url' => route('admin.users.create')],
        ];
    @endphp
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Nouvel utilisateur
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Créez un nouveau compte utilisateur
                </p>
            </div>
            
            <form action="{{ route('admin.users.store') }}" method="POST" class="px-4 py-5 sm:p-6">
                @csrf
                
                <div class="space-y-6">
                    <!-- Informations personnelles -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h4>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Nom complet *
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name') }}"
                                       required
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Adresse email *
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email') }}"
                                       required
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="role" class="block text-sm font-medium text-gray-700">
                                    Rôle *
                                </label>
                                <select id="role" 
                                        name="role" 
                                        required
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">Sélectionnez un rôle</option>
                                    @foreach($availableRoles as $roleOption)
                                        <option value="{{ $roleOption }}" {{ old('role') == $roleOption ? 'selected' : '' }}>
                                            {{ ucfirst($roleOption) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Sécurité</h4>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Mot de passe *
                                </label>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       required
                                       minlength="8"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Minimum 8 caractères
                                </p>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    Confirmer le mot de passe *
                                </label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       required
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- Informations sur les rôles -->
                    <div id="roleInfo" class="hidden bg-gray-50 p-4 rounded-lg">
                        <h5 class="text-sm font-medium text-gray-900 mb-2">Description du rôle sélectionné :</h5>
                        <p id="roleDescription" class="text-sm text-gray-600"></p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Descriptions des rôles
const roleDescriptions = {
    'admin': 'Administrateur complet avec accès à toutes les fonctionnalités du système. Peut gérer les utilisateurs, les rôles, les paramètres, et toutes les données.',
    'creator': 'Créateur / Vendeur avec accès à ses propres produits, commandes et statistiques. Peut gérer son inventaire et suivre ses ventes. Un profil créateur sera automatiquement créé.',
    'logistic': 'Personnel logistique avec accès aux commandes pour la gestion des expéditions et suivi des livraisons.',
    'user': 'Utilisateur standard avec accès limité à son compte personnel et à ses commandes.'
};

// Gérer l'affichage de la description du rôle
document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    const infoDiv = document.getElementById('roleInfo');
    const descriptionEl = document.getElementById('roleDescription');
    
    if (role && roleDescriptions[role]) {
        descriptionEl.textContent = roleDescriptions[role];
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
});

// Initialiser si une valeur est déjà sélectionnée
const initialRole = document.getElementById('role').value;
if (initialRole && roleDescriptions[initialRole]) {
    document.getElementById('roleDescription').textContent = roleDescriptions[initialRole];
    document.getElementById('roleInfo').classList.remove('hidden');
}
</script>
@endsection
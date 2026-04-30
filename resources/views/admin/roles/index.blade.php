@extends('layouts.app')

@section('header', 'Gestion des Rôles & Permissions')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['title' => 'Administration', 'url' => '#'],
            ['title' => 'Rôles & Permissions', 'url' => route('admin.roles.index')],
        ];
    @endphp
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Rôles & Permissions</h1>
                    <p class="text-gray-600 mt-1">Gérer les rôles et leurs permissions d'accès</p>
                </div>
                <div class="text-sm text-gray-500">
                    Total: {{ $totalUsers }} utilisateurs
                </div>
            </div>

            <!-- Stats des rôles -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @foreach($roles as $role)
                    <a href="{{ route('admin.roles.users', $role['name']) }}" 
                       class="block bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full 
                                {{ $role['name'] === 'admin' ? 'bg-purple-100' : 
                                   ($role['name'] === 'creator' ? 'bg-green-100' : 
                                   ($role['name'] === 'logistic' ? 'bg-yellow-100' : 'bg-gray-100')) }} 
                                flex items-center justify-center">
                                <svg class="h-5 w-5 
                                    {{ $role['name'] === 'admin' ? 'text-purple-600' : 
                                       ($role['name'] === 'creator' ? 'text-green-600' : 
                                       ($role['name'] === 'logistic' ? 'text-yellow-600' : 'text-gray-600')) }}" 
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 capitalize">
                                    {{ $role['name'] === 'admin' ? 'Administrateurs' : 
                                       ($role['name'] === 'creator' ? 'Créateurs' : 
                                       ($role['name'] === 'logistic' ? 'Logistique' : 'Utilisateurs')) }}
                                </p>
                                <p class="text-2xl font-semibold 
                                    {{ $role['name'] === 'admin' ? 'text-purple-600' : 
                                       ($role['name'] === 'creator' ? 'text-green-600' : 
                                       ($role['name'] === 'logistic' ? 'text-yellow-600' : 'text-gray-600')) }}">
                                    {{ $role['users_count'] }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            {{ $role['description'] }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Tableau des rôles avec permissions -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Détail des permissions par rôle
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Configurez les permissions d'accès pour chaque rôle
                </p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rôle
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Permissions
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($roles as $role)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                            {{ $role['name'] === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                               ($role['name'] === 'creator' ? 'bg-green-100 text-green-800' : 
                                               ($role['name'] === 'logistic' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($role['name']) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $role['description'] }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $role['users_count'] }} utilisateur(s)
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @if(in_array('all', $role['permissions']))
                                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                                Toutes les permissions
                                            </span>
                                        @else
                                            @foreach($role['permissions'] as $permission)
                                                @if(isset($permissions[$permission]))
                                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                        {{ $permissions[$permission] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button type="button" 
                                                onclick="openEditPermissionsModal('{{ $role['name'] }}', {{ json_encode($role['permissions']) }})"
                                                class="text-blue-600 hover:text-blue-900">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <a href="{{ route('admin.roles.users', $role['name']) }}" 
                                           class="text-green-600 hover:text-green-900">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0H21m-4.5 0H15m4.5 0h.008v.008h-.008V15zm0 0h.008v.008h-.008V15z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Guide des permissions -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Guide des permissions
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Description détaillée de chaque permission
                </p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permissions as $key => $description)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $description }}</h4>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <code class="bg-gray-100 px-1 py-0.5 rounded">perm:{{ $key }}</code>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour éditer les permissions -->
<div id="editPermissionsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <form id="permissionsForm" method="POST" action="{{ route('admin.roles.update-permissions') }}">
                @csrf
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">
                            Éditer les permissions
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="modalDescription">
                                Sélectionnez les permissions pour ce rôle.
                            </p>
                        </div>
                        
                        <!-- Champ caché pour le rôle -->
                        <input type="hidden" name="role" id="modalRole">
                        
                        <!-- Liste des permissions -->
                        <div class="mt-4 max-h-64 overflow-y-auto">
                            <fieldset>
                                <legend class="sr-only">Permissions</legend>
                                <div class="space-y-2">
                                    @foreach($permissions as $key => $description)
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="perm-{{ $key }}" 
                                                       name="permissions[]" 
                                                       value="{{ $key }}" 
                                                       type="checkbox" 
                                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="perm-{{ $key }}" class="font-medium text-gray-700">
                                                    {{ $description }}
                                                </label>
                                                <p class="text-gray-500">
                                                    <code class="text-xs">perm:{{ $key }}</code>
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                        Enregistrer
                    </button>
                    <button type="button" 
                            onclick="closeEditPermissionsModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fonctions pour gérer le modal
function openEditPermissionsModal(role, currentPermissions) {
    const modal = document.getElementById('editPermissionsModal');
    const roleInput = document.getElementById('modalRole');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    
    // Mettre à jour le titre et la description
    const roleNames = {
        'admin': 'Administrateur',
        'creator': 'Créateur',
        'logistic': 'Logistique',
        'user': 'Utilisateur'
    };
    
    modalTitle.textContent = `Permissions du rôle : ${roleNames[role] || role}`;
    modalDescription.textContent = 'Sélectionnez les permissions pour ce rôle.';
    roleInput.value = role;
    
    // Décocher toutes les cases
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Cocher les permissions actuelles
    currentPermissions.forEach(permission => {
        const checkbox = document.getElementById(`perm-${permission}`);
        if (checkbox) {
            checkbox.checked = true;
        }
        
        // Si "all" est coché, cocher toutes les permissions
        if (permission === 'all') {
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                cb.checked = true;
            });
        }
    });
    
    // Afficher le modal
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeEditPermissionsModal() {
    const modal = document.getElementById('editPermissionsModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Fermer le modal en cliquant en dehors
document.getElementById('editPermissionsModal').addEventListener('click', function(e) {
    if (e.target.id === 'editPermissionsModal') {
        closeEditPermissionsModal();
    }
});

// Gérer la touche Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditPermissionsModal();
    }
});

// Gérer le changement de "all"
document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.value === 'all' && this.checked) {
            // Si "all" est coché, cocher toutes les autres
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                if (cb.value !== 'all') {
                    cb.checked = true;
                }
            });
        } else if (this.value === 'all' && !this.checked) {
            // Si "all" est décoché, décocher toutes les autres
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                if (cb.value !== 'all') {
                    cb.checked = false;
                }
            });
        }
    });
});
</script>
@endsection
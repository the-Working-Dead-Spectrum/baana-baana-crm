@extends('layouts.app')

@section('header', 'Modifier l\'utilisateur')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['title' => 'Administration', 'url' => '#'],
            ['title' => 'Utilisateurs', 'url' => route('admin.users.index')],
            ['title' => 'Modifier ' . $user->name, 'url' => route('admin.users.edit', $user)],
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
                                Modifier l'utilisateur
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Mettre à jour les informations de {{ $user->name }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            {{ $user->role === 'admin'
                                ? 'bg-purple-100 text-purple-800'
                                : ($user->role === 'creator'
                                    ? 'bg-green-100 text-green-800'
                                    : ($user->role === 'logistic'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-gray-100 text-gray-800')) }}">
                                {{ $user->role === 'admin'
                                    ? 'Administrateur'
                                    : ($user->role === 'creator'
                                        ? 'Créateur'
                                        : ($user->role === 'logistic'
                                            ? 'Logistique'
                                            : 'Utilisateur')) }}
                            </span>
                            <span
                                class="px-2 py-1 text-xs rounded-full 
                            {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="px-4 py-5 sm:p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-8">
                        <!-- Informations personnelles -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h4>

                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Nom complet *
                                    </label>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $user->name) }}" required
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Adresse email *
                                    </label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $user->email) }}" required
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="role" class="block text-sm font-medium text-gray-700">
                                        Rôle *
                                    </label>
                                    <select id="role" name="role" required
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">Sélectionnez un rôle</option>
                                        @foreach ($availableRoles as $roleOption)
                                            @if ($roleOption !== 'user')
                                                <option value="{{ $roleOption }}"
                                                    {{ old('role', $user->role) == $roleOption ? 'selected' : '' }}>
                                                    {{ ucfirst($roleOption) }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">
                                    Statut
                                </label>
                                <div class="mt-2">
                                    <div class="flex items-center">
                                        <input id="status_active" 
                                               name="status" 
                                               type="radio" 
                                               value="1"
                                               {{ $user->is_active ? 'checked' : '' }}
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                        <label for="status_active" class="ml-3 block text-sm font-medium text-gray-700">
                                            Actif
                                        </label>
                                    </div>
                                    <div class="flex items-center mt-2">
                                        <input id="status_inactive" 
                                               name="status" 
                                               type="radio" 
                                               value="0"
                                               {{ !$user->is_active ? 'checked' : '' }}
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                        <label for="status_inactive" class="ml-3 block text-sm font-medium text-gray-700">
                                            Inactif
                                        </label>
                                    </div>
                                </div>
                            </div> --}}
                            </div>
                        </div>

                        <!-- Mot de passe (optionnel) -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-lg font-medium text-gray-900">Sécurité</h4>
                                <button type="button" onclick="togglePasswordFields()"
                                    class="text-sm text-blue-600 hover:text-blue-500">
                                    Changer le mot de passe
                                </button>
                            </div>

                            <div id="passwordFields" class="hidden">
                                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <label for="password" class="block text-sm font-medium text-gray-700">
                                            Nouveau mot de passe
                                        </label>
                                        <input type="password" name="password" id="password" minlength="8"
                                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-xs text-gray-500">
                                            Laissez vide pour conserver le mot de passe actuel
                                        </p>
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                            Confirmer le nouveau mot de passe
                                        </label>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                            </div>

                            <div id="passwordInfo" class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">
                                    Le mot de passe ne sera modifié que si vous remplissez les champs ci-dessus.
                                </p>
                            </div>
                        </div>

                        <!-- Informations sur le rôle -->
                        <div id="roleInfo" class="hidden bg-blue-50 p-4 rounded-lg">
                            <h5 class="text-sm font-medium text-blue-900 mb-2">Description du rôle sélectionné :</h5>
                            <p id="roleDescription" class="text-sm text-blue-700"></p>
                        </div>

                        <!-- Informations sur l'utilisateur -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Informations supplémentaires</h4>

                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
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
                                @if ($user->email_verified_at)
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

                    <!-- Actions -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('admin.users.index') }}"
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Annuler
                        </a>

                        <!-- Bouton de suppression (si ce n'est pas l'utilisateur courant) -->
                        @if ($user->id !== auth()->id())
                            <button type="button" onclick="confirmDelete()"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Supprimer
                            </button>
                        @endif

                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- Si c'est un créateur, afficher les informations du profil créateur -->
            @if ($user->role === 'creator' && $user->creator)
                <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Informations du créateur
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Profil créateur associé
                        </p>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nom de marque</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->creator->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Slug de marque</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->creator->brand_slug }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Statut du créateur</dt>
                                <dd class="mt-1">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $user->creator->status === 'active'
                                    ? 'bg-green-100 text-green-800'
                                    : ($user->creator->status === 'inactive'
                                        ? 'bg-gray-100 text-gray-800'
                                        : 'bg-red-100 text-red-800') }}">
                                        {{ $user->creator->status === 'active'
                                            ? 'Actif'
                                            : ($user->creator->status === 'inactive'
                                                ? 'Inactif'
                                                : 'Suspendu') }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $user->creator->created_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-4">
                            <a href="{{ route('admin.creators') }}" class="text-sm text-blue-600 hover:text-blue-500">
                                Voir tous les détails du créateur →
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">
                            Supprimer l'utilisateur
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ $user->name }}</strong> ?
                                Cette action est irréversible. Toutes les données associées à cet utilisateur seront
                                également supprimées.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Supprimer
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
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

        // Fonction pour afficher/masquer les champs de mot de passe
        function togglePasswordFields() {
            const passwordFields = document.getElementById('passwordFields');
            const passwordInfo = document.getElementById('passwordInfo');

            if (passwordFields.classList.contains('hidden')) {
                passwordFields.classList.remove('hidden');
                passwordInfo.classList.add('hidden');
                document.getElementById('password').focus();
            } else {
                passwordFields.classList.add('hidden');
                passwordInfo.classList.remove('hidden');
                // Effacer les champs de mot de passe
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
            }
        }

        // Gérer la modal de suppression
        function confirmDelete() {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Fermer la modal en cliquant en dehors
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target.id === 'deleteModal') {
                closeDeleteModal();
            }
        });

        // Gérer la touche Échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

        // Validation du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;

            // Vérifier que les mots de passe correspondent si remplis
            if (password && password !== passwordConfirmation) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                document.getElementById('password_confirmation').focus();
                return false;
            }

            // Vérifier la longueur du mot de passe si rempli
            if (password && password.length < 8) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 8 caractères.');
                document.getElementById('password').focus();
                return false;
            }

            return true;
        });
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Mon Profil</h1>
                
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('creator.profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Informations personnelles -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations Personnelles</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                                    <input type="text" name="name" id="name" 
                                           value="{{ old('name', $user->name) }}"
                                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                           required>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" 
                                           value="{{ old('email', $user->email) }}"
                                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                           required>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Coordonnées</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                    <input type="text" name="phone" id="phone" 
                                           value="{{ old('phone', $creator->phone) }}"
                                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label for="address" class="block text-sm font-medium text-gray-700">Adresse</label>
                                <textarea name="address" id="address" rows="3"
                                          class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('address', $creator->address) }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Informations de la marque (read-only) -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations Marque</h3>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Slug de la marque</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $creator->brand_slug }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Taux de commission</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $creator->commission_rate }}%</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">ID WordPress</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $creator->wp_creator_id }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Méthode de paiement</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $creator->payment_method }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Pour modifier ces informations, contactez l'administrateur.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                        <a href="{{ route('creator.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
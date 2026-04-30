<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-indigo-100 px-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 space-y-6">

            <!-- Logo / Title -->
            <div class="text-center">
                <x-application-logo class="mx-auto h-12 w-auto text-indigo-600" />
                <h2 class="mt-4 text-2xl font-bold text-gray-800">
                    Connexion
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Accédez à votre espace sécurisé
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 12H8m0 0l4-4m-4 4l4 4" />
                            </svg>
                        </span>
                        <x-text-input id="email"
                                      class="pl-10 block w-full rounded-lg"
                                      type="email"
                                      name="email"
                                      :value="old('email')"
                                      required
                                      autofocus />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Mot de passe')" />
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 11c0-1.657 1.343-3 3-3m0 0c1.657 0 3 1.343 3 3m-3-3v2m0 4h.01" />
                            </svg>
                        </span>
                        <x-text-input id="password"
                                      class="pl-10 block w-full rounded-lg"
                                      type="password"
                                      name="password"
                                      required />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="remember"
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-600">Se souvenir de moi</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-indigo-600 hover:text-indigo-800 font-medium"
                           href="{{ route('password.request') }}">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <!-- Button -->
                <x-primary-button class="w-full justify-center py-3 text-base rounded-xl shadow-lg hover:scale-[1.02] transition">
                    {{ __('Se connecter') }}
                </x-primary-button>
            </form>

            <!-- Footer -->
            <div class="text-center text-xs text-gray-400">
                © {{ date('Y') }} {{ config('app.name') }} — Tous droits réservés
            </div>

        </div>
    </div>
</x-guest-layout>
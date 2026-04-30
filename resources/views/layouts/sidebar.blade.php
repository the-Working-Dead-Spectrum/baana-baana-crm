<!-- Sidebar -->
<div class="flex min-h-0 flex-1 flex-col border-r border-gray-200 bg-white">
    <!-- Logo -->
    <div class="mx-auto">
        @php
            $user = auth()->user();

            if ($user->role === 'admin') {
                $dashboardRoute = 'admin.dashboard';
            } elseif ($user->role === 'creator') {
                $dashboardRoute = 'creator.dashboard';
            } elseif ($user->role === 'logistic') {
                $dashboardRoute = 'logistic.dashboard';
            } else {
                $dashboardRoute = 'dashboard';
            }
        @endphp
        <a href="{{ route($dashboardRoute) }}">
            <x-application-logo class="block h-12-custom w-auto fill-current text-red-800" />
        </a>
    </div>

    <!-- Navigation -->
    <div class="flex flex-1 flex-col overflow-y-auto pt-5 pb-4">
        <nav class="mt-5 flex-1 space-y-1 px-2">
            <!-- Dashboard -->
            <div>
                <x-nav-link :href="route($dashboardRoute)" :active="request()->routeIs($dashboardRoute)">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ __('Dashboard') }}
                </x-nav-link>
            </div>
            <!-- Menu Administrateur -->
            @if (auth()->user()->role === 'admin')
                <div>
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">
                        Administration
                    </p>

                    <!-- Créateurs -->
                    <x-sidebar-link :href="route('admin.creators')" :active="request()->routeIs('admin.creators*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0H21m-4.5 0H15m4.5 0h.008v.008h-.008V15zm0 0h.008v.008h-.008V15z" />
                        </svg>
                        Créateurs
                    </x-sidebar-link>

                    <!-- Produits -->
                    <x-sidebar-link :href="route('admin.products')" :active="request()->routeIs('admin.products*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Produits & Ventes
                    </x-sidebar-link>


                    <!-- Commandes -->
                    <x-sidebar-link :href="route('admin.orders')" :active="request()->routeIs('admin.orders*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Commandes
                    </x-sidebar-link>

                    <!-- CLients -->
                    <x-sidebar-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0H21m-4.5 0H15m4.5 0h.008v.008h-.008V15zm0 0h.008v.008h-.008V15z" />
                        </svg>
                        Clients
                    </x-sidebar-link>

                    {{-- <x-sidebar-link :href="route('segments.index')" :active="request()->routeIs('admin.segments*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0H21m-4.5 0H15m4.5 0h.008v.008h-.008V15zm0 0h.008v.008h-.008V15z" />
                        </svg>
                        Segments
                    </x-sidebar-link> --}}

                    <!-- Synchronisation -->
                    <x-sidebar-link :href="route('admin.sync.index')" :active="request()->routeIs('admin.sync*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Synchronisation
                    </x-sidebar-link>

                    <!-- Paramètres -->
                    <x-sidebar-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Paramètres
                    </x-sidebar-link>

                    @if (auth()->user()->role === 'admin')
                        <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users*')">
                            <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0H21m-4.5 0H15m4.5 0h.008v.008h-.008V15zm0 0h.008v.008h-.008V15z" />
                            </svg>
                            Utilisateurs
                        </x-sidebar-link>

                        <!-- Rôles -->
                        <x-sidebar-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles*')">
                            <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Rôles & Permissions
                        </x-sidebar-link>
                    @endif
                </div>
            @endif

            <!-- Menu Créateur -->
            @if (auth()->user()->role === 'creator')
                <div>
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">
                        Mon Espace
                    </p>

                    <!-- Mes Commandes -->
                    <x-sidebar-link :href="route('creator.orders')" :active="request()->routeIs('creator.orders*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Mes Commandes
                    </x-sidebar-link>

                    <!-- Mes Produits -->
                    <x-sidebar-link :href="route('creator.products')" :active="request()->routeIs('creator.products*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Mes Produits
                    </x-sidebar-link>

                    <!-- Mes Statistiques
                    <x-sidebar-link :href="route('creator.stats')" :active="request()->routeIs('creator.stats*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Statistiques
                    </x-sidebar-link> -->
                </div>
            @endif

            <!-- Menu Utilisateur -->
            @if (auth()->user()->role === 'user')
                <div>
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">
                        Mon Compte
                    </p>

                    <!-- Mes Achats -->
                    <x-sidebar-link :href="route('user.orders')" :active="request()->routeIs('user.orders*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Mes Achats
                    </x-sidebar-link>

                    <!-- Mes Favoris -->
                    <x-sidebar-link :href="route('user.favorites')" :active="request()->routeIs('user.favorites*')">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Favoris
                    </x-sidebar-link>
                </div>
            @endif

            <!-- Menu Général -->
            <div>
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">
                    Général
                </p>

                <!-- Profile -->
                <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Mon Profil
                </x-sidebar-link>

                <!-- Documentation 
                <x-sidebar-link href="#" :active="false">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Documentation
                </x-sidebar-link>
                 Support 
                <x-sidebar-link href="#" :active="false">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Support
                </x-sidebar-link>-->
            </div>
        </nav>
    </div>

    <!-- User info en bas -->
    <div class="flex flex-shrink-0 border-t border-gray-200 p-4">
        <div class="flex items-center">
            <div>
                <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-gray-500">
                    @if (auth()->user()->role === 'admin')
                        Administrateur
                    @elseif(auth()->user()->role === 'creator')
                        Créateur
                    @else
                        Utilisateur
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

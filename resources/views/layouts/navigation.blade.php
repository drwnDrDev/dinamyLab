<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 row-span-1 h-full">
    <!-- Primary Navigation Menu -->
 
        <div class="flex flex-col p-3 justify-between">
                <!-- Navigation Links -->
                <div class="flex flex-col">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        
                        {{ __('Dashboard') }}
                    </x-nav-link>



                    <x-nav-link :href="route('personas')" :active="request()->routeIs('personas')">
                        {{ __('Personas') }}
                    </x-nav-link>

                    <x-nav-link :href="route('personas')" :active="request()->routeIs('personas')">
                        {{ __('Ordenes Médicas') }}
                    </x-nav-link>
                    <x-nav-link :href="route('personas')" :active="request()->routeIs('personas')">
                        {{ __('Procedimientos') }}
                    </x-nav-link>
                    <x-nav-link :href="route('personas')" :active="request()->routeIs('personas')">
                        {{ __('Resultados') }}
                    </x-nav-link>
                    <x-nav-link :href="route('personas')" :active="request()->routeIs('personas')">
                        {{ __('Caja') }}
                    </x-nav-link>
                    <x-nav-link :href="route('personas')" :active="request()->routeIs('personas')">
                        {{ __('Facturación') }}
                    </x-nav-link>
                    <x-nav-link :href="route('personas')" :active="request()->routeIs('personas')">
                        {{ __('Reportes') }}
                    </x-nav-link>
                    <x-nav-link :href="route('personas')" :active="request()->routeIs('personas')">
                        {{ __('Aministración') }}
                    </x-nav-link>

                </div>
       </div>
 

</nav>
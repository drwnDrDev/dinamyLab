<nav x-data="{ open: false }" class="fixed top-16 left-0 w-60 row-span-1 h-full hidden md:block border-r border-borders">


        <div class="flex flex-col p-3 justify-between">

        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-nav-link>

        <x-nav-link :href="route('personas')" :active="request()->routeIs('personas*')">
            {{ __('People') }}
        </x-nav-link>

        <x-nav-link :href="route('ordenes')" :active="request()->routeIs('ordenes*')">
            {{ __('Medical order') }}
        </x-nav-link>

        <x-nav-link :href="route('procedimientos')" :active="request()->routeIs('procedimientos*')">
            {{ __('Procedures') }}
        </x-nav-link>

        <x-nav-link :href="route('resultados')" :active="request()->routeIs('resultados*')">
            {{ __('Results') }}
        </x-nav-link>

        <x-nav-link :href="route('caja')" :active="request()->routeIs('caja*')">
            {{ __('Cash register') }}
        </x-nav-link>

        @can('ver_facturas')
            <x-nav-link :href="route('facturas')" :active="request()->routeIs('facturas*')">
                {{ __('Invoices') }}
            </x-nav-link>

        @endcan


        <x-nav-link :href="route('reportes')" :active="request()->routeIs('reportes*')">
            {{ __('Reportes') }}
        </x-nav-link>

        @can('ver_empresa')
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('administracion*')">
                {{ __('Adminstration') }}
            </x-nav-link>
         @endcan


       </div>


</nav>

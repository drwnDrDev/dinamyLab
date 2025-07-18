
    <nav x-data="{ open: false }" class="block fixed top-16 left-0 w-60 row-span-1 h-full md:border-r border-borders print:hidden">
            <!-- Hamburger -->
            <div class="-me-2 flex items-center md:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-titles hover:text-titles hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

    <div :class="{'hidden md:flex flex-col p-3 justify-between': ! open, 'block bg-cyan-50':  open }">

        <x-nav-link :href="route('inicio')" icono="panel" :active="request()->routeIs('inicio')">
            {{ __('Dashboard') }}
        </x-nav-link>

        <x-nav-link :href="route('personas')" icono="personas" :active="request()->routeIs('personas*')">
            {{ __('People') }}
        </x-nav-link>

        <x-nav-link :href="route('ordenes')" icono="ordenes" :active="request()->routeIs('ordenes*')">
            {{ __('Medical order') }}
        </x-nav-link>

        <x-nav-link :href="route('procedimientos')" icono="procedimientos" :active="request()->routeIs('procedimientos*')">
            {{ __('Procedures') }}
        </x-nav-link>

        <x-nav-link :href="route('resultados')" icono="resultados" :active="request()->routeIs('resultados*')">
            {{ __('Results') }}
        </x-nav-link>

        <x-nav-link :href="route('caja')" icono="caja" :active="request()->routeIs('caja*')">
            {{ __('Cash register') }}
        </x-nav-link>

        @can('ver_facturas')
            <x-nav-link :href="route('facturas')" icono="facturas" :active="request()->routeIs('facturas*')">
                {{ __('Invoices') }}
            </x-nav-link>

        @endcan


        <x-nav-link :href="route('reportes')" icono="reportes" :active="request()->routeIs('reportes*')">
            {{ __('Reportes') }}
        </x-nav-link>

        @can('ver_empresa')
            <x-nav-link :href="route('dashboard')" icono="admin" :active="request()->routeIs('administracion*')">
                {{ __('Adminstration') }}
            </x-nav-link>
         @endcan


    </div>


</nav>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 fixed top-16 left-0 w-60 row-span-1 h-full shadow-lg">

 
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

        <x-nav-link href="#" :active="request()->routeIs('procedimientos*')">
            {{ __('Procedures') }}
        </x-nav-link>

        <x-nav-link :href="route('personas')" :active="request()->routeIs('resultados*')">
            {{ __('Results') }}
        </x-nav-link>

        <x-nav-link :href="route('personas')" :active="request()->routeIs('caja*')">
            {{ __('Cash register') }}
        </x-nav-link>

        <x-nav-link :href="route('facturas')" :active="request()->routeIs('facturas*')">
            {{ __('Billing') }}
        </x-nav-link>

        <x-nav-link :href="route('personas')" :active="request()->routeIs('reportes*')">      
            {{ __('Reportes') }}
        </x-nav-link>
   
        @can('administracion')
            <x-nav-link :href="route('personas')" :active="request()->routeIs('administracion*')">    
                {{ __('Adminstration') }}
            </x-nav-link>

         @endcan

                
       </div>
 

</nav>
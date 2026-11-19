<nav id="sideNav" class="block fixed top-16 left-0 w-14 row-span-1 h-full bg-background border-r border-t border-borders transition-[width] duration-300 ease-in-out z-10 print:hidden">
    <!-- Hamburger -->
    <div class="p-2 mb-4 gap-24 flex items-center justify-end">
        <h3 class="font-bold text-primary text-xl">LissApp</h3>
        <button id="toggleNav" class="inline-flex items-center justify-center p-2 rounded-md text-text hover:text-titles focus:outline-none transition duration-150 ease-in-out">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path id="hamburgerIcon" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path id="closeIcon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex flex-col gap-1 p-2 bg-background overflow-hidden">

        {{-- Acceso rápido a nueva orden, disponible en todas las vistas --}}
        @can('crear_orden')
            <a href="{{ route('ordenes.create') }}" title="Nueva orden" aria-label="Nueva orden"
                @class([
                    'group inline-flex items-center rounded-2xl p-2 text-md font-semibold leading-5 text-nowrap transition duration-150 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-primary',
                    'text-primary-strong dark:text-cyan-300',
                    'bg-secondary' => request()->routeIs('ordenes.create'),
                    'hover:bg-secondary' => ! request()->routeIs('ordenes.create'),
                ])>
                <span class="mr-4 inline-flex h-6 w-6 flex-none items-center justify-center rounded-lg bg-primary-strong text-white shadow-sm transition group-hover:bg-cyan-800 dark:bg-cyan-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.75" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
                </span>
                Nueva orden
            </a>
            <div class="mx-1 my-1 border-t border-borders dark:border-slate-700" aria-hidden="true"></div>
        @endcan

        <x-nav-link :href="route('inicio')" icono="panel" :active="request()->routeIs('inicio')">
            {{ 'Panel' }}
        </x-nav-link>

        {{-- El rol agente (sin otros roles) solo navega por órdenes y resultados. Solo afecta el menú, no los permisos. --}}
        @php
        $soloAgente = auth()->user()->hasExactRoles('agente');
    @endphp

        @if (auth()->user()->can('ver_persona') && ! $soloAgente)
            <x-nav-link :href="route('personas')" icono="personas" :active="request()->routeIs('personas*')">
                {{ 'Personas' }}
            </x-nav-link>
        @endif

        @can('ver_orden')
            <x-nav-link :href="route('ordenes')" icono="ordenes" :active="request()->routeIs('ordenes*')">
                {{ 'Orden médica' }}
            </x-nav-link>
        @endcan

        @if (auth()->user()->can('ver_procedimiento') && ! $soloAgente)
            <x-nav-link :href="route('procedimientos')" icono="procedimientos" :active="request()->routeIs('procedimientos*')">
                {{ 'Procedimientos' }}
            </x-nav-link>
        @endif

        @can('ver_resultado')
            <x-nav-link :href="route('resultados')" icono="resultados" :active="request()->routeIs('resultados*')">
                {{ 'Resultados' }}
            </x-nav-link>
        @endcan

        @role('admin')
            <x-nav-link :href="route('actividad.index')" icono="reportes" :active="request()->routeIs('actividad.*')">
                {{ 'Monitor de eventos' }}
            </x-nav-link>
        @endrole

        @role('admin')
            <x-nav-link :href="route('caja')" icono="caja" :active="request()->routeIs('caja*')">
                {{ 'Caja' }}
            </x-nav-link>
        @endrole

        @can('ver_facturas')
        <x-nav-link :href="route('facturas')" icono="facturas" :active="request()->routeIs('facturas*')">
            {{ 'Facturas' }}
        </x-nav-link>

        @endcan


        @if((auth()->user()->hasRole('admin') || auth()->user()->can('ver_reportes')) && ! $soloAgente)
            <x-nav-link :href="route('reportes')" icono="reportes" :active="request()->routeIs('reportes*')">
                {{ 'Reportes' }}
            </x-nav-link>
        @endif

        
        @if(auth()->user()?->hasRole('admin'))
        <x-nav-link :href="route('empresa.show')" icono="admin" :active="request()->routeIs('empresa.show*')">
            {{ 'Administración' }}
        </x-nav-link>
        @endif


    </div>
</nav>

<script>
    const sideNav = document.getElementById('sideNav');
    const toggleNav = document.getElementById('toggleNav');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');
    let isOpen = false;

    function toggleNavigation() {
        isOpen = !isOpen;
        
        if (isOpen) {
            sideNav.classList.remove('w-14');
            sideNav.classList.add('w-60', 'shadow-2xl');
            hamburgerIcon.classList.add('hidden');
            hamburgerIcon.classList.remove('inline-flex');
            closeIcon.classList.add('inline-flex');
            closeIcon.classList.remove('hidden');
        } else {
            sideNav.classList.add('w-14');
            sideNav.classList.remove('w-60', 'shadow-2xl');
            hamburgerIcon.classList.remove('hidden');
            hamburgerIcon.classList.add('inline-flex');
            closeIcon.classList.remove('inline-flex');
            closeIcon.classList.add('hidden');
        }
    }

    // Click en el botón de toggle
    if (toggleNav) {
        toggleNav.addEventListener('click', (e) => {
            e.preventDefault();
            toggleNavigation();
        });
    }

    // Click fuera del nav para cerrarlo
    document.addEventListener('click', (e) => {
        if (isOpen && !sideNav.contains(e.target) && !toggleNav.contains(e.target)) {
            toggleNavigation();
        }
    });
</script>
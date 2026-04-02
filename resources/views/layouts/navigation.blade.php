<nav id="sideNav" class="block fixed top-16 left-0 w-14 h-[calc(100vh-64px)] bg-white dark:bg-slate-900 border-r border-borders dark:border-slate-800 shadow-[2px_0_16px_-4px_rgba(14,180,209,0.10)] dark:shadow-[2px_0_16px_-4px_rgba(0,0,0,0.4)] transition-[width] duration-300 ease-in-out z-10 print:hidden overflow-hidden">

    <!-- Header del sidebar -->
    <div class="flex items-center justify-between px-2 py-3 border-b border-borders/60 dark:border-slate-800 mb-1">
        <h3 class="font-bold text-primary dark:text-cyan-400 text-base tracking-tight whitespace-nowrap overflow-hidden opacity-0 w-0 transition-all duration-300 ease-in-out" id="sideNavTitle">DinamyLAB</h3>
        <button id="toggleNav" class="ml-auto flex items-center justify-center w-8 h-8 rounded-lg text-muted dark:text-slate-500 hover:bg-secondary dark:hover:bg-slate-800 hover:text-primary dark:hover:text-cyan-400 transition-colors shrink-0">
            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path id="hamburgerIcon" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path id="closeIcon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex flex-col gap-0.5 px-1.5 py-1 overflow-hidden">

        <x-nav-link :href="route('inicio')" icono="panel" :active="request()->routeIs('inicio')">
            Inicio
        </x-nav-link>

        <x-nav-link :href="route('personas')" icono="personas" :active="request()->routeIs('personas*')">
            Pacientes
        </x-nav-link>

        <x-nav-link :href="route('ordenes')" icono="ordenes" :active="request()->routeIs('ordenes*')">
            Órdenes
        </x-nav-link>

        <x-nav-link :href="route('procedimientos')" icono="procedimientos" :active="request()->routeIs('procedimientos*')">
            Procedimientos
        </x-nav-link>

        <x-nav-link :href="route('resultados')" icono="resultados" :active="request()->routeIs('resultados*')">
            Resultados
        </x-nav-link>

        <x-nav-link :href="route('caja')" icono="caja" :active="request()->routeIs('caja*')">
            Caja
        </x-nav-link>

        @can('ver_facturas')
        <x-nav-link :href="route('facturas')" icono="facturas" :active="request()->routeIs('facturas*')">
            Facturas
        </x-nav-link>
        @endcan

        <x-nav-link :href="route('reportes')" icono="reportes" :active="request()->routeIs('reportes*')">
            Reportes
        </x-nav-link>

        @if(auth()->user()?->hasRole('admin'))
        <div class="my-1 border-t border-borders/60 dark:border-slate-800"></div>
        <x-nav-link :href="route('empresa.show')" icono="admin" :active="request()->routeIs('empresa.show*')">
            Administración
        </x-nav-link>
        @endif

    </div>
</nav>

<script>
    const sideNav = document.getElementById('sideNav');
    const toggleNav = document.getElementById('toggleNav');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');
    const sideNavTitle = document.getElementById('sideNavTitle');
    let isOpen = false;

    function toggleNavigation() {
        isOpen = !isOpen;

        if (isOpen) {
            sideNav.classList.remove('w-14');
            sideNav.classList.add('w-56', 'shadow-xl');
            hamburgerIcon.classList.add('hidden');
            hamburgerIcon.classList.remove('inline-flex');
            closeIcon.classList.add('inline-flex');
            closeIcon.classList.remove('hidden');
            sideNavTitle.classList.remove('opacity-0', 'w-0');
            sideNavTitle.classList.add('opacity-100', 'w-auto');
        } else {
            sideNav.classList.add('w-14');
            sideNav.classList.remove('w-56', 'shadow-xl');
            hamburgerIcon.classList.remove('hidden');
            hamburgerIcon.classList.add('inline-flex');
            closeIcon.classList.remove('inline-flex');
            closeIcon.classList.add('hidden');
            sideNavTitle.classList.add('opacity-0', 'w-0');
            sideNavTitle.classList.remove('opacity-100', 'w-auto');
        }
    }

    if (toggleNav) {
        toggleNav.addEventListener('click', (e) => {
            e.preventDefault();
            toggleNavigation();
        });
    }

    document.addEventListener('click', (e) => {
        if (isOpen && !sideNav.contains(e.target) && !toggleNav.contains(e.target)) {
            toggleNavigation();
        }
    });
</script>

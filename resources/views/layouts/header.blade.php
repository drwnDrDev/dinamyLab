<header x-data="{ open: false }" class="fixed top-0 left-0 z-20 w-full h-16 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-b border-borders/60 dark:border-slate-800 print:hidden">
    <div class="h-full max-w-full mx-auto px-3 sm:px-5 flex items-center justify-between gap-4">

        <!-- Logo + Nombre sede -->
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                @if (session('sede'))
                    <figure class="w-8 h-8 rounded-lg overflow-hidden bg-secondary dark:bg-slate-800 flex items-center justify-center">
                        <img class="h-8 w-auto object-contain" src="{{ asset('storage/logos/'.session('sede')->logo) }}" alt="{{ session('sede')->nombre }}">
                    </figure>
                @else
                    <div class="w-8 h-8 rounded-lg bg-primary/10 dark:bg-primary/20 flex items-center justify-center">
                        <x-application-logo class="h-5 w-auto fill-current text-primary" />
                    </div>
                @endif
                <span class="text-base font-700 font-bold tracking-tight text-text dark:text-white hidden sm:block">
                    {{ session('sede')?->nombre ?? 'DinamyLAB' }}
                </span>
            </a>
        </div>

        <!-- Buscador -->
        <div class="flex-1 max-w-sm mx-auto">
            <form action="{{ route('search') }}" method="post">
                @csrf
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-muted dark:text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"/>
                        </svg>
                    </div>
                    <input
                        name="search"
                        type="text"
                        placeholder="Buscar paciente, orden..."
                        class="w-full h-9 pl-9 pr-4 text-sm bg-background dark:bg-slate-800 border border-borders dark:border-slate-700 text-text dark:text-slate-100 placeholder:text-muted dark:placeholder:text-slate-500 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 dark:focus:ring-cyan-500/30 focus:border-primary dark:focus:border-cyan-500 transition-colors"
                    />
                </div>
            </form>
        </div>

        <!-- Acciones lado derecho -->
        <div class="flex items-center gap-2 shrink-0">

            <!-- Toggle dark/light -->
            <button id="themeToggle" aria-label="Alternar modo oscuro"
                class="w-9 h-9 flex items-center justify-center rounded-xl text-muted dark:text-slate-400 hover:bg-secondary dark:hover:bg-slate-800 hover:text-primary dark:hover:text-cyan-400 transition-colors">
                <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                </svg>
            </button>

            <!-- User dropdown -->
            <div id="dropdownComponent"
                class="relative z-10"
                data-nombre="{{ Auth::user()->name }}"
                data-profile-url="{{ route('profile.edit') }}"
                data-sedes="{{ json_encode(Auth::user()->empleado->sedes->map(function($sede) { return ['id' => $sede->id, 'nombre' => $sede->nombre, 'logo' => $sede->logo, 'url' => route('elegir.sede', $sede->id)]; })->values()->all()) }}">
            </div>
        </div>

    </div>
</header>

<script>
    document.getElementById('themeToggle').addEventListener('click', function() {
        var isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
</script>

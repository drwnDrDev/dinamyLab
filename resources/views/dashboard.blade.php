<x-app-layout>

    {{-- ================================================================
         BANNER — suavizado con gradiente diagonal y decorativos tenues
    ================================================================ --}}
    <div class="relative w-full bg-gradient-to-br from-teal-700 to-primary px-8 py-10 pb-8 overflow-hidden">
        <div class="absolute -top-16 -right-16 w-80 h-80 bg-white/[0.05] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/3 w-60 h-60 bg-white/[0.04] rounded-full translate-y-1/2 pointer-events-none"></div>
        <div class="absolute top-6 left-1/5 w-32 h-32 bg-white/[0.03] rounded-full pointer-events-none"></div>

        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <p class="text-white/50 text-xs font-medium tracking-widest uppercase">Bienvenido de nuevo</p>
                <h1 class="text-white text-4xl font-bold font-ubuntu mt-1">Hola, {{ $empleado->user->name }}</h1>
                <p class="text-white/65 text-base mt-1">{{ $empleado->cargo }}</p>
            </div>
            <a href="{{ route('ordenes.create') }}"
               class="flex items-center gap-2 bg-white/90 text-teal-700 font-semibold px-5 py-3 rounded-xl shadow-sm hover:bg-white hover:shadow-md transition-all duration-200 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                     stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva Orden
            </a>
        </div>
    </div>

    {{-- ================================================================
         CONTENIDO PRINCIPAL
    ================================================================ --}}
    <x-canva class="bg-transparent border-none shadow-none mt-4">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Procedimientos --}}
        <div class="bg-white/80 dark:bg-slate-800/70 rounded-2xl shadow-sm shadow-primary/5 border border-borders/40 dark:border-slate-700/50 p-5">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-primary/10 dark:bg-primary/20 rounded-xl">
                    <x-iconos.procedimientos :active="false" class="text-primary w-6 h-6" />
                </div>
                <span class="text-4xl font-bold font-ubuntu text-primary leading-none">{{ $procedimientos->count() }}</span>
            </div>
            <div class="mt-4">
                <p class="text-text dark:text-white font-semibold text-sm">Procedimientos</p>
                <p class="text-titles text-xs mt-0.5">Total registrados</p>
            </div>
        </div>

        {{-- Pacientes --}}
        <div class="bg-white/80 dark:bg-slate-800/70 rounded-2xl shadow-sm shadow-teal-500/5 border border-borders/40 dark:border-slate-700/50 p-5">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-teal-500/10 dark:bg-teal-500/20 rounded-xl">
                    <x-iconos.personas :active="false" class="text-teal-600 dark:text-teal-400 w-6 h-6" />
                </div>
                <span class="text-4xl font-bold font-ubuntu text-teal-600 dark:text-teal-400 leading-none">{{ $pacientesHoy }}</span>
            </div>
            <div class="mt-4">
                <p class="text-text dark:text-white font-semibold text-sm">Pacientes</p>
                <p class="text-titles text-xs mt-0.5">Atendidos hoy</p>
            </div>
        </div>

        {{-- En Proceso --}}
        <div class="bg-white/80 dark:bg-slate-800/70 rounded-2xl shadow-sm shadow-rose-400/5 border border-borders/40 dark:border-slate-700/50 p-5">
            <div class="flex items-start justify-between">
                <div class="p-2.5 bg-rose-400/10 dark:bg-rose-400/20 rounded-xl">
                    <x-iconos.resultados :active="false" class="text-rose-400 w-6 h-6" />
                </div>
                <span class="text-4xl font-bold font-ubuntu text-rose-400 leading-none">{{ $procedimientosPendientes->count() }}</span>
            </div>
            <div class="mt-4">
                <p class="text-text dark:text-white font-semibold text-sm">En Proceso</p>
                <p class="text-titles text-xs mt-0.5">Pendientes de resultado</p>
            </div>
        </div>

    </div>

    {{-- DATA --}}
    @php
        $totalProcedimientos = $procedimientos->count();

        $barColors = [
            ['bar' => 'from-primary to-cyan-500',      'dot' => 'bg-primary'],
            ['bar' => 'from-teal-500 to-teal-600',     'dot' => 'bg-teal-500'],
            ['bar' => 'from-violet-400 to-violet-600', 'dot' => 'bg-violet-400'],
            ['bar' => 'from-amber-400 to-amber-500',   'dot' => 'bg-amber-400'],
            ['bar' => 'from-rose-400 to-pink-500',     'dot' => 'bg-rose-400'],
        ];

        $pendientesByExamen = $procedimientosPendientes
            ->groupBy('examen_id')
            ->map(fn($g) => ['examen' => $g->first()->examen->nombre, 'count' => $g->count()])
            ->sortByDesc('count')
            ->values();
        $totalPendientes = $procedimientosPendientes->count();
        $maxPendiente    = $pendientesByExamen->max('count') ?: 1;

        $strokeColors = ['#0eb4d1', '#14b8a6', '#a78bfa', '#fbbf24', '#fb7185'];
        $top5         = $procedimientosByExamen->take(5);
        $r            = 45;
        $circ         = 2 * M_PI * $r;
        $gap          = 3;
        $cumArc       = 0;
        $donutSegs    = [];
        foreach ($top5 as $i => $ex) {
            $pct         = $totalProcedimientos > 0 ? $ex['count'] / $totalProcedimientos : 0;
            $arcLen      = max(0, $pct * $circ - $gap);
            $donutSegs[] = [
                'arcLen'     => $arcLen,
                'dashoffset' => $circ - $cumArc,
                'color'      => $strokeColors[$i],
                'dot'        => $barColors[$i]['dot'],
                'examen'     => $ex['examen'],
                'count'      => $ex['count'],
                'pct'        => round($pct * 100),
            ];
            $cumArc += $pct * $circ;
        }
    @endphp

    {{-- GRID PRINCIPAL --}}
    <div class="mt-6 pb-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

            {{-- ========================
                 EN PROCESO — barras (2/3)
            ======================== --}}
            <div class="h-full lg:col-span-2 bg-white/80 dark:bg-slate-800/70 rounded-2xl shadow-sm border border-rose-100/50 dark:border-rose-900/20 overflow-hidden">

                <div class="px-6 py-4 border-b border-rose-100/50 dark:border-rose-900/20 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-0.5 h-7 bg-gradient-to-b from-rose-300 to-rose-400 rounded-full shrink-0"></div>
                        <div>
                            <h2 class="text-text dark:text-white font-semibold font-ubuntu text-lg">En Proceso</h2>
                            <p class="text-rose-400/70 text-xs mt-0.5">Pendientes por tipo de examen</p>
                        </div>
                    </div>
                    <span class="text-3xl font-bold font-ubuntu text-rose-400/90">{{ $totalPendientes }}</span>
                </div>

                @if($pendientesByExamen->isEmpty())
                    <p class="px-6 py-8 text-center text-titles/50 text-sm">Sin procedimientos en proceso.</p>
                @else
                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-4">
                    @foreach($pendientesByExamen as $item)
                    @php $barPct = round($item['count'] / $maxPendiente * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm text-text dark:text-white truncate max-w-[75%]">{{ $item['examen'] }}</span>
                            <span class="text-sm font-semibold text-rose-400">{{ $item['count'] }}</span>
                        </div>
                        <div class="h-2 w-full bg-rose-50 dark:bg-rose-950/20 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-rose-300 to-rose-400 rounded-full"
                                 style="width: {{ $barPct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

            </div>

            {{-- ========================
                 DISTRIBUCIÓN — donut (1/3)
            ======================== --}}
            <div class="bg-white/80 dark:bg-slate-800/70 rounded-2xl shadow-sm border border-borders/40 dark:border-slate-700/50 overflow-hidden">

                <div class="px-6 py-4 border-b border-borders/40 dark:border-slate-700/50">
                    <h2 class="text-text dark:text-white font-semibold font-ubuntu text-lg">Distribución</h2>
                    <p class="text-titles text-xs mt-0.5">Top 5 por tipo de examen</p>
                </div>

                <div class="px-6 py-5">
                    <div class="relative w-40 h-40 mx-auto mb-5">
                        <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                            <circle cx="60" cy="60" r="{{ $r }}" fill="none"
                                    stroke="rgb(207,228,232)" stroke-width="14" />
                            @foreach($donutSegs as $seg)
                            <circle cx="60" cy="60" r="{{ $r }}" fill="none"
                                    stroke="{{ $seg['color'] }}"
                                    stroke-width="14"
                                    stroke-linecap="butt"
                                    stroke-dasharray="{{ number_format($seg['arcLen'], 3, '.', '') }} {{ number_format($circ, 3, '.', '') }}"
                                    stroke-dashoffset="{{ number_format($seg['dashoffset'], 3, '.', '') }}" />
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-2xl font-bold font-ubuntu text-text dark:text-white leading-none">{{ $totalProcedimientos }}</span>
                            <span class="text-xs text-titles mt-0.5">Total</span>
                        </div>
                    </div>

                    @forelse($donutSegs as $seg)
                    <div class="flex items-center justify-between py-1.5 border-b border-borders/30 dark:border-slate-700/30 last:border-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2 h-2 rounded-full shrink-0 {{ $seg['dot'] }}"></span>
                            <span class="text-xs text-text dark:text-white truncate">{{ $seg['examen'] }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 ml-2">
                            <span class="text-xs font-semibold text-text dark:text-white">{{ $seg['count'] }}</span>
                            <span class="text-xs text-titles/50">({{ $seg['pct'] }}%)</span>
                        </div>
                    </div>
                    @empty
                        <p class="text-sm text-titles/50 text-center py-4">Sin datos.</p>
                    @endforelse
                </div>

            </div>

            {{-- ========================
                 ÓRDENES RECIENTES — full width (3/3)
            ======================== --}}
            <div class="lg:col-span-3 bg-white/80 dark:bg-slate-800/70 rounded-2xl shadow-sm border border-borders/40 dark:border-slate-700/50 overflow-hidden">

                <div class="px-6 py-4 border-b border-borders/40 dark:border-slate-700/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-text dark:text-white font-semibold font-ubuntu text-lg">Órdenes Recientes</h2>
                        <p class="text-titles text-xs mt-0.5">Últimas órdenes en el sistema</p>
                    </div>
                    <a href="{{ route('ordenes') }}" class="text-xs text-primary hover:text-cyan-600 font-medium transition-colors">
                        Ver todas →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-secondary/30 dark:bg-slate-700/30 text-left">
                                <th class="px-6 py-3 text-titles dark:text-cyan-400 font-medium text-xs uppercase tracking-wider">N°</th>
                                <th class="px-6 py-3 text-titles dark:text-cyan-400 font-medium text-xs uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-titles dark:text-cyan-400 font-medium text-xs uppercase tracking-wider">Paciente</th>
                                <th class="px-6 py-3 text-titles dark:text-cyan-400 font-medium text-xs uppercase tracking-wider text-center">Exámenes</th>
                                <th class="px-6 py-3 text-titles dark:text-cyan-400 font-medium text-xs uppercase tracking-wider text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-borders/30 dark:divide-slate-700/30">
                            @forelse ($ordenes as $orden)
                            <tr class="hover:bg-secondary/20 dark:hover:bg-slate-700/20 transition-colors duration-150">
                                <td class="px-6 py-3 font-semibold text-text dark:text-white">#{{ $orden->numero }}</td>
                                <td class="px-6 py-3 text-titles dark:text-slate-400 tabular-nums">{{ $orden->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-text dark:text-white">{{ $orden->paciente->primer_nombre }} {{ $orden->paciente->primer_apellido }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-secondary/50 dark:bg-slate-700 text-titles dark:text-cyan-400 font-semibold text-xs">
                                        {{ $orden->procedimientos->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('ordenes.show', $orden->numero) }}">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors
                                            {{ $orden->terminada
                                                ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100'
                                                : 'bg-amber-50 text-amber-600 hover:bg-amber-100' }}">
                                            {{ $orden->terminada ? 'Terminada' : 'Pendiente' }}
                                        </span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-titles/50 text-sm">No hay órdenes registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>

    </x-canva>

    @vite('resources/js/obtenerStaticos.js')

</x-app-layout>

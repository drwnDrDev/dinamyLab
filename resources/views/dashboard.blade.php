<x-app-layout>

    {{-- ================================================================
         BANNER DE BIENVENIDA
    ================================================================ --}}
    <div class="relative w-full bg-gradient-to-r from-primary via-cyan-500 to-teal-600 px-8 py-10 pb-20 overflow-hidden">
        {{-- Círculos decorativos de fondo --}}
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-white/10 rounded-full pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 pointer-events-none"></div>
        <div class="absolute top-4 left-1/4 w-24 h-24 bg-white/5 rounded-full pointer-events-none"></div>

        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <p class="text-white/70 text-xs font-medium tracking-widest uppercase">Bienvenido de nuevo</p>
                <h1 class="text-white text-4xl font-bold font-ubuntu mt-1">Hola, {{ $empleado->user->name }}</h1>
                <p class="text-white/80 text-base mt-1">{{ $empleado->cargo }}</p>
            </div>
            <a href="{{ route('ordenes.create') }}"
               class="flex items-center gap-2 bg-white text-primary font-semibold px-5 py-3 rounded-xl shadow-lg hover:shadow-xl hover:bg-cyan-50 transition-all duration-200 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                     stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva Orden
            </a>
        </div>
    </div>

    {{-- ================================================================
         STAT CARDS FLOTANTES
    ================================================================ --}}
    <div class="max-w-6xl mx-auto px-8 -mt-10 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Total Procedimientos --}}
            <div class="bg-gradient-to-br from-primary to-cyan-600 rounded-2xl shadow-xl p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/70 text-xs font-medium uppercase tracking-wider">Procedimientos</p>
                        <p class="text-5xl font-bold font-ubuntu mt-1">{{ $procedimientos->count() }}</p>
                        <p class="text-white/60 text-xs mt-1">Total registrados</p>
                    </div>
                    <div class="bg-white/20 rounded-xl p-3 self-start">
                        <x-iconos.procedimientos :active="false" class="text-white w-7 h-7" />
                    </div>
                </div>
            </div>

            {{-- Pacientes Hoy --}}
            <div class="bg-gradient-to-br from-teal-500 to-teal-700 rounded-2xl shadow-xl p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/70 text-xs font-medium uppercase tracking-wider">Pacientes</p>
                        <p class="text-5xl font-bold font-ubuntu mt-1">{{ $pacientesHoy }}</p>
                        <p class="text-white/60 text-xs mt-1">Atendidos hoy</p>
                    </div>
                    <div class="bg-white/20 rounded-xl p-3 self-start">
                        <x-iconos.personas :active="false" class="text-white w-7 h-7" />
                    </div>
                </div>
            </div>

            {{-- En Proceso --}}
            <div class="bg-gradient-to-br from-rose-400 to-pink-600 rounded-2xl shadow-xl p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/70 text-xs font-medium uppercase tracking-wider">En Proceso</p>
                        <p class="text-5xl font-bold font-ubuntu mt-1">{{ $procedimientosPendientes->count() }}</p>
                        <p class="text-white/60 text-xs mt-1">Pendientes de resultado</p>
                    </div>
                    <div class="bg-white/20 rounded-xl p-3 self-start">
                        <x-iconos.resultados :active="false" class="text-white w-7 h-7" />
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ================================================================
         GRID PRINCIPAL ASIMÉTRICO
    ================================================================ --}}
    @php
        $totalProcedimientos = $procedimientos->count();

        $barColors = [
            ['bar' => 'from-primary to-cyan-500',       'dot' => 'bg-primary'],
            ['bar' => 'from-teal-500 to-teal-600',      'dot' => 'bg-teal-500'],
            ['bar' => 'from-violet-400 to-violet-600',  'dot' => 'bg-violet-400'],
            ['bar' => 'from-amber-400 to-amber-500',    'dot' => 'bg-amber-400'],
            ['bar' => 'from-rose-400 to-pink-500',      'dot' => 'bg-rose-400'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto px-8 mt-6 pb-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

            {{-- ========================
                 TABLA ÓRDENES (2/3)
            ======================== --}}
            <div class="lg:col-span-2 bg-background dark:bg-slate-800 rounded-2xl shadow-md border border-borders dark:border-slate-700 overflow-hidden">

                <div class="px-6 py-4 border-b border-borders dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-text dark:text-white font-semibold font-ubuntu text-lg">Órdenes Recientes</h2>
                        <p class="text-titles text-xs mt-0.5">Últimas órdenes en el sistema</p>
                    </div>
                    <a href="{{ route('ordenes') }}"
                       class="text-xs text-primary hover:text-cyan-600 font-medium transition-colors">
                        Ver todas →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-secondary/60 dark:bg-slate-700/50 text-left">
                                <th class="px-6 py-3 text-titles dark:text-cyan-400 font-medium text-xs uppercase tracking-wider">N°</th>
                                <th class="px-6 py-3 text-titles dark:text-cyan-400 font-medium text-xs uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-titles dark:text-cyan-400 font-medium text-xs uppercase tracking-wider">Paciente</th>
                                <th class="px-6 py-3 text-titles dark:text-cyan-400 font-medium text-xs uppercase tracking-wider text-center">Exámenes</th>
                                <th class="px-6 py-3 text-titles dark:text-cyan-400 font-medium text-xs uppercase tracking-wider text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-borders dark:divide-slate-700">
                            @forelse ($ordenes as $orden)
                            <tr class="hover:bg-secondary/50 dark:hover:bg-slate-700/40 transition-colors duration-150">
                                <td class="px-6 py-3 font-semibold text-text dark:text-white">
                                    #{{ $orden->numero }}
                                </td>
                                <td class="px-6 py-3 text-titles dark:text-slate-400 tabular-nums">
                                    {{ $orden->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-3 text-text dark:text-white">
                                    {{ $orden->paciente->primer_nombre }}
                                    {{ $orden->paciente->primer_apellido }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-secondary dark:bg-slate-700 text-titles dark:text-cyan-400 font-semibold text-xs">
                                        {{ $orden->procedimientos->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('ordenes.show', $orden->numero) }}">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-all
                                            {{ $orden->terminada
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 hover:bg-emerald-200'
                                                : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 hover:bg-amber-200' }}">
                                            {{ $orden->terminada ? 'Terminada' : 'Pendiente' }}
                                        </span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-titles/60 text-sm">
                                    No hay órdenes registradas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ========================
                 DISTRIBUCIÓN (1/3)
            ======================== --}}
            <div class="bg-background dark:bg-slate-800 rounded-2xl shadow-md border border-borders dark:border-slate-700 overflow-hidden">

                <div class="px-6 py-4 border-b border-borders dark:border-slate-700">
                    <h2 class="text-text dark:text-white font-semibold font-ubuntu text-lg">Distribución</h2>
                    <p class="text-titles text-xs mt-0.5">Por tipo de examen</p>
                </div>

                <div class="px-6 py-5 flex flex-col gap-5">
                    @forelse ($procedimientosByExamen as $index => $examen)
                        @php
                            $color  = $barColors[$index % count($barColors)];
                            $pct    = $totalProcedimientos > 0
                                        ? round($examen['count'] / $totalProcedimientos * 100)
                                        : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full shrink-0 {{ $color['dot'] }}"></span>
                                    <span class="text-sm text-text dark:text-white font-medium truncate">{{ $examen['examen'] }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0 ml-2">
                                    <span class="text-sm font-bold text-text dark:text-white">{{ $examen['count'] }}</span>
                                    <span class="text-xs text-titles/70 dark:text-slate-500">({{ $pct }}%)</span>
                                </div>
                            </div>
                            <div class="h-2 w-full bg-secondary dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r {{ $color['bar'] }} rounded-full"
                                     style="width: {{ $pct }}%">
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-titles/60 text-center py-4">Sin datos disponibles.</p>
                    @endforelse
                </div>

            </div>

        </div>
    </div>

    @vite('resources/js/obtenerStaticos.js')

</x-app-layout>

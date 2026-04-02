<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Inicio', 'href' => route('inicio')],
            ['label' => 'Órdenes'],
        ]" />
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-text dark:text-white tracking-tight">Órdenes Médicas</h1>
                <p class="text-sm text-muted dark:text-slate-400 mt-0.5">Gestión de órdenes de análisis</p>
            </div>
            <x-primary-button href="{{ route('ordenes.create') }}">
                <svg class="w-4 h-4 mr-1.5 -ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva Orden
            </x-primary-button>
        </div>
    </x-slot>

    <x-canva>
        <!-- Barra de búsqueda y filtros -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <h2 class="text-sm font-semibold text-text dark:text-white">Órdenes recientes</h2>
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-muted dark:text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"/>
                    </svg>
                </div>
                <input
                    placeholder="Buscar por paciente o número..."
                    class="w-full h-9 pl-9 pr-4 text-sm bg-background dark:bg-slate-800 border border-borders dark:border-slate-700 text-text dark:text-slate-100 placeholder:text-muted dark:placeholder:text-slate-500 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 dark:focus:ring-cyan-500/30 focus:border-primary dark:focus:border-cyan-500 transition-colors"
                />
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-hidden rounded-xl border border-borders/60 dark:border-slate-800">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-background dark:bg-slate-800/60 border-b border-borders/60 dark:border-slate-800">
                        <th class="px-4 py-3 text-left text-xs font-600 font-semibold text-muted dark:text-slate-400 uppercase tracking-wider w-36">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-600 font-semibold text-muted dark:text-slate-400 uppercase tracking-wider w-32">N° Orden</th>
                        <th class="px-4 py-3 text-left text-xs font-600 font-semibold text-muted dark:text-slate-400 uppercase tracking-wider">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-600 font-semibold text-muted dark:text-slate-400 uppercase tracking-wider w-36">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-borders/40 dark:divide-slate-800">
                    @foreach ($ordenes as $orden)
                    <tr data-url="{{ route('ordenes.show', $orden) }}"
                        onclick="window.location.href=this.dataset.url"
                        class="bg-white dark:bg-slate-900 hover:bg-primary/5 dark:hover:bg-primary/10 cursor-pointer transition-colors group">
                        <td class="px-4 py-3 text-muted dark:text-slate-400 font-normal">
                            {{ $orden->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-primary dark:text-cyan-400 group-hover:underline">
                                #{{ $orden->numero }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-text dark:text-slate-200">
                            {{ $orden->paciente->nombreCompleto() }}
                        </td>
                        <td class="px-4 py-3">
                            @if($orden->terminada)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Completada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    Pendiente
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $ordenes->links() }}
        </div>
    </x-canva>
</x-app-layout>

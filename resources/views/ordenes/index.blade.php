<x-app-layout>
    <x-index.page>
        <x-index.header title="Órdenes médicas"
            :subtitle="$ordenes->total() === 1 ? '1 orden' : $ordenes->total().' órdenes'">
            @can('crear_orden')
                <a href="{{ route('ordenes.create') }}" class="btn btn-primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
                    Nueva orden
                </a>
            @endcan
        </x-index.header>

        <x-index.card>
            <x-slot name="filters">
                <x-index.filter-bar :action="route('ordenes')">
                    <x-index.search label="Buscar órdenes" placeholder="Número de orden, documento o nombre del paciente" />
                    <x-index.select name="estado" label="Filtrar por estado">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" @selected(request('estado') === 'pendiente')>Pendientes</option>
                        <option value="completada" @selected(request('estado') === 'completada')>Completadas</option>
                    </x-index.select>
                </x-index.filter-bar>
            </x-slot>

            <x-index.table class="min-w-[560px]">
                <x-slot name="head">
                    <th scope="col" class="w-28">Orden</th>
                    <th scope="col">Paciente</th>
                    <th scope="col" class="w-48">Exámenes</th>
                    <th scope="col" class="w-32">Fecha</th>
                    <th scope="col" class="w-36">Estado</th>
                    <th scope="col" class="w-10"><span class="sr-only">Abrir</span></th>
                </x-slot>

                @forelse ($ordenes as $orden)
                    @php
                        $total = $orden->procedimientos_count;
                        $listos = $orden->procedimientos_listos_count;
                        $avance = $total > 0 ? round($listos / $total * 100) : 0;
                    @endphp
                    <tr data-url="{{ route('ordenes.show', $orden) }}" onclick="window.location.href=this.dataset.url">
                        <td>
                            <a href="{{ route('ordenes.show', $orden) }}" class="font-semibold tabular-nums text-text hover:text-primary-strong dark:text-slate-50">#{{ $orden->numero }}</a>
                        </td>
                        <td>
                            <span class="font-medium">{{ $orden->paciente->nombreCompleto() }}</span>
                            <span class="cell-sub">{{ $orden->paciente->tipo_documento?->cod_rips }} {{ $orden->paciente->numero_documento }}</span>
                        </td>
                        <td>
                            <span class="tabular-nums">{{ $total }} {{ $total === 1 ? 'examen' : 'exámenes' }}</span>
                            <span class="text-[13px] text-muted dark:text-slate-400">· {{ $listos }} {{ $listos === 1 ? 'listo' : 'listos' }}</span>
                            <span class="mt-1.5 block h-1 max-w-36 overflow-hidden rounded-full bg-secondary dark:bg-slate-800"
                                role="progressbar" aria-label="Exámenes listos" aria-valuemin="0" aria-valuemax="{{ $total }}" aria-valuenow="{{ $listos }}">
                                <span class="block h-1 rounded-full bg-primary-strong dark:bg-cyan-400" style="width: {{ $avance }}%"></span>
                            </span>
                        </td>
                        <td class="tabular-nums text-slate-600 dark:text-slate-300">{{ $orden->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if ($orden->terminada === null)
                                <x-status-badge tone="warning">Pendiente</x-status-badge>
                            @else
                                <x-status-badge tone="success">Completada</x-status-badge>
                            @endif
                        </td>
                        <td class="text-right"><x-index.chevron /></td>
                    </tr>
                @empty
                    <x-index.empty-row colspan="6">No se encontraron órdenes.</x-index.empty-row>
                @endforelse
            </x-index.table>

            <x-slot name="footer">
                <x-index.pagination :paginator="$ordenes" />
            </x-slot>
        </x-index.card>
    </x-index.page>
</x-app-layout>

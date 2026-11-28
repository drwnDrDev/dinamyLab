<x-app-layout>
    <x-index.page>
        <x-index.header title="Procedimientos"
            :subtitle="$procedimientos->total() === 1 ? '1 procedimiento en proceso' : $procedimientos->total().' procedimientos en proceso'" />

        <x-index.card>
            <x-slot name="filters">
                <x-index.filter-bar :action="route('procedimientos')">
                    <x-index.search label="Buscar procedimientos" placeholder="Orden, documento, paciente o examen" />
                </x-index.filter-bar>
            </x-slot>

            @php
                $abreCaptura = auth()->user()->hasRole('prestador') && auth()->user()->can('registrar_resultado');
            @endphp

            <x-index.table class="min-w-[560px]">
                <x-slot name="head">
                    <th scope="col">Examen</th>
                    <th scope="col">Paciente</th>
                    <th scope="col" class="w-24">Orden</th>
                    <th scope="col" class="w-32">Fecha</th>
                    <th scope="col" class="w-36">Estado</th>
                    <th scope="col" class="w-10"><span class="sr-only">Abrir</span></th>
                </x-slot>

                @forelse ($procedimientos as $procedimiento)
                    @php
                        $url = $abreCaptura
                            ? route('resultados.create', $procedimiento)
                            : route('procedimientos.show', $procedimiento);
                    @endphp
                    <tr data-url="{{ $url }}" onclick="window.location.href=this.dataset.url">
                        <td>
                            <a href="{{ $url }}" class="font-semibold text-text hover:text-primary-strong dark:text-slate-50">{{ $procedimiento->examen->nombre }}</a>
                        </td>
                        <td>
                            <span class="font-medium">{{ $procedimiento->orden->paciente->nombreCompleto() }}</span>
                            <span class="cell-sub">{{ $procedimiento->orden->paciente->numero_documento }}</span>
                        </td>
                        <td class="tabular-nums text-slate-600 dark:text-slate-300">#{{ $procedimiento->orden->numero }}</td>
                        <td class="tabular-nums text-slate-600 dark:text-slate-300">{{ $procedimiento->created_at->format('d/m/Y') }}</td>
                        <td>
                            <x-status-badge :estado="$procedimiento->estado" />
                            <x-solicitar-anulacion :procedimiento="$procedimiento" class="mt-2 text-xs" onclick="event.stopPropagation()" />
                        </td>
                        <td class="text-right"><x-index.chevron /></td>
                    </tr>
                @empty
                    <x-index.empty-row colspan="6">No se encontraron procedimientos en proceso.</x-index.empty-row>
                @endforelse
            </x-index.table>

            <x-slot name="footer">
                <x-index.pagination :paginator="$procedimientos" />
            </x-slot>
        </x-index.card>
    </x-index.page>
</x-app-layout>

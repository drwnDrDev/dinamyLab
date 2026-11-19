<x-app-layout>
    <x-index.page>
        <x-index.header title="Resultados"
            :subtitle="($resultados->total() === 1 ? '1 resultado terminado' : $resultados->total().' resultados terminados').' en las últimas 48 horas'" />

        <x-index.card>
            <x-slot name="filters">
                <x-index.filter-bar :action="route('resultados')">
                    <x-index.search label="Buscar resultados" placeholder="Orden, documento, paciente o examen" />
                </x-index.filter-bar>
            </x-slot>

            <x-index.table class="min-w-[560px]">
                <x-slot name="head">
                    <th scope="col">Examen</th>
                    <th scope="col">Paciente</th>
                    <th scope="col" class="w-24">Orden</th>
                    <th scope="col" class="w-36">Terminado</th>
                    <th scope="col" class="w-36">Estado</th>
                    <th scope="col" class="w-10"><span class="sr-only">Abrir</span></th>
                </x-slot>

                @forelse ($resultados as $procedimiento)
                    <tr data-url="{{ route('resultados.show', $procedimiento) }}" onclick="window.location.href=this.dataset.url">
                        <td>
                            <a href="{{ route('resultados.show', $procedimiento) }}" class="font-semibold text-text hover:text-primary-strong dark:text-slate-50">{{ $procedimiento->examen->nombre }}</a>
                        </td>
                        <td>
                            <span class="font-medium">{{ $procedimiento->orden->paciente->nombreCompleto() }}</span>
                            <span class="cell-sub">{{ $procedimiento->orden->paciente->numero_documento }}</span>
                        </td>
                        <td class="tabular-nums text-slate-600 dark:text-slate-300">#{{ $procedimiento->orden->numero }}</td>
                        <td>
                            <span class="tabular-nums text-slate-600 dark:text-slate-300">{{ $procedimiento->updated_at->format('d/m/Y') }}</span>
                            <span class="cell-sub">{{ $procedimiento->updated_at->format('h:i a') }}</span>
                        </td>
                        <td><x-status-badge :estado="$procedimiento->estado" /></td>
                        <td class="text-right"><x-index.chevron /></td>
                    </tr>
                @empty
                    <x-index.empty-row colspan="6">No se encontraron resultados recientes.</x-index.empty-row>
                @endforelse
            </x-index.table>

            <x-slot name="footer">
                <x-index.pagination :paginator="$resultados" />
            </x-slot>
        </x-index.card>
    </x-index.page>
</x-app-layout>

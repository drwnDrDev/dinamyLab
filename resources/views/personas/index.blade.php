<x-app-layout>
    <x-index.page>
        <x-index.header title="Pacientes"
            :subtitle="$personas->total() === 1 ? '1 paciente' : $personas->total().' pacientes'">
            @can('crear_persona')
                <a href="{{ route('personas.create') }}" class="btn btn-primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
                    Nuevo paciente
                </a>
            @endcan
        </x-index.header>

        <x-index.card>
            <x-slot name="filters">
                <x-index.filter-bar :action="route('personas')">
                    <x-index.search label="Buscar pacientes" placeholder="Documento, nombres o apellidos" />
                    <x-index.select name="sexo" label="Filtrar por sexo" width="flex-[0_1_160px]">
                        <option value="">Todos los sexos</option>
                        @foreach (\App\Models\Sexo::opciones() as $codigo => $nombre)
                            <option value="{{ $codigo }}" @selected(request('sexo') === $codigo)>{{ $nombre }}</option>
                        @endforeach
                    </x-index.select>
                    <x-index.select name="municipio_id" label="Filtrar por municipio">
                        <option value="">Todos los municipios</option>
                        @foreach ($municipios as $municipio)
                            <option value="{{ $municipio->id }}" @selected((string) request('municipio_id') === (string) $municipio->id)>
                                {{ $municipio->municipio }}
                            </option>
                        @endforeach
                    </x-index.select>
                    <x-index.select name="eps" label="Filtrar por EPS" width="flex-[0_1_160px]">
                        <option value="">Todas las EPS</option>
                        @foreach ($eps as $nombreEps)
                            <option value="{{ $nombreEps }}" @selected(request('eps') === $nombreEps)>{{ $nombreEps }}</option>
                        @endforeach
                    </x-index.select>
                </x-index.filter-bar>
            </x-slot>

            <x-index.table class="min-w-[560px]">
                <x-slot name="head">
                    <th scope="col">Paciente</th>
                    <th scope="col" class="w-44">Sexo · Edad</th>
                    <th scope="col" class="w-44">Órdenes</th>
                    <th scope="col" class="w-36"><span class="sr-only">Acciones</span></th>
                </x-slot>

                @forelse ($personas as $paciente)
                    @php
                        $sexo = $paciente->sexo_nombre;
                        $sexoEdad = collect([
                            $sexo !== 'No especificado' ? $sexo : null,
                            $paciente->edad(),
                        ])->filter()->implode(' · ');
                        $ultimaOrden = $paciente->ordenes_max_created_at
                            ? \Illuminate\Support\Carbon::parse($paciente->ordenes_max_created_at)
                            : null;
                    @endphp
                    <tr data-url="{{ route('personas.show', $paciente) }}" onclick="window.location.href=this.dataset.url">
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full bg-secondary text-xs font-bold text-cyan-900 dark:bg-slate-800 dark:text-cyan-200" aria-hidden="true">
                                    {{ $paciente->iniciales }}
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('personas.show', $paciente) }}" class="font-semibold text-text hover:text-primary-strong dark:text-slate-50">
                                        {{ $paciente->nombreCompleto() }}
                                    </a>
                                    <span class="cell-sub">{{ $paciente->tipo_documento?->cod_rips }} {{ $paciente->numero_documento }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-slate-600 dark:text-slate-300">{{ $sexoEdad !== '' ? $sexoEdad : '—' }}</td>
                        <td>
                            <span class="tabular-nums">{{ $paciente->ordenes_count }} {{ $paciente->ordenes_count === 1 ? 'orden' : 'órdenes' }}</span>
                            <span class="cell-sub">{{ $ultimaOrden ? 'Últ. '.$ultimaOrden->format('d/m/Y') : 'Sin órdenes' }}</span>
                        </td>
                        <td class="whitespace-nowrap text-right">
                            @can('crear_orden')
                                <a href="{{ route('ordenes.create', $paciente) }}" class="btn btn-sm btn-secondary" onclick="event.stopPropagation()">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
                                    Orden
                                </a>
                            @endcan
                            <x-index.chevron class="ml-2" />
                        </td>
                    </tr>
                @empty
                    <x-index.empty-row colspan="4">No se encontraron pacientes.</x-index.empty-row>
                @endforelse
            </x-index.table>

            <x-slot name="footer">
                <x-index.pagination :paginator="$personas" />
            </x-slot>
        </x-index.card>
    </x-index.page>
</x-app-layout>

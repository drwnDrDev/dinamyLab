<x-app-layout>
    @php
        $paciente = $orden->paciente;
        $finales = [\App\Estado::TERMINADO->value, \App\Estado::ENTREGADO->value];
        $procedimientosVisibles = $orden->procedimientos
            ->reject(fn ($procedimiento) => auth()->user()->hasRole('agente') && ! $procedimiento->tieneResultadosPublicados())
            ->values();
        $examenesListos = $procedimientosVisibles->filter(fn ($procedimiento) => in_array($procedimiento->estado, $finales, true))->count();
        $avanceExamenes = $procedimientosVisibles->count() > 0 ? round($examenesListos / $procedimientosVisibles->count() * 100) : 0;
        $remisionesFinalizadas = $orden->remisiones->filter(fn ($remision) => in_array($remision->estado, [\App\Estado::TERMINADO, \App\Estado::ENTREGADO], true))->count();
        $totalOrden = $orden->total - $orden->descuento;
        $saldo = $totalOrden - $orden->abono;
    @endphp

    <x-index.page class="print:hidden">
        {{-- Encabezado --}}
        <div class="flex flex-col gap-3">
            <a href="{{ route('ordenes') }}" class="inline-flex w-fit items-center gap-1 text-[13px] font-medium text-primary-strong hover:underline dark:text-cyan-300">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                Órdenes médicas
            </a>
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div class="flex flex-col gap-1.5">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-2xl font-bold leading-tight tracking-tight text-text dark:text-slate-50">Orden #{{ $orden->numero }}</h1>
                        @if ($orden->terminada === null)
                            <x-status-badge tone="warning">Pendiente</x-status-badge>
                        @else
                            <x-status-badge tone="success">Completada</x-status-badge>
                        @endif
                    </div>
                    <p class="text-sm text-muted dark:text-slate-400">
                        Atendida el {{ $orden->created_at->format('d/m/Y') }}@if ($orden->sede) · {{ $orden->sede->nombre }}@endif
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @can('editar_orden')
                        <a href="{{ route('ordenes.edit', $orden) }}" class="btn btn-secondary">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                            Editar
                        </a>
                    @endcan
                    <button type="button" onclick="window.print()" class="btn btn-secondary">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 2h14v20l-3-2-2 2-2-2-2 2-2-2-3 2Z"></path><path d="M9 7h6M9 11h6M9 15h4"></path></svg>
                        Comprobante
                    </button>
                    @can('ver_resultado')
                        <details class="group relative">
                            <summary class="btn btn-primary list-none [&::-webkit-details-marker]:hidden">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><path d="M6 14h12v8H6z"></path></svg>
                                Resultados
                                <svg class="h-4 w-4 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
                            </summary>
                            <div class="absolute right-0 z-20 mt-1.5 flex w-60 flex-col rounded-xl border border-borders bg-white p-1.5 shadow-lg dark:border-slate-700 dark:bg-slate-900">
                                <a href="{{ route('resultados.reporte_orden', $orden) }}" class="flex flex-col gap-0.5 rounded-md px-2.5 py-2 text-sm font-medium text-text hover:bg-secondary dark:text-slate-100 dark:hover:bg-slate-800">
                                    Imprimir terminados
                                    <span class="text-xs font-normal text-muted dark:text-slate-400">Solo exámenes con resultado</span>
                                </a>
                                <a href="{{ route('resultados.reporte_pdf', $orden) }}" class="flex flex-col gap-0.5 rounded-md px-2.5 py-2 text-sm font-medium text-text hover:bg-secondary dark:text-slate-100 dark:hover:bg-slate-800">
                                    Descargar reporte PDF
                                    <span class="text-xs font-normal text-muted dark:text-slate-400">Reporte consolidado de la orden</span>
                                </a>
                            </div>
                        </details>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Paciente --}}
        <section class="surface flex flex-wrap items-center gap-x-8 gap-y-4 p-4">
            <div class="flex min-w-0 flex-[1_1_260px] items-center gap-3">
                <span class="flex h-11 w-11 flex-none items-center justify-center rounded-full bg-secondary text-[15px] font-bold text-cyan-900 dark:bg-slate-800 dark:text-cyan-200" aria-hidden="true">{{ $paciente->iniciales }}</span>
                <div class="min-w-0">
                    @can('ver_persona')
                        <a href="{{ route('personas.show', $paciente) }}" class="text-base font-semibold text-text hover:text-primary-strong dark:text-slate-50">{{ $paciente->nombreCompleto() }}</a>
                    @else
                        <span class="text-base font-semibold">{{ $paciente->nombreCompleto() }}</span>
                    @endcan
                    <span class="block text-[13px] tabular-nums text-muted dark:text-slate-400">{{ $paciente->tipo_documento?->cod_rips }} {{ $paciente->numero_documento }}</span>
                </div>
            </div>
            <dl class="grid flex-[999_1_380px] grid-cols-3 gap-x-6 gap-y-3">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-muted dark:text-slate-400">Sexo</dt>
                    <dd class="text-sm">{{ $paciente->sexo_nombre }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-muted dark:text-slate-400">Edad</dt>
                    <dd class="text-sm">{{ $paciente->edad() ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-muted dark:text-slate-400">Nacimiento</dt>
                    <dd class="text-sm tabular-nums">{{ $paciente->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</dd>
                </div>
            </dl>
            @can('crear_orden')
                <a href="{{ route('ordenes.create', $paciente->id) }}" class="btn btn-sm btn-secondary">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
                    Nueva orden para el paciente
                </a>
            @endcan
        </section>

        {{-- Exámenes --}}
        <section class="surface">
            <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 border-b border-borders/70 px-4 py-3.5 dark:border-slate-800">
                <h2 class="text-base font-bold">Exámenes</h2>
                @if ($procedimientosVisibles->isNotEmpty())
                    <div class="flex items-center gap-2.5 text-[13px] text-muted dark:text-slate-400">
                        <span class="tabular-nums">{{ $examenesListos }} de {{ $procedimientosVisibles->count() }} listos</span>
                        <span class="block h-1.5 w-28 overflow-hidden rounded-full bg-secondary dark:bg-slate-800"
                            role="progressbar" aria-label="Exámenes listos" aria-valuemin="0" aria-valuemax="{{ $procedimientosVisibles->count() }}" aria-valuenow="{{ $examenesListos }}">
                            <span class="block h-1.5 rounded-full bg-primary-strong dark:bg-cyan-400" style="width: {{ $avanceExamenes }}%"></span>
                        </span>
                    </div>
                @endif
            </div>
            <x-index.table class="min-w-[520px]">
                <x-slot name="head">
                    <th scope="col" class="w-10 !pr-0"><span class="sr-only">Número</span></th>
                    <th scope="col">Examen</th>
                    <th scope="col" class="w-40">Toma</th>
                    <th scope="col" class="w-36">Estado</th>
                    <th scope="col" class="w-10"><span class="sr-only">Abrir</span></th>
                </x-slot>

                @forelse ($procedimientosVisibles as $procedimiento)
                    @php
                        $urlProcedimiento = auth()->user()->can('ver_resultado')
                            ? route('resultados.show', $procedimiento)
                            : route('procedimientos.show', $procedimiento);
                    @endphp
                    <tr data-url="{{ $urlProcedimiento }}" onclick="window.location.href=this.dataset.url">
                        <td class="!pr-0 text-[13px] tabular-nums text-slate-400">{{ $loop->iteration }}</td>
                        <td><a href="{{ $urlProcedimiento }}" class="font-semibold text-text hover:text-primary-strong dark:text-slate-50">{{ $procedimiento->examen->nombre }}</a></td>
                        <td class="tabular-nums text-slate-600 dark:text-slate-300">
                            {{ $procedimiento->fecha->format('d/m/Y') }}
                            <span class="text-[13px] text-muted dark:text-slate-400">{{ $procedimiento->fecha->format('H:i') }}</span>
                        </td>
                        <td><x-status-badge :estado="$procedimiento->estado" /></td>
                        <td class="text-right"><x-index.chevron /></td>
                    </tr>
                @empty
                    <x-index.empty-row colspan="5">Esta orden no tiene exámenes del laboratorio.</x-index.empty-row>
                @endforelse
            </x-index.table>
        </section>

        {{-- Remisiones --}}
        @if (auth()->user()?->hasAnyRole(['prestador', 'agente', 'admin']) && $orden->remisiones->isNotEmpty())
            <section class="surface">
                <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 border-b border-borders/70 px-4 py-3.5 dark:border-slate-800">
                    <h2 class="text-base font-bold">Remisiones</h2>
                    <span class="text-[13px] tabular-nums text-muted dark:text-slate-400">{{ $remisionesFinalizadas }} de {{ $orden->remisiones->count() }} finalizadas</span>
                </div>
                @error('estado')<p class="border-b border-borders/70 px-4 py-2 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>@enderror

                <ul class="divide-y divide-borders/70 dark:divide-slate-800">
                    @foreach ($orden->remisiones as $remision)
                        <li class="flex flex-wrap items-center gap-x-5 gap-y-2.5 px-4 py-3.5">
                            <div class="flex min-w-0 flex-[1_1_240px] flex-col gap-0.5">
                                <span class="text-sm font-semibold">{{ $remision->nombre }}</span>
                                <span class="text-[13px] text-muted dark:text-slate-400">CUP {{ $remision->cup }} · Destino: {{ $remision->destinoSede?->nombre ?? $remision->destino }}</span>
                            </div>

                            @can('editar_orden')
                                <form method="POST" action="{{ route('ordenes.remisiones.estado', [$orden, $remision]) }}"
                                    class="contents" data-remision-estado
                                    data-actual="{{ $remision->estado->value }}"
                                    data-completa="{{ $orden->remisionEsLoUltimoPendiente($remision) ? '1' : '0' }}"
                                    data-reabre="{{ $orden->terminada !== null && in_array($remision->estado->value, $finales, true) ? '1' : '0' }}"
                                    data-nombre="{{ $remision->nombre }}"
                                    data-orden="{{ $orden->numero }}">
                                    @csrf
                                    @method('PATCH')
                                    <label for="estado-remision-{{ $remision->id }}" class="sr-only">Estado de {{ $remision->nombre }}</label>
                                    <select id="estado-remision-{{ $remision->id }}" name="estado" class="estado-select">
                                        @foreach (\App\Estado::cases() as $estado)
                                            <option value="{{ $estado->value }}" @selected($remision->estado === $estado)>{{ ucfirst($estado->nombre()) }}</option>
                                        @endforeach
                                    </select>
                                    <noscript><button type="submit" class="btn btn-sm btn-primary">Guardar</button></noscript>
                                    <span class="flex-[0_0_96px] text-right text-sm font-semibold"><x-moneda :valor="$remision->valor" /></span>

                                    <div role="alertdialog" aria-live="polite" data-confirmacion
                                        class="hidden flex-[1_1_100%] flex-wrap items-center gap-x-4 gap-y-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-900 dark:bg-amber-950/40">
                                        <div class="flex flex-[1_1_280px] flex-col gap-0.5">
                                            <span class="text-sm font-semibold text-text dark:text-slate-50" data-confirmacion-titulo></span>
                                            <span class="text-[13px] text-slate-600 dark:text-slate-300" data-confirmacion-texto></span>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" class="btn btn-secondary h-9" data-cancelar>Cancelar</button>
                                            <button type="submit" class="btn btn-primary h-9">Confirmar</button>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <x-status-badge :estado="$remision->estado" />
                                <span class="flex-[0_0_96px] text-right text-sm font-semibold"><x-moneda :valor="$remision->valor" /></span>
                            @endcan
                        </li>
                    @endforeach
                </ul>
                @can('editar_orden')
                    <p class="border-t border-borders/70 bg-background/60 px-4 py-2 text-xs text-muted dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                        El estado se guarda al cambiarlo. Se pide confirmación solo si el cambio completa o reabre la orden, o si anula la remisión.
                    </p>
                @endcan
            </section>
        @endif

        {{-- Observaciones y pago --}}
        <div class="flex flex-wrap items-stretch gap-5">
            <section class="surface flex flex-[999_1_320px] flex-col gap-2 p-4">
                <h2 class="text-base font-bold">Observaciones</h2>
                <p class="whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ filled($orden->observaciones) ? $orden->observaciones : 'Sin observaciones.' }}</p>
            </section>

            <section class="surface flex-[1_1_300px]">
                <h2 class="px-4 pb-2 pt-4 text-base font-bold">Pago</h2>
                <dl class="grid grid-cols-[minmax(0,1fr)_auto] gap-x-4 gap-y-2 px-4 pb-3 text-sm">
                    <dt class="text-muted dark:text-slate-400">Subtotal</dt>
                    <dd class="text-right"><x-moneda :valor="$orden->total" /></dd>
                    @if ($orden->descuento > 0)
                        <dt class="text-muted dark:text-slate-400">Descuento</dt>
                        <dd class="text-right">−<x-moneda :valor="$orden->descuento" /></dd>
                    @endif
                    <dt class="font-semibold">Total</dt>
                    <dd class="text-right font-semibold"><x-moneda :valor="$totalOrden" /></dd>
                    <dt class="text-muted dark:text-slate-400">Abonado</dt>
                    <dd class="text-right"><x-moneda :valor="$orden->abono" /></dd>
                </dl>
                @if ($saldo > 0)
                    <div class="flex items-center justify-between gap-3 border-t border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-900 dark:bg-amber-950/40">
                        <span class="text-sm font-semibold text-amber-800 dark:text-amber-200">Saldo pendiente</span>
                        <x-moneda :valor="$saldo" class="text-[22px] font-bold" />
                    </div>
                @else
                    <div class="flex items-center justify-between gap-3 border-t border-green-200 bg-green-50 px-4 py-3 dark:border-green-900 dark:bg-green-950/40">
                        <span class="text-sm font-semibold text-green-800 dark:text-green-200">Pagado</span>
                        <x-moneda :valor="0" class="text-[22px] font-bold" />
                    </div>
                @endif
            </section>
        </div>
    </x-index.page>

    @can('editar_orden')
        <script>
            // Remisiones: guarda al cambiar el estado; confirma solo si el cambio completa/reabre la orden o anula.
            (() => {
                const finales = ['terminado', 'entregado'];
                const tonos = {
                    'pendiente de muestra': 'badge-warning',
                    'toma muestra': 'badge-info',
                    'en proceso': 'badge-warning',
                    'terminado': 'badge-success',
                    'entregado': 'badge-success',
                    'anulado': 'badge-danger',
                };
                const todosLosTonos = Object.values(tonos);

                document.querySelectorAll('form[data-remision-estado]').forEach((form) => {
                    const select = form.querySelector('select');
                    const panel = form.querySelector('[data-confirmacion]');
                    const pintar = (estado) => {
                        select.classList.remove(...todosLosTonos);
                        select.classList.add(tonos[estado] ?? 'badge-neutral');
                    };
                    const mensaje = (estado) => {
                        const { nombre, orden, completa, reabre } = form.dataset;
                        if (estado === 'anulado') {
                            return [`¿Anular la remisión ${nombre}?`, 'La remisión no se realizará.'];
                        }
                        if (finales.includes(estado) && completa === '1') {
                            return [`La orden #${orden} quedará Completada`, 'Es lo último pendiente de la orden. Puedes revertirlo después.'];
                        }
                        if (!finales.includes(estado) && reabre === '1') {
                            return [`La orden #${orden} volverá a Pendiente`, `La remisión ${nombre} dejará de contar como finalizada.`];
                        }
                        return null;
                    };

                    pintar(form.dataset.actual);

                    select.addEventListener('change', () => {
                        pintar(select.value);
                        const aviso = mensaje(select.value);
                        if (!aviso) {
                            form.submit();
                            return;
                        }
                        panel.querySelector('[data-confirmacion-titulo]').textContent = aviso[0];
                        panel.querySelector('[data-confirmacion-texto]').textContent = aviso[1];
                        panel.classList.replace('hidden', 'flex');
                        panel.querySelector('[type="submit"]').focus();
                    });

                    panel.querySelector('[data-cancelar]').addEventListener('click', () => {
                        select.value = form.dataset.actual;
                        pintar(form.dataset.actual);
                        panel.classList.replace('flex', 'hidden');
                        select.focus();
                    });
                });
            })();
        </script>
    @endcan
    <section class="Ticket hidden text-xs px-2 py-6 m-auto print:flex print:flex-wrap print:justify-between print:w-[80mm] print:bg-white">
        <div class="w-full flex-col justify-center items-center gap-2 mb-4">
            <div class="flex justify-center items-center">
                <figure class="w-20 h-20 p-1">
                    <img class="aspect-square object-cover w-full h-full rounded-md"
                        src="{{ asset('storage/logos/'.$orden->sede->logo) }}" alt="{{$orden->sede->nombre }}">
                </figure>
                <div class="flex flex-col justify-center">
                    <h2 class="font-semibold text-xl">{{$orden->sede->empresa->nombre_comercial}}</h2>
                    <p class="font-light">{{$orden->sede->nombre}}</p>
                    <p class="font-semibold">NIT: {{$orden->sede->empresa->nit}}</p>
                </div>
            </div>
            <div class="px-4 text-center">
                <p class="font-medium">{{$orden->sede->direccion?->direccion}}</p>
                <p class="font-light"> Tels:
                    @php
                    $telefonos = $orden->sede->telefonos->slice(0, 2);
                    @endphp
                    @foreach ($telefonos as $index => $telefono)
                    <span>{{ $telefono->numero }}</span>
                    @if ($index < $telefonos->count() - 1)
                        <span> | </span>
                        @endif
                        @endforeach
                </p>
                <p>
                    {{$orden->sede->direccion?->municipio?->municipio}}
                </p>
            </div>
        </div>

        <div class="flex w-full justify-between items-center gap-2">
            <div>
                <h3><span class="font-normal">Fecha: </span>
                    {{$orden->created_at->translatedFormat('d/m/Y')}}
                </h3>
            </div>

            <div class="">
                <h3><span class="font-semibold">Órden Nº: </span>
                    {{$orden->numero}}
                </h3>
            </div>
        </div>
        <div>
            <p class="font-light"><span class="font-normal">Paciente: </span>
                {{$orden->paciente->nombreCompleto()}}
            </p>
            <p class="font-light"><span class="font-normal">Identificación: </span>
                {{$orden->paciente->tipo_documento->cod_rips}}{{$orden->paciente->numero_documento}}
            </p>
            <p class="font-light"><span class="font-normal">Tels: </span>
                @if($orden->paciente->telefonos->isEmpty())
                Sin Registro
                @else
                @foreach($orden->paciente->telefonos as $telefono)
                <span>{{ $telefono->numero}}</span>
                @endforeach
                @endif
            </p>
            <br>
        </div>
        <div>
            <table>
                <thead>
                    <tr class="border-t border-text">
                        <th class="p1 text-left text-text w-60 text-sm font-normal leading-normal">Examen</th>
                        <th class="p1 text-left text-text w-16 text-sm font-normal leading-normal">Cant.</th>
                        <th class="p1 text-left text-text w-40 text-sm font-normal leading-normal">Vlr</th>
                        <th class="p1 text-left text-text w-40 text-sm font-normal leading-normal">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orden->examenes as $examen)
                    <tr>
                        <td class="content-start p1 w-60 text-xs font-light leading-normal">
                            {{ $examen->nombre }}
                        </td>
                        <td class="content-start p1 w-16 text-xs font-light leading-normal">
                            {{ $examen->pivot->cantidad }}
                        </td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            {{ number_format($examen->valor) }}
                        </td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            {{ number_format($examen->valor*$examen->pivot->cantidad, 2) }}
                        </td>
                    </tr>
                    @endforeach
                    @foreach ($orden->remisiones as $remision)
                    <tr>
                        <td class="content-start p1 w-60 text-xs font-light leading-normal">
                            {{ $remision->nombre }} (CUP: {{ $remision->cup }}) (R)
                        </td>
                        <td class="content-start p1 w-16 text-xs font-light leading-normal">1</td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            {{ number_format($remision->valor, 2) }}
                        </td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            {{ number_format($remision->valor, 2) }}
                        </td>
                    </tr>
                    @endforeach
                    @if ($orden->descuento && $orden->descuento > 0)
                    <tr>
                        <td colspan="3" class="text-end font-semibold p-1 pl-4">Descuento</td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            ${{ number_format($orden->descuento, 2) }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="text-end font-semibold p-1 pl-4">Abono</td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            ${{ number_format($orden->abono, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end font-semibold p-1 pl-4">Saldo</td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            ${{ number_format(($orden->total - $orden->descuento - $orden->abono), 2) }}
                        </td>
                    </tr>
                    <tr class="border-t border-text">
                        <td colspan="3" class="text-end font-semibold p-1 pl-4">Total</td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            ${{ number_format(($orden->total - $orden->descuento), 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="w-full flex flex-col justify-center items-center mt-4">
            <p class="text-center uppercase">Este es un recibo de una orden de examenes. No es valido como factura.</p>
            <p class="text-center">¡Presenta este recibo para reclamar los resultados!</p>
    </section>
</x-app-layout>

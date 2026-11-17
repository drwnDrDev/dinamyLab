{{--
    Corrección de resultados (ED-04). Flujo: se editan los valores → "Revisar cambios" abre un diálogo
    con el resumen anterior → nuevo y el motivo obligatorio → "Guardar cambios" envía a resultados.update.
    El comportamiento está en resources/js/lib/edicionResultadosDom.js.

    Variables: $procedimiento, $parametros (EscogerReferencia::obtenerResultados) y $cambios (resultado_cambios).
--}}
@php
    $paciente = $procedimiento->orden->paciente;
    $cambios = $cambios ?? collect();
    $documento = trim(($paciente->tipo_documento?->cod_rips ?? '').' '.$paciente->numero_documento);
    $erroresResultados = collect($errors->getMessages())
        ->filter(fn ($mensajes, $campo) => str_starts_with($campo, 'resultados'))
        ->isNotEmpty();
    $abrirRevision = $errors->has('observacion') && ! $erroresResultados;
    $hayGrupos = collect($parametros)->pluck('grupo')->filter()->isNotEmpty();
@endphp

<x-app-layout>
    <x-slot name="title">Corregir resultado · {{ $procedimiento->examen->nombre }}</x-slot>

    <div class="mx-auto max-w-5xl px-4 pb-6 pt-6">
        <a href="{{ route('resultados.show', $procedimiento) }}" class="text-sm text-primary hover:underline dark:text-cyan-300">
            ← Volver al resultado
        </a>

        <header class="mt-3 rounded-lg border border-borders bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Corrección de resultado</p>
            <h1 class="mt-1 text-xl font-semibold text-titles dark:text-white">{{ $procedimiento->examen->nombre }}</h1>
            <dl class="mt-3 grid grid-cols-2 gap-x-6 gap-y-2 text-sm sm:grid-cols-4">
                <div class="col-span-2">
                    <dt class="text-gray-500 dark:text-gray-400">Paciente</dt>
                    <dd class="font-medium text-text dark:text-gray-100">{{ $paciente->nombreCompleto() }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Documento</dt>
                    <dd class="text-text dark:text-gray-100">{{ $documento }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Edad · Sexo</dt>
                    <dd class="text-text dark:text-gray-100">{{ $paciente->edad() ?? '—' }} · {{ $paciente->sexo_nombre }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Orden</dt>
                    <dd class="text-text dark:text-gray-100">{{ $procedimiento->orden->numero }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Fecha del examen</dt>
                    <dd class="text-text dark:text-gray-100">{{ $procedimiento->fecha?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-gray-500 dark:text-gray-400">Sede</dt>
                    <dd class="text-text dark:text-gray-100">{{ $procedimiento->sede?->nombre }}</dd>
                </div>
            </dl>
        </header>

        <p class="mt-4 text-sm text-text dark:text-gray-300">
            Modifique solo los valores que necesita corregir. Antes de guardar verá un resumen y deberá indicar el motivo;
            el cambio queda en el historial con su nombre y la fecha.
        </p>

        @if ($errors->any())
            <div class="mt-4 rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200" role="alert">
                <p class="font-semibold">Revise la información antes de guardar:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('resultados.update', $procedimiento) }}"
            method="POST"
            data-edicion-resultados
            data-abrir-revision="{{ $abrirRevision ? 'true' : 'false' }}"
            class="mt-4"
        >
            @csrf
            @method('PUT')

            <div class="overflow-x-auto rounded-lg border border-borders bg-white dark:border-slate-700 dark:bg-slate-900">
                <table class="w-full text-sm">
                    <thead class="bg-secondary text-left text-xs uppercase tracking-wide text-gray-600 dark:bg-slate-800 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="py-2 pl-4 pr-3">Parámetro</th>
                            <th scope="col" class="py-2 pr-3">Valor actual</th>
                            <th scope="col" class="py-2 pr-3">Nuevo valor</th>
                            <th scope="col" class="py-2 pr-3">Unidades</th>
                            <th scope="col" class="py-2 pr-3">Referencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grupoActual = null; @endphp
                        @foreach ($parametros as $p)
                            @if ($hayGrupos && ($p['grupo'] ?? null) !== $grupoActual)
                                @php $grupoActual = $p['grupo'] ?? null; @endphp
                                <tr class="border-b border-borders/60 bg-gray-50 dark:bg-slate-800/60">
                                    <th scope="colgroup" colspan="5" class="py-1.5 pl-4 text-left text-xs font-semibold uppercase tracking-wide text-titles dark:text-cyan-200">
                                        {{ $grupoActual ?? 'Otros' }}
                                    </th>
                                </tr>
                            @endif
                            <x-parametro-edit :parametro="$p" />
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="sticky bottom-0 z-10 mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-borders bg-white/95 px-4 py-3 backdrop-blur dark:border-slate-700 dark:bg-slate-900/95">
                <p class="text-sm font-medium text-text dark:text-gray-200" data-contador aria-live="polite">Sin cambios</p>
                <div class="flex gap-2">
                    <a href="{{ route('resultados.show', $procedimiento) }}" class="rounded-md border border-borders px-4 py-2 text-sm text-text hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-slate-800">
                        Cancelar
                    </a>
                    <button type="button" data-revisar class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-titles disabled:cursor-not-allowed disabled:opacity-50">
                        Revisar cambios
                    </button>
                </div>
            </div>

            <dialog
                data-dialogo-revision
                aria-labelledby="titulo-revision"
                class="w-full max-w-2xl rounded-lg border border-borders bg-white p-0 text-text shadow-xl backdrop:bg-black/40 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-100"
            >
                <div class="p-5">
                    <h2 id="titulo-revision" class="text-lg font-semibold text-titles dark:text-white">Confirmar cambios</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $procedimiento->examen->nombre }} · {{ $paciente->nombreCompleto() }} · Orden {{ $procedimiento->orden->numero }}
                    </p>

                    <div class="mt-4 max-h-64 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="pb-1 pr-3">Parámetro</th>
                                    <th scope="col" class="pb-1 pr-3">Anterior</th>
                                    <th scope="col" class="pb-1 pr-3">Nuevo</th>
                                </tr>
                            </thead>
                            <tbody data-resumen></tbody>
                        </table>
                    </div>

                    <label for="observacion" class="mt-4 block text-sm font-semibold">
                        Motivo de la modificación <span class="text-red-600">*</span>
                    </label>
                    <textarea
                        id="observacion"
                        name="observacion"
                        rows="3"
                        maxlength="1000"
                        data-motivo
                        @error('observacion') aria-invalid="true" aria-describedby="error-observacion" @enderror
                        class="mt-1 w-full rounded-md border-borders bg-white p-2 text-sm focus:border-primary focus:ring-primary dark:bg-slate-800 dark:text-white"
                        placeholder="Ej.: error de transcripción del equipo"
                    >{{ old('observacion') }}</textarea>
                    @error('observacion')
                        <p id="error-observacion" class="mt-1 text-xs text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end gap-2 border-t border-borders bg-gray-50 px-5 py-3 dark:border-slate-700 dark:bg-slate-800">
                    <button type="button" data-cerrar class="rounded-md border border-borders bg-white px-4 py-2 text-sm hover:bg-gray-50 dark:bg-slate-900 dark:hover:bg-slate-700">
                        Seguir editando
                    </button>
                    <button type="submit" data-confirmar class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-titles disabled:opacity-60">
                        Guardar cambios
                    </button>
                </div>
            </dialog>
        </form>

        @if ($cambios->isNotEmpty())
            <details class="mt-6 rounded-lg border border-borders bg-white dark:border-slate-700 dark:bg-slate-900">
                <summary class="cursor-pointer px-4 py-3 text-sm font-semibold text-titles dark:text-white">
                    Historial de modificaciones ({{ $cambios->count() }})
                </summary>
                <div class="overflow-x-auto border-t border-borders dark:border-slate-700">
                    <table class="w-full text-sm">
                        <thead class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="py-2 pl-4 pr-3">Fecha</th>
                                <th scope="col" class="py-2 pr-3">Parámetro</th>
                                <th scope="col" class="py-2 pr-3">Anterior → Nuevo</th>
                                <th scope="col" class="py-2 pr-3">Modificado por</th>
                                <th scope="col" class="py-2 pr-3">Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cambios as $cambio)
                                <tr class="border-t border-borders/60 align-top dark:border-slate-700">
                                    <td class="whitespace-nowrap py-2 pl-4 pr-3">{{ $cambio->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2 pr-3 uppercase">{{ $cambio->parametro_nombre }}</td>
                                    <td class="py-2 pr-3">
                                        <span class="text-gray-500 dark:text-gray-400">{{ $cambio->valor_anterior ?? 'Vacío' }}</span>
                                        → <span class="font-medium">{{ $cambio->valor_nuevo ?? 'Vacío' }}</span>
                                    </td>
                                    <td class="py-2 pr-3">{{ $cambio->usuario_nombre }}</td>
                                    <td class="py-2 pr-3">{{ $cambio->observacion }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @endif
    </div>
</x-app-layout>

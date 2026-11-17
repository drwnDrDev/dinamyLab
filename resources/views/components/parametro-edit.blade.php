{{--
    Fila de la tabla de edición de resultados (ED-04). El JS de lib/edicionResultadosDom.js
    lee data-original / data-min / data-max y marca data-modificado="true" cuando el valor cambia.
--}}
@props(['parametro'])

@php
    $id = $parametro['id'];
    $campoId = 'resultado-'.$id;
    $fieldName = 'resultados['.$id.']';
    $original = (string) ($parametro['resultado'] ?? '');
    $fieldValue = old('resultados.'.$id, $original);
    $tipo = $parametro['tipo_dato'] ?? 'text';
    $esTexto = $tipo === 'textarea';
    $actualFuera = ! ($parametro['es_normal'] ?? true) && is_numeric($original);
    $tieneError = $errors->has('resultados.'.$id);
    $claseCampo = 'w-full rounded-md border-borders bg-white px-2 py-1.5 text-sm text-text focus:border-primary focus:ring-primary dark:bg-slate-800 dark:text-white'
        .($tieneError ? ' border-red-500 ring-1 ring-red-500' : '');
    $describe = $tieneError ? 'error-'.$campoId : null;
@endphp

<tr
    data-parametro
    data-id="{{ $id }}"
    data-nombre="{{ $parametro['nombre'] }}"
    data-original="{{ $original }}"
    data-min="{{ $parametro['referencia_min'] ?? '' }}"
    data-max="{{ $parametro['referencia_max'] ?? '' }}"
    data-modificado="false"
    class="group border-b border-borders/60 align-top transition-colors data-[modificado=true]:bg-amber-50 dark:data-[modificado=true]:bg-amber-950/40"
>
    <th scope="row" class="border-l-4 border-transparent py-2 pl-3 pr-3 text-left font-medium uppercase text-text dark:text-gray-200 group-data-[modificado=true]:border-amber-500">
        <label for="{{ $campoId }}">{{ $parametro['nombre'] }}</label>
        @if (filled($parametro['metodo'] ?? null))
            <span class="block text-xs font-normal normal-case text-gray-500 dark:text-gray-400">{{ $parametro['metodo'] }}</span>
        @endif
    </th>

    <td class="py-2 pr-3 text-text dark:text-gray-300 {{ $esTexto ? 'max-w-[14rem] whitespace-pre-line' : 'whitespace-nowrap' }}">
        @if ($original === '')
            <span class="text-gray-400">Vacío</span>
        @else
            <span @class(['font-semibold text-red-700 dark:text-red-300' => $actualFuera])>{{ $original }}</span>
            @if ($actualFuera)
                <span class="ml-1 rounded bg-red-100 px-1 text-xs font-semibold text-red-800 dark:bg-red-950 dark:text-red-200">Fuera de rango</span>
            @endif
        @endif
    </td>

    <td class="py-2 pr-3" @if ($esTexto) colspan="3" @endif>
        @switch ($tipo)
            @case ('number')
                <input type="number" step="any" inputmode="decimal" name="{{ $fieldName }}" id="{{ $campoId }}" value="{{ $fieldValue }}" data-campo class="{{ $claseCampo }} min-w-[7rem]" @if ($describe) aria-invalid="true" aria-describedby="{{ $describe }}" @endif>
                @break
            @case ('date')
                <input type="date" name="{{ $fieldName }}" id="{{ $campoId }}" value="{{ $fieldValue }}" data-campo class="{{ $claseCampo }}" @if ($describe) aria-invalid="true" aria-describedby="{{ $describe }}" @endif>
                @break
            @case ('select')
                <select name="{{ $fieldName }}" id="{{ $campoId }}" data-campo class="{{ $claseCampo }} pr-8 uppercase" @if ($describe) aria-invalid="true" aria-describedby="{{ $describe }}" @endif>
                    <option value="">Seleccione</option>
                    @foreach ($parametro['opciones'] as $opcion)
                        <option value="{{ $opcion }}" @selected((string) $fieldValue === (string) $opcion)>{{ $opcion }}</option>
                    @endforeach
                </select>
                @break
            @case ('list')
                <input list="opciones-{{ $id }}" name="{{ $fieldName }}" id="{{ $campoId }}" value="{{ $fieldValue }}" data-campo class="{{ $claseCampo }}" @if ($describe) aria-invalid="true" aria-describedby="{{ $describe }}" @endif>
                <datalist id="opciones-{{ $id }}">
                    @foreach ($parametro['opciones'] as $opcion)
                        <option value="{{ $opcion }}"></option>
                    @endforeach
                </datalist>
                @break
            @case ('textarea')
                <textarea name="{{ $fieldName }}" id="{{ $campoId }}" rows="2" data-campo class="{{ $claseCampo }}" @if ($describe) aria-invalid="true" aria-describedby="{{ $describe }}" @endif>{{ $fieldValue }}</textarea>
                @break
            @default
                <input type="text" name="{{ $fieldName }}" id="{{ $campoId }}" value="{{ $fieldValue }}" data-campo class="{{ $claseCampo }}" @if ($describe) aria-invalid="true" aria-describedby="{{ $describe }}" @endif>
        @endswitch
        <span data-rango-nuevo hidden class="mt-1 inline-block rounded bg-red-100 px-1.5 py-0.5 text-xs font-semibold text-red-800 dark:bg-red-950 dark:text-red-200" aria-live="polite"></span>
        @if ($tieneError)
            <p id="{{ $describe }}" class="mt-1 text-xs text-red-700 dark:text-red-300">{{ $errors->first('resultados.'.$id) }}</p>
        @endif
    </td>

    @unless ($esTexto)
        <td class="whitespace-nowrap py-2 pr-3 text-gray-600 dark:text-gray-400">{{ $parametro['unidades'] }}</td>
        <td class="py-2 pr-3 text-gray-600 dark:text-gray-400">{{ $parametro['referencia'] }}</td>
    @endunless
</tr>

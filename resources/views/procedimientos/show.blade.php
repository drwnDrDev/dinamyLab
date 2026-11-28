<x-app-layout >
    <x-slot name="title">
       {{$procedimiento->orden->paciente->nombreCompleto()}} - {{ $procedimiento->examen->nombre }}
    </x-slot>
<x-slot name="header">
            <h2 class="text-xl font-semibold">{{$procedimiento->orden->paciente->nombreCompleto()}}</h2>
          <p> Edad: {{ $procedimiento->orden->paciente->edad()}} </p>  <h2 class="text-xl font-semibold">{{$procedimiento->orden->paciente->tipo_documento->cod_rips}} {{$procedimiento->orden->paciente->numero_documento}}</h2>

    </x-slot>
    <x-canva>
        <div class="flex justify-around">
            <h2>Numero de orden <span class="font-semibold">{{$procedimiento->orden->numero}}</span></h2>
        </div>
        <div class="my-4"><x-solicitar-anulacion :procedimiento="$procedimiento" /></div>
        @can('gestionar_procedimiento')
            @if (! $procedimiento->bloqueadoPorRips() && ($procedimiento->estado !== \App\Estado::ANULADO->value || auth()->user()->hasRole('admin')))
                <form action="{{ route('procedimientos.observaciones', $procedimiento) }}" method="POST" class="my-4 flex flex-wrap items-end gap-3 rounded-lg border border-borders p-4">
                    @csrf
                    @method('PATCH')
                    <label class="min-w-52 flex-1 text-sm font-medium">
                        Cambiar estado
                        <select name="estado" required class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                            @foreach (\App\Estado::cases() as $estado)
                                @continue($estado === \App\Estado::ANULADO && ! auth()->user()->hasRole('admin'))
                                <option value="{{ $estado->value }}" @selected($procedimiento->estado === $estado->value)>{{ ucfirst($estado->nombre()) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="min-w-52 flex-[2] text-sm font-medium">
                        Observación
                        <input name="observacion" required maxlength="255" class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                    </label>
                    <button type="submit" class="btn btn-primary">Guardar estado</button>
                </form>
            @elseif ($procedimiento->bloqueadoPorRips())
                <p class="my-4 rounded-md border border-amber-300 bg-amber-50 p-3 text-sm text-amber-900">Este procedimiento fue exportado a RIPS y su estado está bloqueado.</p>
            @else
                <p class="my-4 text-sm">Este examen fue anulado. Solo administración puede modificar su estado.</p>
            @endif
        @endcan
        @can('eliminar_procedimiento')
            @unless ($procedimiento->bloqueadoPorRips())
                <form action="{{ route('procedimientos.destroy', $procedimiento) }}" method="POST" class="my-3" onsubmit="return confirm('¿Eliminar este procedimiento?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-semibold text-red-700 underline">Eliminar procedimiento</button>
                </form>
            @endunless
        @endcan
        @if ($navegacion['anterior'] || $navegacion['siguiente'])
            <nav class="my-4 flex flex-wrap items-center justify-between gap-3" aria-label="Navegar por los exámenes de la orden">
                @if ($navegacion['anterior'])
                    <a class="text-primary underline" href="{{ route('procedimientos.show', $navegacion['anterior']) }}">
                        ← Anterior: {{ $navegacion['anterior']->examen->nombre }}
                    </a>
                @else
                    <span></span>
                @endif
                @if ($navegacion['siguiente'])
                    <a class="text-primary underline" href="{{ route('procedimientos.show', $navegacion['siguiente']) }}">
                        Siguiente: {{ $navegacion['siguiente']->examen->nombre }} →
                    </a>
                @endif
            </nav>
        @endif
        <div>
            <h2 class="font-bold mb-4 text-xl text-secondary">Resultados del Examen</h2>
            <p class="text-gray-600 mb-4">Examen: {{ $procedimiento->examen->nombre }}</p>
            <p class="text-gray-600 mb-4">CUP: {{ $procedimiento->examen->cup }}</p>
            <p class="text-gray-600 mb-4">Valor: ${{ number_format($procedimiento->examen->valor, 2) }}</p>
        </div>
        <form method="POST"  class="mt-4">
            @csrf
            <div class="w-full bg-white p-4 border border-gray-300 rounded-sm shadow-md my-4">
                <x-input-label for="resultado">Resultado</x-input-label>
                <textarea id="resultado" name="resultado" class="form-textarea w-full h-32" placeholder="Escribe aquí el resultado del examen..."></textarea>
            </div>
            <div class="flex justify-end">
                <x-primary-button type="submit">Guardar Resultado</x-primary-button>
            </div>

    </x-canva>




</x-app-layout>

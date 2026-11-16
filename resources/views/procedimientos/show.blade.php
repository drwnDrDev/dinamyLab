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

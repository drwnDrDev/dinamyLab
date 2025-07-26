<x-app-layout>

    <x-canva>
        <div class="py-4 flex justify-between items-center border-b border-borders">
            <div>
                <p class="text-2xl font-bold leading-tight tracking-[-0.015em]">{{ $persona->nombreCompleto() }}</p>
                <p class="text-titles">Paciente ID: {{ $persona->numero_documento }}</p>
            </div>
        </div>
        <h3>Selecciona los procedimientos que se van a incluir:</h1>

            <form action="{{route('resultados.historia_show', $persona)}}" method="POST">
                @csrf
                @foreach($ordenes as $orden)
                <div class="flex gap-4 border border-borders mb-4">
                    <span>Número de Orden: {{ $orden->id }}</span>
                    <span> Fecha de creación: {{ $orden->created_at->format('d-m-Y') }}</span>
                </div>
                @foreach($orden->procedimientos as $procedimiento)
                    <div class="flex gap-4">
                        <span>{{ $procedimiento->created_at->format('d-m-Y') }}
                        </span>
                        <span>{{ $procedimiento->examen->nombre }}</span>
                        <span>{{ $procedimiento->estado }}</span>
                        <input type="checkbox" name="{{$procedimiento->id}}" id="{{$procedimiento->id}}">
                    </div>

                @endforeach
                @endforeach

                <x-primary-button>Imprimir</x-primary-button>
            </form>
            @dump($persona)
            @dump($ordenes)
    </x-canva>
</x-app-layout>
<x-app-layout>

    <x-canva>
        <div class="py-4 flex justify-between items-center border-b border-borders">
            <div>
                <p class="text-2xl font-bold leading-tight tracking-[-0.015em]">{{ $persona->nombreCompleto() }}</p>
                <p class="text-titles">Paciente ID: {{ $persona->numero_documento }}</p>
            </div>
        </div>
        <h3>Selecciona los procedimientos que se van a incluir:</h1>

            <form action="{{route('resultados.historia_show', $persona)}}" method="POST" target="_blank">
                @csrf

                @foreach($ordenes as $orden)
                <div class="flex gap-4 border border-borders mb-4">
                    <span>Número de Orden: {{ $orden->id }}</span>
                    <span> Fecha de creación: {{ $orden->created_at->format('d-m-Y') }}</span>
                </div>
                @isset($orden->procedimientos)
                    @foreach($orden->procedimientos as $procedimiento)
                    @if($procedimiento->estado === 'terminado')
                        <div class="grid grid-cols-4 gap-4">

                            <span><input type="checkbox" name="{{$procedimiento->id}}" id="{{$procedimiento->id}}"
                                            class="mr-4">
                                {{ $procedimiento->created_at->format('d-m-Y') }}
                            </span>
                            <span>{{ $procedimiento->examen->nombre }}</span>

                            <span>{{ $procedimiento->empleado?->user->name}}</span>
                        </div>
                    @endif

                    @endforeach
                @endisset
                @endforeach

                <x-primary-button>Imprimir</x-primary-button>
            </form>

    </x-canva>
</x-app-layout>

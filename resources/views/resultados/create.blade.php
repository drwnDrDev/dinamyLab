<x-app-layout>
    <x-slot name="title">
        Nuevo Resultado
    </x-slot>
    <x-slot name="header">
    </x-slot>
    <x-canva>
        <section class="grid grid-cols-2 py-4 mb-4 border-b border-t border-borders  ">
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">

                <span class="font-bold ">Paciente: </span>
                <h3>{{$procedimiento->orden->paciente->nombreCompleto()}}</h3>
                <span class="font-bold ">Identificación: </span>
                <h3>{{$procedimiento->orden->paciente->tipo_documento}}{{$procedimiento->orden->paciente->numero_documento}}</h3>
                <span class="font-bold ">Sexo: </span>
                <h3>{{$procedimiento->orden->paciente->sexo}} </h3>

            </div>
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                <span class="font-bold ">Fecha de atención: </span>
                <h3>{{$procedimiento->orden->created_at}}</h3>
                <span class="font-bold ">Número de órden: </span>
                <h3>{{$procedimiento->orden->numero}}</h3>
                <span class="font-bold ">Edad: </span>
                <h3>{{$procedimiento->orden->paciente->edad()}} años</h3>

            </div>
        </section>

        <h1 class="text-2xl font-bold text-center mb-4">{{$procedimiento->examen->nombre}}</h1>
        <form action="{{ route('resultados.store', $procedimiento) }}" method="POST">
            @csrf

            @php
            $lastGroup = null;
            $parametros = $procedimiento->examen->parametros->sortBy('pivot.orden');
            @endphp

            @foreach ($parametros as $parametro)
                @if ($parametro->grupo && $parametro->grupo !== $lastGroup)
                    <h3 class="font-bold text-xl">{{ $parametro->grupo }}</h3>
                    @php
                    $lastGroup = $parametro->grupo;
                    @endphp
                @endif

                <div class="Componente_param pl-4 mb-2">

                
                    <label for="{{$parametro->slug}}">{{$parametro->nombre}}</label>
                    <input type="text" name="$parametro->slug" id="{{$parametro->id}}"><span>{{$parametro->pivot->orden}}</span>
                </div>

            @endforeach
            @dump($parametro)

        </form>
    </x-canva>
</x-app-layout>
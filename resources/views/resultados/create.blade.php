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

        <h1 class="text-2xl font-bold text-center mb-4 uppercase">{{$procedimiento->examen->nombre}}</h1>

        <div class="header_resultado grid grid-cols-4 gap-2 pl-4 mb-2 border-b border-t border-borders items-center text-titles">
            <h2 class="text-lg font-bold">Parametro</h2>
            <h2 class="text-lg text-end font-bold">Resultado</h2>
            <h2 class="text-lg font-bold">U. Medida</h2>
            <h2 class="text-lg font-bold">Valor de Referencia</h2>
        </div>
        <form action="{{ route('resultados.store', $procedimiento) }}" method="POST">
            @csrf
            @php
            $lastGroup = null;
            @endphp

            @foreach ($parametros as $parametro)

                @if ($parametro['grupo'] && $parametro['grupo'] !== $lastGroup)
                    <h3 class="pt-4 font-semibold uppercase col-span-full">{{ $parametro['grupo'] }}</h3>
                    @php
                    $lastGroup = $parametro['grupo'];
                    @endphp
                @endif

                <x-parametro-input :parametro="$parametro"/>
            @endforeach
                
         </form>
         <x-primary-button>guardar</x-primary-button>

    </x-canva>
</x-app-layout>

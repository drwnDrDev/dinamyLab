<x-app-layout>
    <x-slot name="title">
       Resultados de Procedimientos
    </x-slot>
    <x-slot name="header">
    </x-slot>
    <x-canva>


        <section class="grid grid-cols-2 py-2 border-t-[0.2px] border-b-[0.2px] border-borders w-full">
            <div class="col-span-2 flex gap-2">
                <span class="font-normal ">Paciente: </span>
                <h3 class="text-titles font-normal">{{$procedimiento->orden->paciente->nombreCompleto()}}</h3>
            </div>
            <div class="w-full grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">

                <span class="font-normal ">Identificación: </span>
                <h3 class="text-titles font-light">{{$procedimiento->orden->paciente->tipo_documento->cod_rips}}{{$procedimiento->orden->paciente->numero_documento}}</h3>
                <span class="font-normal ">Sexo: </span>
                <h3 class="text-titles font-light">{{$procedimiento->orden->paciente->sexo==="M" ? 'Masculino':'Femenino'}} </h3>
                <span class="font-normal ">Edad: </span>
                <h3 class="text-titles font-light">{{$procedimiento->orden->paciente->edad()}}</h3>
            </div>
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                <span class="font-normal ">Fecha de atención: </span>
                <h3 class="text-titles font-light">{{$procedimiento->orden->created_at->format('d-m-Y')}}</h3>
                <span>Fecha de Nacimiento: </span>
                <h3 class="text-titles font-light">{{$procedimiento->orden->paciente->fecha_nacimiento->format('d-m-Y')}}</h3>
                <span class="font-normal ">Órden Nº: </span>
                <h3 class="text-titles font-light">{{$procedimiento->orden->numero}}</h3>

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
            <div class="col-span-full text-center mt-4">
                <x-primary-button>guardar resultado</x-primary-button>
            </div>
         </form>


    </x-canva>
</x-app-layout>

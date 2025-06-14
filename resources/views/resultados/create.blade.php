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

            @foreach ($procedimiento->examen->parametros as $parametro)

        @if($parametro->tipo_dato=='select' || $parametro->tipo_dato=='list')
            <label for="{{$parametro->slug}}">{{$parametro->nombre}}</label>
            <select name="$parametro->slug" id="{{$parametro->id}}">
            @foreach ($parametro->opciones as $opcion)
             <option value="{{$opcion->valor}}">{{$opcion->valor}}</option>
            @endforeach


            </select>
            @endif



            @endforeach


        </form>
    </x-canva>
</x-app-layout>

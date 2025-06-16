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
            <label for="{{$parametro->id}}">{{$parametro->nombre}}</label>
            <select name="$parametro->id" id="{{$parametro->id}}">
            @foreach ($parametro->opciones as $opcion)
             <option value="{{$opcion->valor}}">{{$opcion->valor}}</option>
            @endforeach


            </select>

            @elseif($parametro->tipo_dato=='text' || $parametro->tipo_dato=='number')
            <label for="{{$parametro->id}}">{{$parametro->nombre}}</label>
            <input type="{{$parametro->tipo_dato}}" name="{{$parametro->id}}" id="{{$parametro->id}}"
                class="border border-borders rounded-md p-2 w-full" required>
            @elseif($parametro->tipo_dato=='date')
            <label for="{{$parametro->id}}">{{$parametro->nombre}}</label>
            <input type="{{$parametro->tipo_dato}}" name="{{$parametro->id}}" id="{{$parametro->id}}"
                class="border border-borders rounded-md p-2 w-full" required>
            @elseif($parametro->tipo_dato=='textarea')
            <label for="{{$parametro->id}}">{{$parametro->nombre}}</label>
            <textarea name="{{$parametro->id}}" id="{{$parametro->id}}"
                class="border border-borders rounded-md p-2 w-full" required></textarea>
            @endif
            @endforeach
        <div>
            <button type="submit"
                class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark transition-colors">Guardar
                Resultado</button>
        </div>

        </form>
    </x-canva>
</x-app-layout>

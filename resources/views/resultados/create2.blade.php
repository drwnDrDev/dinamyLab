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

<<<<<<< HEAD:resources/views/resultados/create.blade.php
            @php
            $lastGroup = null;
            $parametros = $procedimiento->examen->parametros->sortBy('pivot.orden');
            @endphp

            @foreach ($parametros as $parametro)
                <div class="Componente_param grid grid-cols-4 gap-4 pl-4 mb-2">
                @if ($parametro->grupo && $parametro->grupo !== $lastGroup)
                    <h3 class="font-bold text-xl uppercase col-span-full">{{ $parametro->grupo }}</h3>
                    @php
                    $lastGroup = $parametro->grupo;
                    @endphp
                @endif

                @switch ($parametro->tipo_dato)
                    @case ('texto'):
                        
                            <div><label for="{{$parametro->id}}">{{ucfirst($parametro->nombre)}}</label></div>
                            <div><input type="text" name="$parametro->id" id="{{$parametro->id}}" value="{{$parametro->default}}">
                            <div><p>{{$parametro->unidades}}</p></div>
                            <div><p>100 - 200</p></div>
                        
                    @break
                    @case ('numero')
                        
                            <div><label for="{{$parametro->id}}">{{ucfirst($parametro->nombre)}}</label></div>
                            <div><input type="number" name="{{$parametro->id}}" id="{{$parametro->id}}" value="{{$parametro->default}}"></div>
                            <div><p>{{$parametro->unidades}}</p></div>
                            <div><p>100 - 200</p></div>
                    @break
                    @case ('date')
                        
                            <div><label for="{{$parametro->id}}">{{ucfirst($parametro->nombre)}}</label></div>
                            <div><input type="date" name="{{$parametro->id}}" id="{{$parametro->id}}" value="{{$parametro->default}}"></div>
                            <div><p>{{$parametro->unidades}}</p></div>
                            <div><p>100 - 200</p></div>
                    @break
                    @case ('select')
                        
                            <div><label for="{{$parametro->id}}">{{ucfirst($parametro->nombre)}}</label></div>
                            <div><select name="{{$parametro->id}}" id="{{$parametro->id}}">
                                @foreach ($parametro->opciones as $opcion)
                                    <option value="{{$opcion->valor}}" {{ $opcion->valor == $parametro->default ? 'selected' : '' }}>{{$opcion->nombre}}</option>
                                @endforeach
                            </select></div>
                            <div><p>{{$parametro->unidades}}</p></div>
                            <div><p>100 - 200</p></div>
                        
                    @break
                    
                    @default
                        
                            <div><label for="{{$parametro->id}}">{{ucfirst($parametro->nombre)}}</label></div>
                            <div><input type="text" name="$parametro->id" id="{{$parametro->id}}" value="{{$parametro->default}}">
                            <span>{{$parametro->pivot->orden}}</span></div>
                            <div><p>{{$parametro->unidades}}</p></div>
                            <div><p>100 - 200</p></div>

                        
                @endswitch
               
            
                    <!-- <div><label for="{{$parametro->id}}">{{ucfirst($parametro->nombre)}}</label></div>
                    <div><input type="text" name="$parametro->id" id="{{$parametro->id}}" value="{{$parametro->default}}">
                    <span>{{$parametro->pivot->orden}}</span></div>
                    <div><p>{{$parametro->unidades}}</p></div>
                    <div><p>100 - 200</p></div> -->
                    
                </div>  

            @endforeach
            @dump($parametro)
=======
            @foreach ($parametros as $parametro)
                @dump($parametro)
            @endforeach
        <div>
            <button type="submit"
                class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark transition-colors">Guardar
                Resultado</button>
        </div>
>>>>>>> 281792cc2306ac71b98c3ac81e90b211f40595be:resources/views/resultados/create2.blade.php

        </form>
    </x-canva>
</x-app-layout>
<x-app-layout>
    <x-slot name="title">
        Nuevo Resultado
    </x-slot>
    <x-slot name="header">
        <section class="hidden print:grid print:grid-cols-3"> 
            Esta es la historia de un sabado
            <!-- ESTO QUE ES??? -->
        </section>
    </x-slot>
    <x-canva>
        <section class="grid grid-cols-2 py-4 mb-4 border-b border-t border-borders  ">
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                   
                <span class="font-bold ">Paciente: </span><h3>{{$procedimiento->orden->paciente->nombreCompleto()}}</h3>
                <span class="font-bold ">Identificación: </span><h3>{{$procedimiento->orden->paciente->tipo_documento}}{{$procedimiento->orden->paciente->numero_documento}}</h3>
                <span class="font-bold ">Sexo: </span><h3>{{$procedimiento->orden->paciente->sexo}} </h3>
                
            </div>
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                <span class="font-bold ">Fecha de atención: </span><h3>{{$procedimiento->orden->created_at}}</h3>
                <span class="font-bold ">Número de órden: </span><h3>{{$procedimiento->orden->numero}}</h3>
                <span class="font-bold ">Edad: </span><h3>{{$procedimiento->orden->paciente->edad()}} años</h3>

            </div>
        </section>

        <h1 class="text-2xl font-bold text-center mb-4">{{$procedimiento->examen->nombre}}</h1>

    @includeIf('resultados.resultados.'.$procedimiento->examen->slug(), ['paciente' => $procedimiento->orden->paciente,'resultados'=>$procedimiento->resultados['data'], 'isResultado' => true])

    </x-canva>

    

</x-app-layout>
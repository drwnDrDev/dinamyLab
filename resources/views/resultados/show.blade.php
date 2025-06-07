<x-app-layout >
    <x-slot name="title">
   Nuevo Resultado
    </x-slot>
    <x-slot name="header" >
    <section class="grid grid-cols-2 p-2 bg-white dark:bg-gray-800 dark:text-white shadow sm:rounded-lg">
    <div>
    <h2>{{$procedimiento->orden->paciente->nombreCompleto()}}</h2>
    <h3>{{$procedimiento->orden->paciente->tipo_documento}} {{$procedimiento->orden->paciente->numero_documento}}</h3>
    <h3>Inicio de atencion  {{$procedimiento->orden->created_at}}</h3>
    </div>
    <div>
        <h3>{{$procedimiento->orden->paciente->edad()}} años</h3>
        <h3>Sexo: {{$procedimiento->orden->paciente->sexo}} </h3>

    </div>
    </section>
    </x-slot>

    <h1 class="text-2xl font-bold text-center">{{$procedimiento->examen->nombre}}</h1>

    @includeIf('resultados.componentes.'.$procedimiento->examen->slug(), ['paciente' => $procedimiento->orden->paciente,'resultados'=>$procedimiento->resultados['data'], 'isResultado' => true])


</x-app-layout>

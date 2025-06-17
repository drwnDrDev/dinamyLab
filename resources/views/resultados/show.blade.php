<x-app-layout>
    <x-slot name="title">
        Nuevo Resultado
    </x-slot>
    <x-slot name="header">
        <section class="hidden print:grid print:grid-cols-3">
            <x-application-logo class="w-24 h-24 mx-auto p-0" />
            <div class="col-span-2 text-center">
                <h1 class="text-lg font-bold"></h1>
                <h2 class="text-md font-semibold">Hospital San Juan de Dios</h2>
                <p class="text-xs">Calle 123 # 456 - 789</p>
                <p class="text-xs">Teléfono: (123) 456-7890</p>
                <p class="text-xs">Email: </p>
            </div>

            <!-- Precuntece más bien que deberia ser??? -->
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

   @foreach ( $procedimiento->resultado as $p)
   @dump($p)
<div class="grid grid-cols-4"><p> {{$p->parametro->nombre}} </p><p>{{$p->resultado}}</p>
<p>{{$p->valor_referencia}}</p>
</div>

   @endforeach

    </x-canva>



</x-app-layout>

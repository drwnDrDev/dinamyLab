<x-app-layout>
    <x-slot name="title">
        resultado_{{$procedimiento->orden->paciente->tipo_documento}}
    </x-slot>

    <x-canva class="print:!p-0 print:!bg-white print:w-letter print:!h-letter print:!m-0 print:!border-none print:text-xs print:!overflow-hidden">
        <section class="hidden print:grid print:grid-cols-3">
            <x-application-logo class="w-24 h-24 mx-auto p-0" />
            <h1 class="font-bold"></h1>
            <h2 class="font-semibold">Hospital San Juan de Dios</h2>
            <p class="">Calle 123 # 456 - 789</p>
            <p class="">Teléfono: (123) 456-7890</p>
            <p class="">Email: </p>

        </section>
        <h1 class="font-bold text-center mb-4 uppercase">{{$procedimiento->examen->nombre}}</h1>
        <section class="grid grid-cols-2 py-4 border-t border-borders  ">
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">

                <span class="font-bold ">Paciente: </span>
                <h3>{{$procedimiento->orden->paciente->nombreCompleto()}}</h3>
                <span class="font-bold ">Identificación: </span>
                
                <div class="flex">
                    <h3>{{$procedimiento->orden->paciente->tipo_documento}}{{$procedimiento->orden->paciente->numero_documento}}</h3>
                    <div class="flex pl-4 gap-2">
                        <span class="font-bold ">Sexo: </span>
                        <h3>{{$procedimiento->orden->paciente->sexo}} </h3>
                    </div>
                    <div class="flex pl-4 gap-2">
                        <span class="font-bold ">Edad: </span>
                        <h3>{{$procedimiento->orden->paciente->edad()}}</h3>
                    </div>
                </div>

            </div>
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                <span class="font-bold ">Fecha de atención: </span>
                <h3>{{$procedimiento->orden->created_at}}</h3>
                <span class="font-bold ">Número de órden: </span>
                <h3>{{$procedimiento->orden->numero}}</h3>

            </div>
        </section>

        <div class="header_resultado grid [grid-template-columns:2fr_1fr_1fr_1fr] gap-2 pl-4 mb-2 bg-secondary text-titles">
            <h2 class="font-bold">Parametro</h2>
            <h2 class="font-bold text-end">Resultado</h2>
            <h2 class="font-bold">U. Medida</h2>
            <h2 class="font-bold">Valor de Referencia</h2>
        </div>

        @php
        $lastGroup = null;
        @endphp

        @foreach ( $procedimiento->resultado as $p)

        @if ($p->parametro->grupo && $p->parametro->grupo !== $lastGroup)
        <h3 class="pt-2 pl-2 font-semibold uppercase col-span-full">{{ $p->parametro->grupo}}</h3>
        @php
        $lastGroup = $p->parametro->grupo;
        @endphp
        @endif
        <x-parametro-print :item="$p" />

        @endforeach
        @dump($procedimiento->resultado)
    </x-canva>



</x-app-layout>
<x-app-layout>
    <x-slot name="title">
        resultado_{{$procedimiento->orden->paciente->tipo_documento}}
    </x-slot>

    <article class="print_resultado relative max-w-6xl mx-auto sm:p-2 md:p-4 lg:p-6 print:!min-h-screen print:!text-xs print:!p-0 print:!bg-white print:!text-black">
        <section class="print_header p-4 flex w-full">
            <figure class="w-12 my-auto p-0">
                <x-application-logo />
            </figure>
            <div class="pl-4">
                <h1 class="font-bold"></h1>
                <h2 class="font-semibold">Hospital San Juan de Dios</h2>
                <p class="font-light">Colombia potencia de la vida</p>
                <p class="font-semibold">NIT: 800111456-5</p>
            </div>
        </section>

        <section class="print_paciente grid grid-cols-3 py-2 border-t-[0.2px] border-b-[0.2px] border-borders w-full">
            <div class="col-span-2 w-full grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">

                <span class="font-bold ">Paciente: </span>
                <h3 class="text-md">{{$procedimiento->orden->paciente->nombreCompleto()}}</h3>
                <span class="font-bold ">Identificación: </span>
                <h3>{{$procedimiento->orden->paciente->tipo_documento}}{{$procedimiento->orden->paciente->numero_documento}}</h3>


                    <div class="flex gap-2">
                        <span class="font-bold ">Sexo: </span>
                        <h3>{{$procedimiento->orden->paciente->sexo}} </h3>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold ">Edad: </span>
                        <h3>{{$procedimiento->orden->paciente->edad()}}</h3>
                    </div>


            </div>
            <div class="grid  gap-x-4 gap-y-0">
                <span class="font-bold ">Fecha de atención: </span>
                <h3>{{$procedimiento->orden->created_at}}</h3>
               <h3><span class="font-bold ">Órden Nº: </span>
                {{$procedimiento->orden->numero}}</h3>

            </div>
        </section>

        <section class="print_paramentros w-full">
            <h1 class="font-bold text-center my-4 uppercase">{{$procedimiento->examen->nombre}}</h1>
            <div class="header_resultado grid [grid-template-columns:minmax(max-content,2fr)_1fr_1fr_minmax(max-content,1fr)] gap-2 pl-4 mb-2 bg-secondary text-titles">
                <h2 class="font-semibold">Parametro</h2>
                <h2 class="font-semibold text-end">Resultado</h2>
                <h2 class="font-semibold">U. Medida</h2>
                <h2 class="font-semibold">Valor de Referencia</h2>
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


        </section>


        <footer class="print_footer hidden bottom-0 left-0 w-full m-auto items-center print:!block print:!absolute">
            <div class="font-light border-t border-borders text-center p-2">

                <spam class="">Teléfono: (123) 456-7890</spam>
                <spam class="">Dirección: Carrera 01 #56-13 </spam>
                <spam class="">Email: sanjuandedios@gobierno.gov.vo </spam>
            </div>
        </footer>
    </article>



</x-app-layout>

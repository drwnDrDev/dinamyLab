<x-app-layout>
    <x-slot name="title">
        resultado_{{$procedimiento->orden->paciente->tipo_documento}}
    </x-slot>

    <article class="print_resultado relative max-w-6xl mx-auto sm:p-2 md:p-4 lg:p-6 print:!text-xs print:!p-0 print:!bg-white print:!text-black">
        <section class="print_header hidden print:flex w-full">
            <figure class="w-12 mx-auto p-0">
                <x-application-logo />
            </figure>
            <div>
                <h1 class="font-bold"></h1>
                <h2 class="font-semibold">Hospital San Juan de Dios</h2>
                <p class="">Calle 123 # 456 - 789</p>
                <p class="">Teléfono: (123) 456-7890</p>
                <p class="">Email: </p>
            </div>
        </section>

        <section class="print_paciente grid grid-cols-2 py-4 border-t border-b border-borders w-full">
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">

                <span class="font-bold ">Paciente: </span>
                <h3>{{$procedimiento->orden->paciente->nombreCompleto()}}</h3>
                <span class="font-bold ">Identificación: </span>

                <div class="flex flex-wrap">
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
            @dump($procedimiento->resultado)
        </section>
        <footer class="print_footer sticky bottom-0 left-0 w-full print:!text-[0.6rem] items-center mt-4">
            <div class="">
                <p class="font-semibold">Fecha de impresión: {{ now()->format('d/m/Y H:i') }}</p>
                <p class="font-semibold">Usuario: {{ auth()->user()->name }}</p>
            </div>
            <div class="">
                <p class="font-semibold">Firma del médico:</p>
                <p class="border-b border-borders w-48 h-8"></p>
            </div>

        </footer>
    </article>



</x-app-layout>
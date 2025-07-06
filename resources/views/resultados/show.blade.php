<x-app-layout>
    <x-slot name="title">
        resultado_{{$procedimiento->orden->paciente->nombreCompleto()}}
    </x-slot>

    <article class="print_resultado relative max-w-6xl mx-auto sm:p-2 md:p-4 lg:p-6 print:!min-h-screen print:!text-xs print:!p-0 print:!bg-white print:!text-black">
        <section class="print_header p-4 flex w-full">
            <figure class="w-12 my-auto p-0">

                           @if (session('sede')->id === $procedimiento->sede->id)

                                 <img class="h-10 w-auto fill-current text-gray-800" src="{{ asset('storage/logos/'.$procedimiento->sede->logo) }}" alt="{{ session('sede')->nombre }}">


                           @else
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                           @endif()
            </figure>
            <div class="pl-4">

                <h2 class="font-semibold"></h2>
                <p class="font-light">{{$procedimiento->sede->nombre}}</p>
                <p class="font-semibold">{{$procedimiento->sede->empresa->nombre_comercial}}</p>
            </div>
        </section>

        <section class="print_paciente grid grid-cols-2 py-2 border-t-[0.2px] border-b-[0.2px] border-borders w-full">
            <div class="w-full grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">

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
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                <span class="font-bold ">Fecha de atención: </span>
                <h3>{{$procedimiento->orden->created_at->format('d-m-Y')}}</h3>
               <h3><span class="font-bold ">Órden Nº: </span>
                {{$procedimiento->orden->numero}}</h3>

            </div>
        </section>

        <section class="print_paramentros w-full">
            <h1 class="font-bold text-center my-4 uppercase">{{$procedimiento->examen->nombre}}</h1>
            <div class="header_resultado grid [grid-template-columns:minmax(25%,40%)_20%_20%_minmax(max-content,20%)] gap-2 pl-4 mb-2 bg-secondary text-titles">
                <h2 class="font-semibold">Parametro</h2>
                <h2 class="font-semibold text-end">Resultado</h2>
                <h2 class="font-semibold">U. Medida</h2>
                <h2 class="font-semibold">V. de Referencia</h2>
            </div>
            @php
            $lastGroup = null;
            @endphp

            @foreach ( $parametros as $p)

            @if ($p['grupo'] && $p['grupo'] !== $lastGroup)
            <h3 class="pt-2 pl-2 font-semibold uppercase col-span-full">{{ $p['grupo']}}</h3>
            @php
            $lastGroup = $p['grupo'];
            @endphp
            @endif
            <x-parametro-print :item="$p" />

            @endforeach



        </section>

            <aside class="firma  print:!block mt-4">

             <img src=" {{ asset('storage/firmas/'.$procedimiento->empleado->firma) }}" alt="{{$procedimiento->empleado->user->name}}" class="w-32 h-16">


            </aside>


        <footer class="print_footer hidden bottom-0 left-0 w-full m-auto items-center print:!block print:!absolute">
            <div class="font-light border-t border-borders text-center p-2">

                <spam class="">
                  <span class="text-sm font-semibold">☎</span>
                  @foreach ( $procedimiento->sede->telefonos as $telefono)
                    <span class="text-sm"> {{$telefono->numero}}</span>
                    @if (!$loop->last)
                    <span class="text-sm">- </span>
                    @endif

                  @endforeach
                </spam>
                <spam class="">Dirección: {{$procedimiento->sede->direccion->direccion}}</spam>
                @if ($procedimiento->sede->emails && count($procedimiento->sede->emails) > 0)
                    <spam class="">Email: {{$procedimiento->sede->emails->first()->email}} </spam>

                @elseif ($procedimiento->sede->empresa->emails && count($procedimiento->sede->empresa->emails) > 0)
                    <spam class="">Email: {{$procedimiento->sede->empresa->emails->first()->email}} </spam>
                @endif

            </div>
        </footer>
    </article>



</x-app-layout>


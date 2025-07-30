<x-app-layout>
     
    <article class="print_resultado relative max-w-6xl mx-auto sm:p-2 md:p-4 lg:p-6 print:!min-h-screen print:!text-xs print:!p-0 print:!bg-white print:!text-black">
        <section class="print_header hidden print:!flex pb-2  items-center w-full">
            <figure class="w-16 my-auto p-0">
            @if (session('sede')->id === $sede->id)
                <img class="fill-current object-contain text-gray-800" src="{{ asset('storage/logos/'.$sede->logo) }}" alt="{{ session('sede')->nombre }}">
            @else
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            @endif
                
            </figure>
            <div class="pl-4">
                <h2 class="font-semibold text-xl">{{$sede->empresa->nombre_comercial}}</h2>
                <p class="">{{$sede->nombre}}</p>
                <p class="font-light">NIT. {{$sede->empresa->nit}}</p>
            </div>
        </section>

        <section class="print_paciente grid grid-cols-2 py-2 border-t-[0.2px] border-b-[0.2px] border-borders w-full">
            <div class="col-span-2 flex gap-2">
                <span class="font-normal ">Paciente: </span>
                <h3 class="text-titles font-normal">{{$persona->nombreCompleto()}}</h3>
            </div>
            <div class="w-full grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">

                <span class="font-normal ">Identificación: </span>
                <h3 class="text-titles font-light">{{$persona->tipo_documento->cod_rips}}{{$persona->numero_documento}}</h3>
                <span class="font-normal ">Sexo: </span>
                <h3 class="text-titles font-light">{{$persona->sexo==="M" ? 'Masculino':'Femenino'}} </h3>
                <span class="font-normal ">Edad: </span>
                <h3 class="text-titles font-light">{{$persona->edad()}}</h3>



            </div>
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                <span class="font-normal ">Fecha de creación: </span>
                <h3 class="text-titles font-light">{{ now()->format('d-m-Y') }}</h3>
                <span>Fecha de Nacimiento: </span>
                <h3 class="text-titles font-light">{{$persona->fecha_nacimiento->format('d-m-Y')}}</h3>
                <span class="font-normal ">Total de Exámenes</span>
                <h3 class="text-titles font-light">{{$procedimientos->count()}}</h3>

            </div>
        </section>

        @foreach ($procedimientos as $procedimiento)
            <section class="print_paramentros w-full">
                <h1 class="font-semibold text-center my-4 uppercase">nombre de examen{{$procedimiento['procedimiento']->examen->nombre}}</h1>
                <div class="header_resultado grid [grid-template-columns:minmax(25%,40%)_20%_20%_minmax(max-content,20%)] gap-2 pl-4 mb-2 text-titles border-b border-t border-text">
                    <h2 class="font-semibold">Parametro</h2>
                    <h2 class="font-semibold text-end">Resultado</h2>
                    <h2 class="font-semibold">U. Medida</h2>
                    <h2 class="font-semibold">V. de Referencia</h2>
                </div>
                
            @php
            $lastGroup = null;
            @endphp

            @foreach ( $procedimiento['resultado'] as $p)

            @if ($p['grupo'] && $p['grupo'] !== $lastGroup)
            <h3 class="pt-2 pl-2 font-semibold uppercase col-span-full">{{ $p['grupo']}}</h3>
            @php
            $lastGroup = $p['grupo'];
            @endphp
            @endif
            <x-parametro-print :item="$p" />

            @endforeach

            </section>

                <div class="text-end text-xs font-light p-4 print:hidden">
                    <p class="text-xs font-light">Bacteriologo: {{$procedimiento['procedimiento']->empleado->user->name}} - {{$procedimiento['procedimiento']->empleado->numero_documento}}</p>

                </div>
                <aside class="firma hidden print:!flex justify-end h-full items-end p-4 gap-2">

                <div class="flex flex-col">
                    <img src=" {{ asset('storage/firmas/'.$procedimiento['procedimiento']->empleado->firma) }}" alt="{{$procedimiento['procedimiento']->empleado->user->name}}" class="w-32 object-contain">
                    <span class="text-xs font-light">Bacteriologo: {{$procedimiento['procedimiento']->empleado->user->name}} - {{$procedimiento['procedimiento']->empleado->numero_documento}}</span>

                </div>
            </aside>
            

        @endforeach

        <footer class="print_footer hidden bottom-0 left-0 w-full m-auto items-center print:!block print:!absolute">
            
            <div class="font-light border-t border-borders text-center p-2">

                <spam class="">
                    <span class="text-xs">Tels: </span>
                    @foreach ( $sede->telefonos as $telefono)
                    <span class="text-xs"> {{$telefono->numero}}</span>
                    @if (!$loop->last)
                    <span class="text-xs">- </span>
                    @endif

                    @endforeach
                </spam>
                <spam class="text-xs">Dirección: {{$sede->direccion->direccion}}</spam>
                @if ($sede->emails && count($sede->emails) > 0)
                <spam class="text-xs">Email: {{$sede->emails->first()->email}} </spam>

                @elseif ($sede->empresa->emails && count($sede->empresa->emails) > 0)
                <spam class="text-xs">Email: {{$sede->empresa->emails->first()->email}} </spam>
                @endif

            </div>
        </footer>
    </article>



    @dump($procedimientos)
    @dump($persona)

</x-app-layout>
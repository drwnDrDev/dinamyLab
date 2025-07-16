<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight print:hidden">
           {{ __('Medical order') }}
        </h2>
    </x-slot>
    <x-canva>

      <section class="p-4 flex flex-wrap justify-between w-full">
        <div class="flex-shrink-0">
            <div class="flex">
                <figure class="w-24 h-24 p-2">
                    <img class="aspect-square object-cover w-full h-full"
                    src="{{ asset('storage/logos/'.$orden->sede->logo) }}" alt="{{$orden->sede->nombre }}">
                </figure>
                <div class="flex flex-col justify-center py-2 ml-[-1px]">
                    <h2 class="font-semibold text-2xl">{{$orden->sede->empresa->nombre_comercial}}</h2>
                    <p class="font-light">{{$orden->sede->nombre}}</p>
                    <p class="font-semibold">NIT: {{$orden->sede->empresa->nit}}</p>
                </div>
            </div>
            <div class="pl-4">

            <p class="font-medium">{{$orden->sede->direccion->direccion}}-{{$orden->sede->direccion->municipio->municipio}}</p>
               <p class="font-light"> Telefono:
                @php
                    $telefonos = $orden->sede->telefonos->slice(0, 2);
                @endphp
                @foreach ($telefonos as $index => $telefono)
                    <span>{{ $telefono->numero }}</span>
                    @if ($index < $telefonos->count() - 1)
                        <span> | </span>
                    @endif
                @endforeach
                </p>
            </div>

        </div>

            <div class="flex flex-col  gap-x-4 gap-y-0">
                <span class="font-semibold">Fecha de atención </span>
                <h3 class="mb-2">{{$orden->created_at->format('d-m-Y')}}</h3>
               <h3><strong>Órden Nº: </strong>
                {{$orden->numero}}</h3>

            </div>

        </section>

        <section class="w-full">
            <div class="w-full flex flex-wrap gap-2">
                <div class="w-full flex gap-2">
                <span class="font-bold ">Paciente: </span>
                <h3 class="text-md">{{$orden->paciente->nombreCompleto()}}</h3>
                </div>
                <div class="w-full flex gap-2">

                <span class="font-bold ">Identificación: </span>
                <h3>{{$orden->paciente->tipo_documento->cod_rips}}{{$orden->paciente->numero_documento}}</h3>
                </div>
            </div>
            <div class="w-full flex flex-wrap gap-2 print:hidden">
                    <div class="flex gap-2">
                        <span class="font-bold ">Sexo: </span>
                        <h3>{{$orden->paciente->sexo}} </h3>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold ">Edad: </span>
                        <h3>{{$orden->paciente->edad()}}</h3>
                    </div>

                    <div class="flex gap-2">
                        <span class="font-bold ">Teléfono: </span>
                        <h3>
                            @php
                                $telefonos = $orden->paciente->telefonos->slice(0, 2);
                            @endphp
                            @foreach ($telefonos as $index => $telefono)
                                <span>{{ $telefono->numero }}</span>
                                @if ($index < $telefonos->count() - 1)
                                    <span> | </span>
                                @endif
                            @endforeach
                        </h3>
                    </div>

        </section>

        <h2 class="font-bold mb-4 text-xl text-titles">Datos de la orden</h2>
        <input type="hidden" id="orden_id" name="orden_id" value="{{ $orden->id }}">
        <div class="row-inputs w-full grid grid-cols-3 justify-around gap-2">
            <div>
                <x-input-label for="numero">Número de orden</x-input-label>
                <x-text-input type="text" id="numero" name="numero" value="{{ $orden->numero }}" readonly />
            </div>

            <div class="w-full">
                <x-input-label for="fecha">Fecha de emisión</x-input-label>
                <x-text-input type="date" id="fecha" name="fecha" value="{{ $orden->created_at->format('Y-m-d') }}" readonly />
            </div>

        </div>
    <div class="container" id="ticket">
        <div class="grid grid-cols-5 border border-borders rounded-md">

                <p class="text-titles">Paciente</p>
                <p class="text-text">{{$orden->paciente->nombres()}} {{$orden->paciente->apellidos()}}</p>
                <p class="text-titles">Identificacion</p>
                <p class="text-text">{{$orden->paciente->tipo_documento->cod_rips}} {{$orden->paciente->numero_documento}}</p>



            <p class="text-titles">#{{$orden->numero}} Inicio de Procedimiento {{$orden->created_at->format('d-m-Y') }}</p>

        </div>


        <div class="grid grid-cols-5 p-2 justify-center border border-borders">
           <p class="col-span-2">{{$orden->updated_at}} </p>
            <div class="col-span-4 grid grid-cols-4">


                @foreach ($orden->examenes as $examen)
                <p>{{$examen->nombre}}</p>
                <p>{{$examen->pivot->cantidad}}</p>
                <p>{{$examen->valor}}</p>
                <p>{{$examen->valor*$examen->pivot->cantidad}}</p>

               @endforeach
            </div>
            <div class="col-span-4 grid grid-cols-3 justify-end">
                <p class="text-titles">Total</p>
                <p class="text-text">{{$orden->total}}</p>
                <p class="text-text">IVA {{$orden->iva}}%</p>
            </div>

        </div>

    </div>
    <x-primary-button type="button" class="mt-4 print:hidden" id="imprimir" >
        {{ __('Print') }}
    </x-primary-button>

    </x-canva>
    @vite(['resources/js/ticket.js'])
</x-app-layout>

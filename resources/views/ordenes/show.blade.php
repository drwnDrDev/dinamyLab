<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight print:hidden">
           {{ __('Medical order') }}
        </h2>
    </x-slot>
    <x-canva>

      <section class="print_header p-4 flex w-full">
            <figure class="w-24 my-auto p-0">
                <img class="h-full w-auto fill-current text-gray-800"
                 src="{{ asset('storage/logos/'.$orden->sede->logo) }}" alt="{{ session('sede')->nombre }}">
            </figure>
            <div class="pl-4">

                <h2 class="font-semibold text-3xl">{{$orden->sede->empresa->nombre_comercial}}</h2>
                <p class="font-light">{{$orden->sede->nombre}}</p>
                <p class="font-semibold">{{$orden->sede->empresa->nit}}</p>
                <p class="font-semibold">{{$orden->sede->direccion->direccion}}-{{$orden->sede->direccion->municipio->municipio}}</p>
                @foreach ($orden->sede->telefonos as $telefono)
                    <p class="font-light">Telefono: {{$telefono->numero}}</p>
                @endforeach

            </div>
        </section>

        <section class="print_paciente grid grid-cols-2 py-2 border-t-[0.2px] border-b-[0.2px] border-borders w-full">
            <div class="w-full grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">

                <span class="font-bold ">Paciente: </span>
                <h3 class="text-md">{{$orden->paciente->nombreCompleto()}}</h3>
                <span class="font-bold ">Identificación: </span>
                <h3>{{$orden->paciente->tipo_documento->cod_rips}}{{$orden->paciente->numero_documento}}</h3>


                    <div class="flex gap-2">
                        <span class="font-bold ">Sexo: </span>
                        <h3>{{$orden->paciente->sexo}} </h3>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold ">Edad: </span>
                        <h3>{{$orden->paciente->edad()}}</h3>
                    </div>


            </div>
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                <span class="font-bold ">Fecha de atención: </span>
                <h3>{{$orden->created_at->format('d-m-Y')}}</h3>
               <h3><span class="font-bold ">Órden Nº: </span>
                {{$orden->numero}}</h3>

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

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight print:hidden">
           {{ __('Medical order') }}
        </h2>
    </x-slot>


    <x-canva>
    <section class="print:hidden">

        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Nº: {{ $orden->numero }}</span>
                <span class="text-sm text-gray-600">Fecha: {{ $orden->created_at->format('d-m-Y') }}</span>
            </div>
        </div>

    <x-primary-button type="button" class="mt-4 print:hidden" id="printButton" >
        {{ __('Print') }}
    </x-primary-button>

</section>

    </x-canva>

    <section class="hidden print:flex print:flex-wrap print:justify-between print:w-full print:mt-[-20px]">
        <div class="flex-col justify-center items-center gap-2">
            <div class="flex">
                <figure class="w-20 h-20 p-1">
                    <img class="aspect-square object-cover w-full h-full"
                    src="{{ asset('storage/logos/'.$orden->sede->logo) }}" alt="{{$orden->sede->nombre }}">
                </figure>
                <div class="flex flex-col justify-center  ml-[-1px]">
                    <h2 class="font-semibold text-2xl">{{$orden->sede->empresa->nombre_comercial}}</h2>
                    <p class="font-light text-sm">{{$orden->sede->nombre}}</p>
                    <p class="font-semibold text-xs">NIT: {{$orden->sede->empresa->nit}}</p>
                </div>
            </div>
            <div class="px-4">

            <p class="font-medium text-xs">{{$orden->sede->direccion->direccion}}-{{$orden->sede->direccion->municipio->municipio}}</p>
               <p class="font-light text-sm"> Telefono:
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

        <section class="w-full border border-borders rounded-md p-3">
            <div class="w-full flex flex-wrap gap-2">
                <div class="w-full flex gap-2">
                <span class="font-bold ">Paciente: </span>
                <h3 class="text-md p-0 mb-0">{{$orden->paciente->nombreCompleto()}}</h3>
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
            </div>

                    <div class="flex gap-2">
                        <span class="font-bold ">Teléfono: </span>
                        <h3>
                            @if($orden->paciente->telefonos->count() > 0)
                                {{$orden->paciente->telefonos->first()->numero}}
                            @else
                                No registrado
                            @endif
                        </h3>
                    </div>

        </section>

    <div class="container" id="ticket">

        <div class="grid grid-cols-6 border border-borders rounded-md">
            <p>Examen</p>
            <p class="col-span-2 text-end pr-2">Cantidad</p>
            <p class="text-end">Valor</p>
            <p class="col-span-2 text-end pr-2">Total</p>
        </div>
        <div class="grid grid-cols-6 border border-borders rounded-md">
                @foreach ($orden->examenes as $examen)
                <p class="col-span-2">{{$examen->nombre}}</p>
                <p class="text-end px-2">{{$examen->pivot->cantidad}}</p>
                <p class="text-end">{{number_format($examen->valor)}}</p>
                <p class="col-span-2 text-end px-2">{{number_format($examen->valor*$examen->pivot->cantidad, 2)}}</p>
               @endforeach
        </div>
            <div class="grid grid-cols-5 justify-between items-center p-2 gap-2">
                <p class="text-end col-span-3">Subtotal</p>
                <p class="text-end col-span-2">{{number_format($orden->total,2)}}</p>
                @if ($orden->descuento && $orden->descuento > 0)
                    <p class="col-span-3 text-end">Descuento </p>
                    <p class="text-end col-span-2">${{$orden->descuento ?? 0}}</p>
                @endif

                 @if($orden->total != $orden->abono)
                    <p class="col-span-3 text-end">Saldo</p>
                    <p class="text-end col-span-2">{{$orden->total - $orden->abono}}</p>
                 @endif
                <p class="col-span-3 font-semibold text-end">Total</p>
                <p class="font-semibold text-end col-span-2">${{ number_format(($orden->total - $orden->descuento), 2) }} COP</p>
                <p class="text-black/75 col-span-5 text-end">**IVA**: $0 (Exento según Art. 476 ET)  </p>
            </div>

        </div>


    </div>

    @vite(['resources/js/ticket.js'])
</x-app-layout>

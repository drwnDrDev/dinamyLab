<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-text leading-tight">
            {{ __('Orden Medica') }}
        </h2>
    </x-slot>

    <x-canva class="print:hidden">
        <div class="py-4 flex justify-between items-center">
            <div>
                <p class="text-2xl font-bold leading-tight tracking-[-0.015em]">Numero de Orden: {{$orden->numero}}</p>
                <p class="text-titles">
                    <button
                        class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-8 px-4 bg-[#e7f2f3] font-medium leading-normal w-full {{ $orden->terminada==null ? 'bg-rose-200 text-rose-600' : 'bg-secondary text-primary' }}">
                        <span class="truncate">{{ $orden->terminada==null ? 'Pendiente':'Completada' }}</span>
                    </button>
                </p>
            </div>
            <div class="flex gap-2">

                <a href="{{route('ordenes.create',$orden->paciente->id)}}">
                    <x-primary-button>Nueva Orden</x-primary-button>
                </a>

                <x-secondary-button onclick="window.print()">{{__('Ticket')}}</x-secondary-button>
            </div>
        </div>

        <section class="grid grid-cols-2 py-2 border-t-[0.2px] border-b-[0.2px] border-borders w-full print:hidden">
            <div class="col-span-2 flex gap-2">
                <span class="font-normal ">Paciente: </span>
                <h3 class="text-titles font-normal">{{$orden->paciente->nombreCompleto()}}</h3>
            </div>
            <div class="w-full grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">

                <span class="font-normal ">Identificación: </span>
                <h3 class="text-titles font-light">{{$orden->paciente->tipo_documento->cod_rips}}{{$orden->paciente->numero_documento}}</h3>
                <span class="font-normal ">Sexo: </span>
                <h3 class="text-titles font-light">{{$orden->paciente->sexo==="M" ? 'Masculino':'Femenino'}} </h3>
                <span class="font-normal ">Edad: </span>
                <h3 class="text-titles font-light">{{$orden->paciente->edad()}}</h3>
            </div>
            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                <span class="font-normal ">Fecha de atención: </span>
                <h3 class="text-titles font-light">{{$orden->created_at->format('d-m-Y')}}</h3>
                <span>Fecha de Nacimiento: </span>
                <h3 class="text-titles font-light">{{$orden->paciente->fecha_nacimiento->format('d-m-Y')}}</h3>
                <span class="font-normal ">Órden Nº: </span>
                <h3 class="text-titles font-light">{{$orden->numero}}</h3>
            </div>
        </section>

        <div class="py-4" id="historia">
            <div class="flex overflow-hidden rounded-xl border border-borders">
                <table class="flex-1">
                    <thead>
                        <tr class="">
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Date')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Procedimiento')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Test')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Status')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orden->procedimientos as $procedimiento)

                        <tr class="border-t border-borders hover:bg-secondary">
                            <td class="px-3 py-4"><span class="w-40">{{ $procedimiento->fecha}}</span></td>
                            <td class="px-3 py-4"><span class="w-40">{{ $procedimiento->orden_id }}</span></td>
                            <td class="px-3 py-4"><span class="w-60">{{ $procedimiento->examen->nombre }}</span></td>
                            <td class="px-3 py-4">
                                <a href="{{ route('resultados.show', $procedimiento) }}">
                                    <span class="w-40 text-titles">{{ $procedimiento->estado }}</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <section class="mx-auto print:!block bg-white">
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
                <p class="text-black/75 col-span-5 text-end">**IVA**: $0 (Exento según Art. 476 ET) </p>
            </div>

        </section>

    </x-canva>

    <section class="Ticket hidden print:flex print:flex-wrap print:justify-between print:w-full print:bg-white">
        <div class="flex-col justify-center items-center gap-2">
            <div class="flex justify-center items-center">
                <figure class="w-20 h-20 p-1">
                    <img class="aspect-square object-cover w-full h-full rounded-md"
                        src="{{ asset('storage/logos/'.$orden->sede->logo) }}" alt="{{$orden->sede->nombre }}">
                </figure>
                <div class="flex flex-col justify-center">
                    <h2 class="font-semibold text-2xl">{{$orden->sede->empresa->nombre_comercial}}</h2>
                    <p class="font-light text-sm">{{$orden->sede->nombre}}</p>
                    <p class="font-semibold text-xs">NIT: {{$orden->sede->empresa->nit}}</p>
                </div>
            </div>
            <div class="px-4">

                <p class="font-medium text-xs">{{$orden->sede->direccion->direccion}}-{{$orden->sede->direccion->municipio->municipio}}</p>
                <p class="font-light text-sm"> Telefonos:
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

        <div class="flex flex-col justify-center items-center gap-2 p-1 ">
            <div class="flex flex-col text-center">
                <span class="font-semibold">Fecha de atención </span>
                <h3 class="mb-2 text-centrer">{{$orden->created_at->translatedFormat('d \de F Y')}}</h3>
            </div>

            <h3><strong>Órden Nº: </strong>
                {{$orden->numero}}
            </h3>

        </div>
        <div>
            <table>
                <thead>
                    <tr class="border-t border-text">
                        <th class="px-1 py-1 text-left text-text w-40 text-sm font-medium leading-normal">Examen</th>
                        <th class="px-1 py-1 text-left text-text w-32 text-sm font-medium leading-normal">Cantidad</th>
                        <th class="px-1 py-1 text-left text-text w-60 text-sm font-medium leading-normal">Valor</th>
                        <th class="px-1 py-1 text-left text-text w-60 text-sm font-medium leading-normal">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orden->examenes as $examen)
                    <tr class="border-t border-borders">
                        <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                            {{ $examen->nombre }}
                        </td>
                        <td class="content-start px-4 py-2 w-32 text-titles text-sm font-normal leading-normal">
                            {{ $examen->pivot->cantidad }}
                        </td>
                        <td class="content-start px-4 py-2 w-60 text-titles text-sm font-normal leading-normal">
                            {{ number_format($examen->valor) }}
                        </td>
                        <td class="content-start px-4 py-2 w-60 text-titles text-sm font-normal leading-normal">
                            {{ number_format($examen->valor*$examen->pivot->cantidad, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

</x-app-layout>
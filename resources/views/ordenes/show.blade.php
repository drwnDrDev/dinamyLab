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

        <div class="py-2">
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
                            <td class="px-3 py-4"><span class="w-40">{{ $procedimiento->fecha->format('d-m-Y H:i')   }}</span></td>
                            <td class="px-3 py-4"><span class="w-40">{{ $procedimiento->id }}</span></td>
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

        <div class="flex w-full gap-2 py-2">
            <div class="p-4 w-full overflow-hidden rounded-xl border border-borders">
                <p class="font-semibold">Observaciones:</p>
                <p class="font-light">{{ $orden->observaciones }}hola como estas</p>

            </div>
            <div class="p-4 flex justify-center gap-2 items-center w-full overflow-hidden rounded-xl border border-borders">
                <div class="flex flex-col justify-center items-end">
                    <p class="font-semibold">Abono Total:</p>
                    <p class="font-semibold"0>Saldo:</p>
                    <p class="font-semibold text-2xl">Total:</p>
                </div>
                <div class="flex flex-col">
                    <p class="font-semibold">${{ number_format($orden->total - $orden->descuento, 2) }}</p>
                    <p class="font-semibold">${{ number_format($orden->total - $orden->descuento - $orden->abono, 2) }}</p>
                    <p class="font-semibold text-2xl">${{ number_format($orden->total, 2) }}</p>
                </div>
            </div>
        </div>

    </x-canva>
    <section class="Ticket hidden text-xs px-2 py-6 m-auto print:flex print:flex-wrap print:justify-between print:w-[80mm] print:bg-white">
        <div class="w-full flex-col justify-center items-center gap-2 mb-4">
            <div class="flex justify-center items-center">
                <figure class="w-20 h-20 p-1">
                    <img class="aspect-square object-cover w-full h-full rounded-md"
                        src="{{ asset('storage/logos/'.$orden->sede->logo) }}" alt="{{$orden->sede->nombre }}">
                </figure>
                <div class="flex flex-col justify-center">
                    <h2 class="font-semibold text-xl">{{$orden->sede->empresa->nombre_comercial}}</h2>
                    <p class="font-light">{{$orden->sede->nombre}}</p>
                    <p class="font-semibold">NIT: {{$orden->sede->empresa->nit}}</p>
                </div>
            </div>
            <div class="px-4 text-center">
                <p class="font-medium">{{$orden->sede->direccion->direccion}}</p>
                <p class="font-light"> Tels:
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
                <p>
                    {{$orden->sede->direccion->municipio->municipio}}
                </p>
            </div>
        </div>

        <div class="flex w-full justify-between items-center gap-2">
            <div>
                <h3><span class="font-normal">Fecha: </span>
                    {{$orden->created_at->translatedFormat('d/m/Y')}}
                </h3>
            </div>

            <div class="">
                <h3><span class="font-semibold">Órden Nº: </span>
                    {{$orden->numero}}
                </h3>
            </div>
        </div>
        <div>
            <p class="font-light"><span class="font-normal">Paciente: </span>
                {{$orden->paciente->nombreCompleto()}}
            </p>
            <p class="font-light"><span class="font-normal">Identificación: </span>
                {{$orden->paciente->tipo_documento->cod_rips}}{{$orden->paciente->numero_documento}}
            </p>
            <p class="font-light"><span class="font-normal">Tels: </span>
                @if($orden->paciente->telefonos->isEmpty())
                Sin Registro
                @else
                @foreach($orden->paciente->telefonos as $telefono)
                <span>{{ $telefono->numero}}</span>
                @endforeach
                @endif
            </p>
            <br>
        </div>
        <div>
            <table>
                <thead>
                    <tr class="border-t border-text">
                        <th class="p1 text-left text-text w-60 text-sm font-normal leading-normal">Examen</th>
                        <th class="p1 text-left text-text w-16 text-sm font-normal leading-normal">Cant.</th>
                        <th class="p1 text-left text-text w-40 text-sm font-normal leading-normal">Vlr</th>
                        <th class="p1 text-left text-text w-40 text-sm font-normal leading-normal">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orden->examenes as $examen)
                    <tr>
                        <td class="content-start p1 w-60 text-xs font-light leading-normal">
                            {{ $examen->nombre }}
                        </td>
                        <td class="content-start p1 w-16 text-xs font-light leading-normal">
                            {{ $examen->pivot->cantidad }}
                        </td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            {{ number_format($examen->valor) }}
                        </td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            {{ number_format($examen->valor*$examen->pivot->cantidad, 2) }}
                        </td>
                    </tr>
                    @endforeach
                    @if ($orden->descuento && $orden->descuento > 0)
                    <tr>
                        <td colspan="3" class="text-end font-semibold p-1 pl-4">Descuento</td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            ${{ number_format($orden->descuento, 2) }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="text-end font-semibold p-1 pl-4">Abono</td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            ${{ number_format($orden->abono, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end font-semibold p-1 pl-4">Saldo</td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            ${{ number_format(($orden->total - $orden->descuento - $orden->abono), 2) }}
                        </td>
                    </tr>
                    <tr class="border-t border-text">
                        <td colspan="3" class="text-end font-semibold p-1 pl-4">Total</td>
                        <td class="content-start p1 w-40 text-xs font-light leading-normal">
                            ${{ number_format(($orden->total - $orden->descuento), 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="w-full flex flex-col justify-center items-center mt-4">
            <p class="text-center uppercase">Este es un recibo de una orden de examenes. No es valido como factura.</p>
            <p class="text-center">¡Presenta este recibo para reclamar los resultados!</p>
    </section>
</x-app-layout>

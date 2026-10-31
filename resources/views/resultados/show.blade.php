<x-app-layout>
    <x-slot name="title">
        resultado_{{$procedimiento->orden->paciente->nombreCompleto()}}
    </x-slot>

    <article class="print_resultado relative max-w-6xl mx-auto sm:p-2 md:p-4 lg:p-6 print:!min-h-screen print:!text-xs print:!p-0 print:!bg-white print:!text-black">
        <table class="resultados-print-table w-full table-fixed border-collapse">
            <colgroup>
                <col class="w-2/5">
                <col class="w-1/5">
                <col class="w-1/5">
                <col class="w-1/5">
            </colgroup>
            <thead>
                <tr>
                    <th colspan="4" class="p-0 text-left font-normal">
                        <section class="print_header hidden print:!flex pb-2 items-center w-full">
                            <figure class="w-16 my-auto p-0">
                                @if (session('sede')->id === $procedimiento->sede->id)
                                    <img class="fill-current object-contain text-gray-800" src="{{ asset('storage/logos/'.$procedimiento->sede->logo) }}" alt="{{ session('sede')->nombre }}">
                                @else
                                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                                @endif
                            </figure>
                            <div class="pl-4">
                                <h2 class="font-semibold text-xl">{{$procedimiento->sede->empresa->nombre_comercial}}</h2>
                                <p>{{$procedimiento->sede->nombre}}</p>
                                <p class="font-light">NIT. {{$procedimiento->sede->empresa->nit}}</p>
                            </div>
                        </section>

                        <section class="print_paciente grid grid-cols-2 py-2 border-t-[0.2px] border-b-[0.2px] border-borders w-full">
                            <div class="col-span-2 flex gap-2">
                                <span class="font-normal">Paciente:</span>
                                <h3 class="text-titles font-normal">{{$procedimiento->orden->paciente->nombreCompleto()}}</h3>
                            </div>
                            <div class="w-full grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                                <span class="font-normal">Identificación:</span>
                                <h3 class="text-titles font-light">{{$procedimiento->orden->paciente->tipo_documento->cod_rips}}{{$procedimiento->orden->paciente->numero_documento}}</h3>
                                <span class="font-normal">Sexo:</span>
                                <h3 class="text-titles font-light">{{$procedimiento->orden->paciente->sexo === 'M' ? 'Masculino' : 'Femenino'}}</h3>
                                <span class="font-normal">Edad:</span>
                                <h3 class="text-titles font-light">{{$procedimiento->orden->paciente->edad()}}</h3>
                            </div>
                            <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                                <span class="font-normal">Fecha de atención:</span>
                                <h3 class="text-titles font-light">{{$procedimiento->orden->created_at->format('d-m-Y')}}</h3>
                                <span>Fecha de Nacimiento:</span>
                                <h3 class="text-titles font-light">{{$procedimiento->orden->paciente->fecha_nacimiento->format('d-m-Y')}}</h3>
                                <span class="font-normal">Orden Nº:</span>
                                <h3 class="text-titles font-light">{{$procedimiento->orden->numero}}</h3>
                            </div>
                        </section>

                        <h1 class="font-semibold text-center my-4 uppercase">{{$procedimiento->examen->nombre}}</h1>
                    </th>
                </tr>
                <tr class="header_resultado text-titles border-b border-t border-text">
                    <th class="py-2 pl-2 text-left font-semibold">Parámetro</th>
                    <th class="py-2 text-right font-semibold">Resultado</th>
                    <th class="py-2 text-left font-semibold">U. Medida</th>
                    <th class="py-2 text-left font-semibold">V. de Referencia</th>
                </tr>
            </thead>
            <tbody class="print_paramentros">
            @php
            $lastGroup = null;
            @endphp

            @foreach ( $parametros as $p)

            @if ($p['grupo'] && $p['grupo'] !== $lastGroup)
            <tr class="break-inside-avoid">
                <th colspan="4" class="pt-2 pl-2 text-left font-semibold uppercase">{{ $p['grupo']}}</th>
            </tr>
            @php
            $lastGroup = $p['grupo'];
            @endphp
            @endif
            <tr class="break-inside-avoid border-b-[0.2px] border-dashed border-secondary">
                <x-parametro-print-table :item="$p" />
            </tr>

            @endforeach
            </tbody>
        </table>

        <div class="text-end text-xs font-light p-4 print:hidden">
            <p class="text-xs font-light">Bacteriologo: {{$procedimiento->empleado->user->name}} - {{$procedimiento->empleado->numero_documento}}</p>

        </div>

        <footer class="print_footer hidden bottom-0 left-0 w-full m-auto items-center print:!block print:!absolute">
            <aside class="firma flex justify-end h-full items-end p-4 gap-2">

                <div class="flex flex-col">
                    <img src=" {{ asset('storage/firmas/'.$procedimiento->empleado->firma) }}" alt="{{$procedimiento->empleado->user->name}}" class="w-32 object-contain">
                    <span class="text-xs font-light">Bacteriologo: {{$procedimiento->empleado->user->name}} - {{$procedimiento->empleado->numero_documento}}</span>

                </div>
            </aside>
            <div class="font-light border-t border-borders text-center p-2">

                <spam class="">
                    <span class="text-xs">Tels: {{$procedimiento->sede->telefonos->first()->numero}} </span>

                </spam>
                <spam class="text-xs">Dirección: {{$procedimiento->sede->direccion->direccion}}</spam>
             </div>
        </footer>
    </article>

    <section class="print:hidden max-w-6xl mx-auto p-4 md:p-6">
        <h2 class="mb-3 text-lg font-semibold text-titles dark:text-white">Historial de modificaciones</h2>
        @if ($cambios->isEmpty())
            <p class="rounded-md border border-borders bg-background p-4 text-sm text-text dark:text-gray-300">
                Este resultado no tiene modificaciones registradas.
            </p>
        @else
            <div class="overflow-x-auto rounded-lg border border-borders">
                <table class="w-full border-collapse text-sm">
                    <thead class="bg-secondary text-left text-titles dark:bg-slate-800 dark:text-white">
                        <tr>
                            <th class="px-3 py-2">Fecha</th>
                            <th class="px-3 py-2">Parámetro</th>
                            <th class="px-3 py-2">Valor anterior</th>
                            <th class="px-3 py-2">Valor nuevo</th>
                            <th class="px-3 py-2">Modificado por</th>
                            <th class="px-3 py-2">Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cambios as $cambio)
                            <tr class="border-t border-borders align-top text-text dark:text-gray-200">
                                <td class="whitespace-nowrap px-3 py-2">{{ $cambio->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-3 py-2">{{ $cambio->parametro_nombre }}</td>
                                <td class="px-3 py-2">{{ $cambio->valor_anterior ?? 'Vacío' }}</td>
                                <td class="px-3 py-2">{{ $cambio->valor_nuevo ?? 'Vacío' }}</td>
                                <td class="px-3 py-2">{{ $cambio->usuario_nombre }}</td>
                                <td class="px-3 py-2">{{ $cambio->observacion }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>



</x-app-layout>

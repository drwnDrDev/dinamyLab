<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Reporte para {{ $persona->nombreCompleto() }}</title>

    <style>
        @media print {

            html,
            body {
                height: auto !important;
                overflow: visible !important;
                margin: 0;
                padding: 0;
            }


            .page-break {
                page-break-after: always;
                break-after: always;
            }

            .no-break {
                page-break-inside: avoid;
                break-inside: avoid-page;
            }

            header,
            footer {
                page-break-inside: avoid;
            }
            .print\:table-header-group { display: table-header-group; }
            .print\:table-row-group { display: table-row-group; }
            .print\:table-footer-group { display: table-footer-group; }

        }
    </style>
</head>

<body>
    <article class="print_resultado relative max-w-6xl mx-auto sm:p-2 md:p-4 lg:p-6 print:!min-h-screen print:!text-sm print:!p-0 print:!bg-white print:!text-black">
        <table class="w-full ">
            <thead class="print:table-header-group">
                <tr>
                    <th colspan="4">
                        <header class="print_header m-auto mb-4 items-center">
                            <div class="print_logo flex pb-2  items-center w-full">
                                <figure class="w-16 my-auto p-0">
                                    @if (session('sede')->id === $sede->id)
                                    <img class="fill-current object-contain text-gray-800" src="{{ asset('storage/logos/'.$sede->logo) }}" alt="{{ session('sede')->nombre }}">
                                    @else
                                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                                    @endif

                                </figure>
                                <div class="pl-4 text-start">
                                    <h2 class="font-semibold text-xl">{{$sede->empresa->nombre_comercial}}</h2>
                                    <p class="">{{$sede->nombre}}</p>
                                    <p class="font-light">NIT. {{$sede->empresa->nit}}</p>
                                </div>
                            </div>

                            <div class="print_paciente grid grid-cols-2 py-2 border-t-[0.2px] border-b-[0.2px] border-borders w-full">
                                <div class="col-span-2 flex gap-2">
                                    <span class="font-normal ">Paciente: </span>
                                    <h3 class="text-titles font-normal">{{$persona->nombreCompleto()}}</h3>
                                </div>
                                <div class="w-full grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">

                                    <span class="font-normal text-start">Identificación: </span>
                                    <h3 class="text-titles font-light text-start">{{$persona->tipo_documento->cod_rips}}{{$persona->numero_documento}}</h3>
                                    <span class="font-normal text-start">Sexo: </span>
                                    <h3 class="text-titles font-light text-start">{{$persona->sexo==="M" ? 'Masculino':'Femenino'}} </h3>
                                    <span class="font-normal text-start">Edad: </span>
                                    <h3 class="text-titles font-light text-start">{{$persona->edad()}}</h3>

                                </div>
                                <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                                    <span class="font-normal text-start">Fecha de creación: </span>
                                    <h3 class="text-titles font-light text-start">{{ now()->format('d-m-Y') }}</h3>
                                    <span class="font-normal text-start">Fecha de Nacimiento: </span>
                                    <h3 class="text-titles font-light text-start">{{$persona->fecha_nacimiento->format('d-m-Y')}}</h3>
                                    <span class="font-normal text-start">Total de Exámenes</span>
                                    <h3 class="text-titles font-light text-start">{{$procedimientos->count()}}</h3>

                                </div>
                            </div>
                            <div class="header_resultado mt-4 mb-0 grid [grid-template-columns:minmax(25%,40%)_20%_20%_minmax(max-content,20%)] gap-2 pl-4 text-titles border-b border-t border-text">
                                    <h2 class="font-semibold">Parametro</h2>
                                    <h2 class="font-semibold text-end">Resultado</h2>
                                    <h2 class="font-semibold">U. Medida</h2>
                                    <h2 class="font-semibold">V. de Referencia</h2>
                            </div>
                        </header>
                    </th>
                </tr>
            </thead>
            <tbody class="print:table-row-group">
                <tr>
                    <td colspan="4">
                        <main class="print_paramentros w-full mb-9">
                            @foreach ($procedimientos as $procedimiento)
                            <div class="no-break">
                                <section>
                                    <h1 class="font-semibold text-center uppercase">{{$procedimiento['procedimiento']->examen->nombre}}</h1>
                                    <p class="font-light text-center mb-4">Fecha de Validación: {{ $procedimiento['procedimiento']->updated_at->format('d-m-Y h:m') }}</p>
                                
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
                            </div>
                            @endforeach

                        </main>
                    </td>
                </tr>
            </tbody>
            <tfoot class="print:table-footer-group">
                <tr>
                    <td colspan="4">
                        <footer class="print_footer hidden w-full m-auto items-center print:!block print:!fixed bottom-0 left-0 right-0">

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
                    </td>
                </tr>
            </tfoot>
        </table>
    </article>
</body>

</html>
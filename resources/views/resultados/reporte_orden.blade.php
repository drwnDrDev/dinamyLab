<x-app-layout>
    <x-slot name="title">
        Resultados de orden {{ $orden->numero }}
    </x-slot>

    <style>
        @media print {
            @page {
                margin: 14mm 12mm 18mm;
                @bottom-right {
                    content: "Página " counter(page) " de " counter(pages);
                    font-size: 9pt;
                }
            }

            .reporte-procedimiento + .reporte-procedimiento {
                break-before: page;
            }

            .reporte-procedimiento tr,
            .reporte-procedimiento td,
            .reporte-procedimiento th {
                break-inside: avoid;
            }
        }
    </style>

    <div class="mx-auto flex max-w-6xl justify-between gap-3 px-4 pt-4 print:hidden">
        <a href="{{ route('ordenes.show', $orden) }}" class="rounded-md border border-borders px-4 py-2 text-text">
            Volver a la orden
        </a>
        <button type="button" onclick="window.print()" class="rounded-md bg-primary px-4 py-2 text-white">
            Imprimir resultados
        </button>
        <a href="{{ route('resultados.reporte_pdf', $orden) }}" class="rounded-md border border-primary px-4 py-2 text-primary">
            Descargar PDF
        </a>
    </div>

    <main class="mx-auto max-w-6xl bg-white p-4 text-black print:max-w-none print:p-0">
        @if ($procedimientos->isEmpty())
            <p class="rounded-md border border-borders p-4 text-center">
                Esta orden aún no tiene exámenes terminados con resultados para imprimir.
            </p>
        @endif

        @foreach ($procedimientos as $reporte)
            @php
                $procedimiento = $reporte['procedimiento'];
                $paciente = $orden->paciente;
                $lastGroup = null;
            @endphp
            <article class="reporte-procedimiento mb-8 print:mb-0">
                <table class="w-full table-fixed border-collapse text-sm">
                    <colgroup>
                        <col class="w-2/5">
                        <col class="w-1/5">
                        <col class="w-1/5">
                        <col class="w-1/5">
                    </colgroup>
                    <thead>
                        <tr>
                            <th colspan="4" class="pb-3 text-left font-normal">
                                <div class="mb-3 flex items-center gap-3 border-b border-gray-300 pb-3">
                                    <figure class="w-16 shrink-0">
                                        @if ($orden->sede->logo)
                                            <img src="{{ asset('storage/logos/'.$orden->sede->logo) }}" alt="{{ $orden->sede->nombre }}" class="max-h-16 object-contain">
                                        @else
                                            <x-application-logo class="block h-10 w-auto fill-current text-gray-800" />
                                        @endif
                                    </figure>
                                    <div>
                                        <h2 class="text-lg font-semibold">{{ $orden->sede->empresa->razon_social }}</h2>
                                        <p>{{ $orden->sede->nombre }} · NIT {{ $orden->sede->empresa->nit }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-x-6 gap-y-1 border-y border-gray-300 py-2">
                                    <p class="col-span-2"><strong>Paciente:</strong> {{ $paciente->nombreCompleto() }}</p>
                                    <p><strong>Identificación:</strong> {{ $paciente->tipo_documento->cod_rips }}{{ $paciente->numero_documento }}</p>
                                    <p><strong>Sexo:</strong> {{ $paciente->sexo_nombre }}</p>
                                    <p><strong>Edad:</strong> {{ $paciente->edad() }}</p>
                                    <p><strong>Fecha de nacimiento:</strong> {{ $paciente->fecha_nacimiento?->format('d-m-Y') }}</p>
                                    <p><strong>Orden Nº:</strong> {{ $orden->numero }}</p>
                                    <p><strong>Fecha del resultado:</strong> {{ $procedimiento->updated_at->format('d-m-Y') }}</p>
                                </div>
                                <h1 class="my-4 text-center text-base font-semibold uppercase">
                                    <a href="{{ route('resultados.show', $procedimiento) }}" class="text-primary underline print:text-black print:no-underline">
                                        {{ $procedimiento->examen->nombre }}
                                    </a>
                                </h1>
                            </th>
                        </tr>
                        <tr class="border-y border-gray-700 text-left">
                            <th class="py-2 pl-2">Parámetro</th>
                            <th class="py-2 text-right">Resultado</th>
                            <th class="py-2">U. Medida</th>
                            <th class="py-2">V. de referencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reporte['parametros'] as $parametro)
                            @if (($parametro['grupo'] ?? null) && $parametro['grupo'] !== $lastGroup)
                                <tr>
                                    <th colspan="4" class="pt-2 pl-2 text-left uppercase">{{ $parametro['grupo'] }}</th>
                                </tr>
                                @php $lastGroup = $parametro['grupo']; @endphp
                            @endif
                            <tr class="border-b border-dashed border-gray-300">
                                <x-parametro-print-table :item="$parametro" />
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($procedimiento->empleado)
                    <p class="mt-4 text-right text-xs">
                        Profesional: {{ $procedimiento->empleado->user?->name }}
                        @if ($procedimiento->empleado->numero_documento)
                            — {{ $procedimiento->empleado->numero_documento }}
                        @endif
                    </p>
                @endif
            </article>
        @endforeach
    </main>
</x-app-layout>

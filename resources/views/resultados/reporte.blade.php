{{--
    Reporte de resultados (IM-02). Una sola plantilla para la vista previa en el navegador,
    la impresión y el PDF de DomPDF.

    Reglas de maquetación (DomPDF 3.x):
      - Nada de flex, grid ni clases de Tailwind: solo tablas y el CSS de este <style>.
      - Encabezado y pie con position: fixed, hijos directos de <body>: DomPDF y Chrome los repiten
        en cada página dentro del margen que les reserva @page.
      - Cada examen es un bloque hermano con page-break-inside: avoid (nunca anidado en una celda).
        Si un examen no cabe en una página se parte entre grupos de parámetros (ver el bloque PHP de abajo).
      - Imágenes (logo y firmas) como data URI: DomPDF no descarga asset() sin isRemoteEnabled.
      - Fuente Helvetica (base 14 de PDF): cubre Latin-1 (tildes, ñ, µ, ³). Evitar ≤, ≥ o ⁶.

    Variables:
      $reporte  arreglo con el contrato de IM-01 (docs/CHECKLIST_DEUDA_TECNICA.md). Si no llega,
                se usan los datos ficticios de resultados/reporte/ejemplo.php.
      $enPdf    true cuando lo renderiza DomPDF: activa la numeración "Página X de Y" y quita los
                estilos de pantalla.
--}}
@php
    $enPdf = $enPdf ?? false;
    $cambios = $cambios ?? collect();
    $reporte = $reporte ?? (function () use (&$modoEjemplo) {
        $modoEjemplo = $modoEjemplo ?? 'orden';

        return require resource_path('views/resultados/reporte/ejemplo.php');
    })();

    $lab = $reporte['laboratorio'];
    $paciente = $reporte['paciente'];
    $orden = $reporte['orden'] ?? null;
    $tituloReporte = $tituloReporte ?? ($orden ? 'Resultados de orden '.$orden['numero'] : 'Reporte de resultados');
    $urlVolver = $urlVolver ?? null;
    $urlPdf = $urlPdf ?? null;

    /*
     * Paginación determinista, en puntos (pt). DomPDF solo respeta page-break-inside: avoid si el
     * bloque cabe en una página vacía; si no cabe lo parte donde caiga. Por eso cada bloque se
     * estima aquí y, si no cabe, el examen se parte entre grupos antes de llegar a DomPDF.
     * Medidas tomadas del PDF de ejemplo (carta, márgenes de @page 60/20 mm, filas de 8 pt).
     * Si cambian los márgenes de @page, las fuentes o los paddings, recalibrar estos valores.
     */
    $ALTO_UTIL = 565 - 25;            // alto útil de la página menos un margen de seguridad
    $ALTO_ENCABEZADO_EXAMEN = 42;     // título + fila de columnas
    $ALTO_GRUPO = 19;
    $ALTO_LINEA = 17;                 // fila de una línea
    $ALTO_LINEA_EXTRA = 10;           // cada línea adicional dentro de una fila
    $ALTO_METODO = 8;
    $ALTO_FIRMA = 95;                 // firma + margen inferior del bloque
    $CARACTERES_NOMBRE = 34;          // caracteres en mayúscula por línea en la columna Parámetro
    $CARACTERES_TEXTO = 120;          // caracteres por línea en una fila de texto a todo el ancho

    $costoParametro = function (array $p) use ($ALTO_LINEA, $ALTO_LINEA_EXTRA, $ALTO_METODO, $CARACTERES_NOMBRE, $CARACTERES_TEXTO): float {
        if (($p['tipo'] ?? 'valor') === 'texto') {
            $largo = mb_strlen((string) $p['nombre']) + mb_strlen((string) $p['resultado']) + 4;
            $lineas = max(1, (int) ceil($largo / $CARACTERES_TEXTO));
        } else {
            $lineas = max(1, (int) ceil(mb_strlen($p['nombre']) / $CARACTERES_NOMBRE));
        }

        return $ALTO_LINEA + ($lineas - 1) * $ALTO_LINEA_EXTRA
            + (filled($p['metodo'] ?? null) ? $ALTO_METODO : 0);
    };
    $costoGrupo = fn (array $g): float => (filled($g['nombre']) ? $ALTO_GRUPO : 0)
        + array_sum(array_map($costoParametro, $g['parametros']));

    $mostrarOrden = $orden === null;
    $bloques = [];
    $hayFueraDeRango = false;

    foreach ($reporte['examenes'] as $examen) {
        $grupos = array_values(array_filter($examen['grupos'], fn ($g) => count($g['parametros']) > 0));
        foreach ($grupos as $g) {
            foreach ($g['parametros'] as $p) {
                $hayFueraDeRango = $hayFueraDeRango || ($p['fuera_de_rango'] ?? false);
            }
        }

        $total = $ALTO_ENCABEZADO_EXAMEN + $ALTO_FIRMA + array_sum(array_map($costoGrupo, $grupos));

        if ($total <= $ALTO_UTIL) {
            $trozos = [$grupos];
        } else {
            // 1) Partir los grupos que solos no caben en una página (último recurso: por filas).
            $capacidad = $ALTO_UTIL - $ALTO_ENCABEZADO_EXAMEN - $ALTO_GRUPO;
            $piezas = [];
            foreach ($grupos as $g) {
                if ($costoGrupo($g) <= $capacidad) {
                    $piezas[] = $g;
                    continue;
                }
                $actual = [];
                $usado = 0;
                $esContinuacion = false;
                foreach ($g['parametros'] as $p) {
                    $c = $costoParametro($p);
                    if ($actual && $usado + $c > $capacidad) {
                        $piezas[] = ['nombre' => $g['nombre'], 'parametros' => $actual, 'continuacion' => $esContinuacion];
                        $actual = [];
                        $usado = 0;
                        $esContinuacion = true;
                    }
                    $actual[] = $p;
                    $usado += $c;
                }
                $piezas[] = ['nombre' => $g['nombre'], 'parametros' => $actual, 'continuacion' => $esContinuacion];
            }

            // 2) Acumular piezas en trozos de una página; la firma viaja con la última pieza.
            $trozos = [];
            $actual = [];
            $usado = $ALTO_ENCABEZADO_EXAMEN;
            $ultima = count($piezas) - 1;
            foreach ($piezas as $i => $pieza) {
                $c = $costoGrupo($pieza) + ($i === $ultima ? $ALTO_FIRMA : 0);
                if ($actual && $usado + $c > $ALTO_UTIL) {
                    $trozos[] = $actual;
                    $actual = [];
                    $usado = $ALTO_ENCABEZADO_EXAMEN;
                }
                $actual[] = $pieza;
                $usado += $c;
            }
            $trozos[] = $actual;
        }

        foreach ($trozos as $i => $trozo) {
            $bloques[] = [
                'examen' => $examen,
                'grupos' => $trozo,
                'continuacion' => $i > 0,
                'ultimo' => $i === count($trozos) - 1,
                'mostrar_orden' => $mostrarOrden,
            ];
        }
    }

    $bloquesPorOrden = collect($bloques)->groupBy(
        fn (array $bloque) => $bloque['examen']['orden_id'] ?? $bloque['examen']['orden_numero'] ?? 'sin-orden'
    );
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados · {{ $orden ? 'Orden '.$orden['numero'] : $paciente['nombre'] }}</title>
    <style>
        /* Sin "size" aquí: DomPDF descarta la regla @page completa si la trae (el papel lo fija setPaper). */
        @page {
            margin: 60mm 14mm 20mm 14mm;
        }

        * { box-sizing: border-box; }

        /* No tocar el margin de <html>: DomPDF aplica ahí los márgenes de @page. */
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.25;
            color: #0d191c;
            background: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            padding: 0;
            vertical-align: top;
            text-align: left;
            font-weight: normal;
        }

        /* ---------- Encabezado (repetido en cada página) ---------- */
        .encabezado {
            position: fixed;
            top: -52mm;
            left: 0;
            right: 0;
            height: 48mm;
        }

        .marca-logo {
            width: 20mm;
            vertical-align: middle;
        }

        .marca-logo img {
            width: 17mm;
            height: 17mm;
        }

        .marca-texto {
            vertical-align: middle;
        }

        .lab-nombre {
            font-size: 14pt;
            font-weight: bold;
            color: #0d191c;
            letter-spacing: 0.2pt;
        }

        .lab-sede {
            font-size: 9pt;
            color: #4b8f9b;
            margin-top: 0.6mm;
        }

        .lab-nit {
            font-size: 8pt;
            color: #5b6b6e;
            margin-top: 0.4mm;
        }

        .doc-meta {
            width: 62mm;
            vertical-align: middle;
            text-align: right;
        }

        .doc-titulo {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
            color: #4b8f9b;
        }

        .doc-dato {
            font-size: 8pt;
            color: #5b6b6e;
            margin-top: 0.6mm;
        }

        .doc-dato strong {
            color: #0d191c;
        }

        .paciente {
            margin-top: 4mm;
            border-top: 0.6pt solid #4b8f9b;
            border-bottom: 0.6pt solid #cfe4e8;
            background: #f3f9fa;
        }

        .paciente td {
            padding: 1.6mm 2.5mm;
        }

        .etiqueta {
            display: block;
            font-size: 6.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            color: #5b6b6e;
        }

        .valor {
            display: block;
            font-size: 9pt;
            color: #0d191c;
        }

        .paciente-nombre .valor {
            font-size: 11pt;
            font-weight: bold;
        }

        /* ---------- Pie (repetido en cada página) ---------- */
        .pie {
            position: fixed;
            bottom: -14mm;
            left: 0;
            right: 0;
            height: 10mm;
            border-top: 0.5pt solid #cfe4e8;
            padding-top: 1.5mm;
            font-size: 7pt;
            color: #5b6b6e;
        }

        .pie-contacto {
            text-align: left;
        }

        .pie-pagina {
            width: 30mm;
            text-align: right;
        }

        .pie-pagina .numero:after {
            content: counter(page);
        }

        /* ---------- Bloque de examen ---------- */
        .bloque {
            page-break-inside: avoid;
            break-inside: avoid;
            margin-bottom: 5mm;
        }

        .examen {
            table-layout: fixed;
        }

        .titulo {
            margin-bottom: 1.2mm;
        }

        .titulo td {
            vertical-align: bottom;
        }

        .titulo-nombre {
            border-left: 1.4mm solid #0eb4d1;
            padding: 0.6mm 0 0.6mm 2.5mm;
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4pt;
            color: #0d191c;
        }

        .continuacion {
            font-weight: normal;
            text-transform: none;
            font-size: 8.5pt;
            color: #5b6b6e;
            letter-spacing: 0;
        }

        .titulo-meta {
            width: 70mm;
            text-align: right;
            font-size: 7pt;
            color: #5b6b6e;
            padding-bottom: 0.8mm;
        }

        .columnas th {
            font-size: 6.8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            color: #4b8f9b;
            padding: 1.2mm 2mm;
            border-top: 0.6pt solid #0d191c;
            border-bottom: 0.6pt solid #0d191c;
        }

        .col-resultado {
            text-align: right;
        }

        .columnas th.col-resultado {
            text-align: right;
        }

        .grupo td {
            padding: 2.2mm 2mm 0.8mm 2mm;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
            color: #4b8f9b;
            border-bottom: 0.4pt solid #cfe4e8;
        }

        .fila td {
            padding: 0.9mm 2mm;
            border-bottom: 0.4pt dotted #b9d3d8;
        }

        .fila .col-parametro {
            text-transform: uppercase;
            font-size: 8pt;
        }

        .metodo {
            display: block;
            text-transform: none;
            font-size: 6.5pt;
            color: #5b6b6e;
            margin-top: 0.3mm;
        }

        .fila .col-resultado {
            font-weight: bold;
            font-size: 9pt;
        }

        .fila td { overflow-wrap: break-word; word-wrap: break-word; }

        .col-unidades, .col-referencia { padding-left: 2mm; }

        .fila .col-unidades,
        .fila .col-referencia {
            color: #3a4a4d;
        }

        .marca {
            padding-right: 1.5mm;
        }

        .fila-alerta .col-resultado {
            color: #000000;
            text-decoration: underline;
        }

        .fila-texto td {
            padding: 1.6mm 2mm;
        }

        .texto-etiqueta {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4pt;
            padding-right: 2mm;
        }

        .texto-valor {
            font-style: italic;
        }

        /* ---------- Firma ---------- */
        .firma {
            margin-top: 2mm;
        }

        .firma-celda {
            width: 62mm;
            text-align: center;
        }

        .firma-imagen {
            height: 14mm;
            max-width: 50mm;
        }

        .firma-vacia {
            height: 14mm;
        }

        .firma-linea {
            border-top: 0.5pt solid #0d191c;
            padding-top: 0.8mm;
            font-size: 8pt;
            font-weight: bold;
        }

        .firma-cargo {
            font-size: 6.8pt;
            color: #5b6b6e;
        }

        /* ---------- Cierre ---------- */
        .seccion-titulo {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            color: #0d191c;
            padding: 0 0 1.2mm 2.5mm;
            border-left: 1.4mm solid #cfe4e8;
        }

        .nota {
            font-size: 7pt;
            color: #5b6b6e;
            margin-top: 1mm;
        }

        .historial-cambios {
            margin-top: 8mm;
        }

        .historial-cambios th,
        .historial-cambios td {
            padding: 1.5mm 2mm;
            border-bottom: 0.4pt solid #cfe4e8;
            vertical-align: top;
        }

        .historial-cambios th {
            font-weight: bold;
            text-align: left;
        }

        .acciones-reporte {
            display: flex;
            justify-content: flex-end;
            gap: 3mm;
            margin-bottom: 6mm;
        }

        .acciones-reporte a,
        .acciones-reporte button {
            border: 0.5pt solid #4b8f9b;
            background: #ffffff;
            color: #0d191c;
            padding: 2mm 4mm;
            text-decoration: none;
            font: inherit;
            cursor: pointer;
        }

        .orden-seccion + .orden-seccion {
            page-break-before: always;
            break-before: page;
        }

        .orden-seccion-encabezado {
            margin-bottom: 4mm;
            padding: 2mm 2.5mm;
            border-top: 0.6pt solid #0d191c;
            border-bottom: 0.6pt solid #cfe4e8;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .orden-seccion-encabezado span {
            margin-left: 4mm;
            font-size: 7pt;
            font-weight: normal;
            text-transform: none;
            color: #5b6b6e;
        }

        .orden-seccion-sede {
            margin: 0 0 3mm;
            font-size: 7pt;
            color: #5b6b6e;
        }

        @media print {
            .acciones-reporte,
            .historial-cambios {
                display: none;
            }
        }
    </style>
    @unless ($enPdf)
        {{--
            Modo web (vista previa e impresión desde el navegador). Chrome no ubica bien los position: fixed
            con desplazamiento negativo al imprimir, así que aquí el encabezado y el pie van en el flujo normal:
            salen una vez por documento. El encabezado en cada página es del PDF (IM-01/IM-03).
            Este bloque no llega a DomPDF, por eso puede usar flex.
        --}}
        <style>
            @page {
                size: letter;
                margin: 12mm 14mm;
            }

            body {
                display: flex;
                flex-direction: column;
            }

            .encabezado, .pie {
                position: static;
                height: auto;
            }

            .encabezado { margin-bottom: 6mm; }
            main { order: 2; }
            .pie { order: 3; margin-top: 8mm; }

            @media screen {
                html { background: #e7f2f3; }

                body {
                    width: 215.9mm;
                    min-height: 279.4mm;
                    margin: 8mm auto;
                    padding: 12mm 14mm 14mm;
                    box-shadow: 0 1mm 4mm rgba(13, 25, 28, 0.15);
                }
            }

            @media screen and (max-width: 230mm) {
                body {
                    width: auto;
                    min-height: 0;
                    margin: 0;
                    padding: 16px;
                    box-shadow: none;
                }
            }
        </style>
    @endunless
</head>
<body>
    @unless ($enPdf)
        <nav class="acciones-reporte">
            @if ($urlVolver)
                <a href="{{ $urlVolver }}">Volver</a>
            @endif
            <button type="button" onclick="window.print()">Imprimir</button>
            @if ($urlPdf)
                <a href="{{ $urlPdf }}">Descargar PDF</a>
            @endif
        </nav>
    @endunless
    <header class="encabezado">
        <table>
            <tr>
                @if (filled($lab['logo_src'] ?? null))
                    <td class="marca-logo"><img src="{{ $lab['logo_src'] }}" alt="Logo de {{ $lab['razon_social'] }}"></td>
                @endif
                <td class="marca-texto">
                    <div class="lab-nombre">{{ $lab['razon_social'] }}</div>
                    <div class="lab-sede">{{ $lab['sede'] }}</div>
                    <div class="lab-nit">NIT {{ $lab['nit'] }}</div>
                </td>
                <td class="doc-meta">
                    <div class="doc-titulo">{{ $tituloReporte }}</div>
                    @if ($orden)
                        <div class="doc-dato">Orden Nº <strong>{{ $orden['numero'] }}</strong></div>
                        <div class="doc-dato">Fecha de atención <strong>{{ $orden['fecha_atencion'] }}</strong></div>
                    @else
                        <div class="doc-dato">Total de exámenes <strong>{{ $reporte['total_examenes'] }}</strong></div>
                    @endif
                    <div class="doc-dato">Emitido {{ $reporte['fecha_emision'] }}</div>
                </td>
            </tr>
        </table>

        <table class="paciente">
            <tr>
                <td colspan="2" class="paciente-nombre">
                    <span class="etiqueta">Paciente</span>
                    <span class="valor">{{ $paciente['nombre'] }}</span>
                </td>
                <td colspan="2">
                    <span class="etiqueta">Identificación</span>
                    <span class="valor">{{ $paciente['documento'] }}</span>
                </td>
            </tr>
            <tr>
                <td style="width: 25%">
                    <span class="etiqueta">Sexo</span>
                    <span class="valor">{{ $paciente['sexo'] }}</span>
                </td>
                <td style="width: 25%">
                    <span class="etiqueta">Edad</span>
                    <span class="valor">{{ $paciente['edad'] }}</span>
                </td>
                <td style="width: 25%">
                    <span class="etiqueta">Fecha de nacimiento</span>
                    <span class="valor">{{ $paciente['fecha_nacimiento'] }}</span>
                </td>
                <td style="width: 25%">
                    <span class="etiqueta">Exámenes</span>
                    <span class="valor">{{ $reporte['total_examenes'] }}</span>
                </td>
            </tr>
        </table>
    </header>

    <footer class="pie">
        <table>
            <tr>
                <td class="pie-contacto">
                    @if (! empty($lab['telefonos']))Tel. {{ implode(' · ', $lab['telefonos']) }} &nbsp;|&nbsp; @endif
                    {{ $lab['direccion'] }}
                    @if (filled($lab['email'] ?? null)) &nbsp;|&nbsp; {{ $lab['email'] }}@endif
                </td>
                @if ($enPdf)
                    <td class="pie-pagina">Página <span class="numero"></span></td>
                @endif
            </tr>
        </table>
    </footer>

    <main>
        @if (empty($bloques))
            <p class="nota">No hay exámenes terminados con resultados para incluir en el reporte.</p>
        @else
            @foreach ($bloquesPorOrden as $bloquesOrden)
                @php($primerExamenOrden = $bloquesOrden->first()['examen'])
                <section class="orden-seccion">
                    @if ($mostrarOrden)
                        <div class="orden-seccion-encabezado">
                            Orden Nº {{ $primerExamenOrden['orden_numero'] }}
                            <span>Fecha de atención {{ $primerExamenOrden['fecha_atencion'] }}</span>
                        </div>
                    @endif
                    @if (isset($primerExamenOrden['laboratorio']))
                        <p class="orden-seccion-sede">
                            {{ $primerExamenOrden['laboratorio']['razon_social'] }}
                            · {{ $primerExamenOrden['laboratorio']['sede'] }}
                            @if (filled($primerExamenOrden['laboratorio']['nit'] ?? null))
                                · NIT {{ $primerExamenOrden['laboratorio']['nit'] }}
                            @endif
                        </p>
                    @endif
                    @foreach ($bloquesOrden as $bloque)
                        @include('resultados.reporte._examen', ['bloque' => $bloque])
                    @endforeach
                </section>
            @endforeach
        @endif

        @if ($hayFueraDeRango)
            <p class="nota">* Valor fuera del rango de referencia. La interpretación de los resultados corresponde al médico tratante.</p>
        @endif

        @if ($cambios->isNotEmpty())
            <section class="historial-cambios">
                <div class="seccion-titulo">Historial de modificaciones</div>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Parámetro</th>
                            <th>Valor anterior</th>
                            <th>Valor nuevo</th>
                            <th>Modificado por</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cambios as $cambio)
                            <tr>
                                <td>{{ $cambio->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $cambio->parametro_nombre }}</td>
                                <td>{{ $cambio->valor_anterior ?? 'Vacío' }}</td>
                                <td>{{ $cambio->valor_nuevo ?? 'Vacío' }}</td>
                                <td>{{ $cambio->usuario_nombre }}</td>
                                <td>{{ $cambio->observacion }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endif
    </main>
</body>
</html>

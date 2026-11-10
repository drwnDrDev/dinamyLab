{{--
    Un bloque de examen: título, tabla de parámetros y, en el último trozo, la firma.
    Todo el bloque lleva page-break-inside: avoid; si el examen no cabe en una página,
    reporte.blade.php lo parte entre grupos y los trozos siguientes se marcan como continuación.
--}}
@php($examen = $bloque['examen'])
<div class="bloque">
    <table class="titulo">
        <tr>
            <td class="titulo-nombre">
                {{ $examen['nombre'] }}@if ($bloque['continuacion'])<span class="continuacion"> (continuación)</span>@endif
            </td>
            <td class="titulo-meta">
                @if ($bloque['mostrar_orden'])Orden {{ $examen['orden_numero'] }} · @endif
                Validado {{ $examen['fecha_validacion'] }}
            </td>
        </tr>
    </table>

    {{-- Anchos en las celdas del encabezado: con table-layout fixed DomPDF los toma de la primera fila e ignora <col>. --}}
    <table class="examen">
        <thead>
            <tr class="columnas">
                <th class="col-parametro" style="width: 42%">Parámetro</th>
                <th class="col-resultado" style="width: 17%">Resultado</th>
                <th class="col-unidades" style="width: 14%">Unidades</th>
                <th class="col-referencia" style="width: 27%">Valor de referencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bloque['grupos'] as $grupo)
                @if (filled($grupo['nombre']))
                    <tr class="grupo">
                        <td colspan="4">{{ $grupo['nombre'] }}@if ($grupo['continuacion'] ?? false) (cont.)@endif</td>
                    </tr>
                @endif
                @foreach ($grupo['parametros'] as $item)
                    @include('resultados.reporte._parametro', ['item' => $item])
                @endforeach
            @endforeach
        </tbody>
    </table>

    @if ($bloque['ultimo'])
        @php($bacteriologo = $examen['bacteriologo'])
        <table class="firma">
            <tr>
                <td class="firma-espacio"></td>
                <td class="firma-celda">
                    @if (filled($bacteriologo['firma_src'] ?? null))
                        <img class="firma-imagen" src="{{ $bacteriologo['firma_src'] }}" alt="Firma de {{ $bacteriologo['nombre'] }}">
                    @else
                        <div class="firma-vacia"></div>
                    @endif
                    <div class="firma-linea">{{ $bacteriologo['nombre'] }}</div>
                    <div class="firma-cargo">Bacteriólogo(a) · {{ $bacteriologo['documento'] }}</div>
                </td>
            </tr>
        </table>
    @endif
</div>

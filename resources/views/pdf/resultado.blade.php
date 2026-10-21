{{--
    Vista PDF (dompdf) - Reporte de resultado de laboratorio.
    Datos esperados:
      $procedimiento : Procedimiento con orden.paciente, examen, sede.empresa, empleado.user
      $parametros    : array de parámetros (salida de App\Services\EscogerReferencia)
      $empresa       : (opcional) Empresa; si no viene se usa $procedimiento->sede->empresa
    No usa Tailwind/Vite: dompdf no resuelve flex/grid ni assets remotos.
--}}
@php
    $empresa  = $empresa ?? $procedimiento->sede->empresa;
    $sede     = $procedimiento->sede;
    $paciente = $procedimiento->orden->paciente;

    // Imágenes a base64 para que dompdf las incruste sin depender de URLs.
    $imagenBase64 = function (?string $discoRuta) {
        if (!$discoRuta) return null;
        foreach ([public_path('storage/'.$discoRuta), storage_path('app/public/'.$discoRuta)] as $ruta) {
            if (is_file($ruta)) {
                $mime = mime_content_type($ruta) ?: 'image/png';
                return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($ruta));
            }
        }
        return null;
    };

    $logo  = $imagenBase64($sede->logo ?? null);
    $firma = $imagenBase64(optional($procedimiento->empleado)->firma ?? null);

    $telefono = optional($sede->telefonos->first())->numero;
    $direccion = optional($sede->direccion)->direccion;
    $email = optional($sede->emails->first())->email ?? optional($empresa->emails->first())->email;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Resultado {{ $procedimiento->orden->numero }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 10px; color: #111; margin: 0; }
        .muted { color: #555; }
        .header { width: 100%; border-bottom: 1px solid #333; padding-bottom: 6px; margin-bottom: 8px; }
        .header td { vertical-align: middle; }
        .logo { width: 70px; }
        .logo img { width: 64px; }
        .empresa { font-size: 13px; font-weight: bold; }
        .datos-paciente { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .datos-paciente td { padding: 2px 6px 2px 0; vertical-align: top; }
        .titulo-examen { text-align: center; font-size: 12px; font-weight: bold; text-transform: uppercase; margin: 10px 0 6px; }
        table.resultados { width: 100%; border-collapse: collapse; }
        table.resultados thead th { border-top: 1px solid #333; border-bottom: 1px solid #333; padding: 3px 4px; text-align: left; font-size: 9px; }
        table.resultados td { padding: 3px 4px; border-bottom: 1px dotted #bbb; }
        .col-resultado { text-align: right; font-weight: bold; }
        .fuera-rango { color: #b00; }
        .grupo { font-weight: bold; text-transform: uppercase; padding-top: 6px; }
        .textarea-row td { padding: 4px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #333; padding-top: 4px; font-size: 8px; color: #555; }
        .firma { margin-top: 20px; text-align: right; }
        .firma img { width: 120px; }
        .firma .nombre { font-size: 8px; }
    </style>
</head>
<body>

<table class="header">
    <tr>
        <td class="logo">
            @if ($logo)
                <img src="{{ $logo }}" alt="{{ $sede->nombre }}">
            @endif
        </td>
        <td>
            <div class="empresa">{{ $empresa->nombre_comercial }}</div>
            <div>{{ $sede->nombre }}</div>
            <div class="muted">NIT. {{ $empresa->nit }}</div>
        </td>
    </tr>
</table>

<table class="datos-paciente">
    <tr>
        <td><strong>Paciente:</strong> {{ $paciente->nombreCompleto() }}</td>
        <td><strong>Órden Nº:</strong> {{ $procedimiento->orden->numero }}</td>
    </tr>
    <tr>
        <td><strong>Identificación:</strong> {{ $paciente->tipo_documento->cod_rips }}{{ $paciente->numero_documento }}</td>
        <td><strong>Fecha de atención:</strong> {{ $procedimiento->orden->created_at->format('d-m-Y') }}</td>
    </tr>
    <tr>
        <td>
            <strong>Sexo:</strong> {{ $paciente->sexo === 'M' ? 'Masculino' : 'Femenino' }}
            &nbsp;&nbsp;<strong>Edad:</strong> {{ $paciente->edad() }}
        </td>
        <td><strong>Fecha de nacimiento:</strong> {{ $paciente->fecha_nacimiento->format('d-m-Y') }}</td>
    </tr>
</table>

<div class="titulo-examen">{{ $procedimiento->examen->nombre }}</div>

<table class="resultados">
    <thead>
        <tr>
            <th style="width:40%">Parámetro</th>
            <th style="width:20%; text-align:right">Resultado</th>
            <th style="width:18%">U. Medida</th>
            <th style="width:22%">V. de Referencia</th>
        </tr>
    </thead>
    <tbody>
        @php $lastGroup = null; @endphp
        @foreach ($parametros as $p)
            @if (!empty($p['grupo']) && $p['grupo'] !== $lastGroup)
                <tr><td colspan="4" class="grupo">{{ $p['grupo'] }}</td></tr>
                @php $lastGroup = $p['grupo']; @endphp
            @endif

            @if (($p['tipo_dato'] ?? null) === 'textarea')
                <tr class="textarea-row">
                    <td><strong>{{ $p['nombre'] }}</strong></td>
                    <td colspan="3">{{ $p['resultado'] ?? '' }}</td>
                </tr>
            @else
                <tr>
                    <td>
                        {{ $p['nombre'] }}
                        @if (!empty($p['metodo']))<span class="muted"> - {{ $p['metodo'] }}</span>@endif
                    </td>
                    <td class="col-resultado {{ (isset($p['es_normal']) && !$p['es_normal']) ? 'fuera-rango' : '' }}">
                        {{ $p['resultado'] ?? '' }}@if (isset($p['es_normal']) && !$p['es_normal']) *@endif
                    </td>
                    <td>{{ $p['unidades'] ?? '' }}</td>
                    <td>{{ $p['referencia'] ?? '' }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
    <tfoot>
        3014819820 -Cr 78L 69 D 15
    </tfoot>
</table>

<div class="firma">
    @if ($firma)
        <img src="{{ $firma }}" alt="{{ optional($procedimiento->empleado->user)->name }}">
    @endif
    <div class="nombre">
        {{ optional($procedimiento->empleado->user)->name }} -
        {{ optional($procedimiento->empleado)->numero_documento }}
    </div>
</div>

<div class="footer">
    @if ($telefono)<span>Tels: {{ $telefono }}</span>@endif
    @if ($direccion)<span> | Dirección: {{ $direccion }}</span>@endif
    @if ($email)<span> | Email: {{ $email }}</span>@endif
</div>

</body>
</html>

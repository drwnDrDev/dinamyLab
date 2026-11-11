{{-- Una fila de resultado. Solo tablas y CSS de reporte.blade.php (DomPDF no soporta flex ni grid). --}}
@if (($item['tipo'] ?? 'valor') === 'texto')
    <tr class="fila fila-texto">
        <td colspan="4">
            <span class="texto-etiqueta">{{ $item['nombre'] }}</span>
            <span class="texto-valor">{{ filled($item['resultado']) ? $item['resultado'] : 'Sin registro' }}</span>
        </td>
    </tr>
@else
    <tr class="fila{{ $item['fuera_de_rango'] ? ' fila-alerta' : '' }}">
        <td class="col-parametro">
            {{ $item['nombre'] }}
            @if (filled($item['metodo'] ?? null))
                <span class="metodo">{{ $item['metodo'] }}</span>
            @endif
        </td>
        <td class="col-resultado">
            @if ($item['fuera_de_rango'])<span class="marca">*</span>@endif{{ filled($item['resultado']) ? $item['resultado'] : '—' }}
        </td>
        <td class="col-unidades">{{ $item['unidades'] }}</td>
        <td class="col-referencia">{{ $item['referencia'] }}</td>
    </tr>
@endif

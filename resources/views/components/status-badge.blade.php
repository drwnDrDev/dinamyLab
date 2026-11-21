@props(['estado' => null, 'tone' => null])

@php
    // Mapea los estados de App\Estado a un tono visual; también acepta tone + slot directamente.
    $tonos = [
        'pendiente de muestra' => 'warning',
        'toma muestra' => 'info',
        'en proceso' => 'warning',
        'terminado' => 'success',
        'entregado' => 'success',
        'anulado' => 'danger',
    ];
    // Nombres completos para que Tailwind los detecte al compilar (no construirlos dinámicamente).
    $clases = [
        'neutral' => 'badge-neutral',
        'info' => 'badge-info',
        'warning' => 'badge-warning',
        'success' => 'badge-success',
        'danger' => 'badge-danger',
    ];
    $valor = $estado instanceof \App\Estado ? $estado->value : $estado;
    $tono = $tone ?? ($tonos[$valor] ?? 'neutral');
    $claseTono = $clases[$tono] ?? $clases['neutral'];
    $texto = $slot->isNotEmpty() ? $slot : \Illuminate\Support\Str::ucfirst((string) $valor);
@endphp

<span {{ $attributes->merge(['class' => "badge {$claseTono}"]) }}>{{ $texto }}</span>

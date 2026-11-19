@props(['valor'])

{{-- Valor en pesos colombianos: $113.000 --}}
<span {{ $attributes->merge(['class' => 'tabular-nums']) }}>${{ number_format((float) $valor, 0, ',', '.') }}</span>

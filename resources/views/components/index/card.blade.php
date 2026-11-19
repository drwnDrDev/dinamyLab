{{-- Tarjeta de listado: filtros (slot "filters"), contenido y paginación (slot "footer"). --}}
<section {{ $attributes->merge(['class' => 'surface']) }}>
    {{ $filters ?? '' }}
    {{ $slot }}
    {{ $footer ?? '' }}
</section>

{{-- Contenedor estándar para vistas de listado (index). --}}
<div {{ $attributes->merge(['class' => 'mx-auto flex w-full max-w-6xl flex-col gap-5 px-4 py-6 sm:px-6 lg:py-8']) }}>
    {{ $slot }}
</div>

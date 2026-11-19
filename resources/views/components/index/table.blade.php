{{-- Tabla de listado con scroll horizontal en pantallas pequeñas. --}}
<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'data-table']) }}>
        <thead>
            <tr>{{ $head }}</tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

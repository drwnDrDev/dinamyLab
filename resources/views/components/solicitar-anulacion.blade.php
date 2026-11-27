@props(['procedimiento'])

@if (auth()->user()->hasRole('prestador') && ! auth()->user()->hasRole('admin') && $procedimiento->puedeSolicitarAnulacion())
    <a href="{{ route('anulaciones.create', $procedimiento) }}" {{ $attributes->merge(['class' => 'inline-flex text-sm font-semibold text-primary hover:underline dark:text-cyan-300']) }}>
        {{ $procedimiento->solicitudAnulacionPendiente ? 'Ver solicitud de anulación' : 'Solicitar anulación' }}
    </a>
@endif

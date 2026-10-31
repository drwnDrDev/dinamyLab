<x-app-layout>
    <x-slot name="title">Factura {{ $factura->numero }}</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-text">Factura {{ $factura->numero }}</h1>
    </x-slot>

    <x-canva>
        <div class="space-y-2 text-text">
            <p><strong>Fecha de emisión:</strong> {{ $factura->fecha_emision?->format('d/m/Y') }}</p>
            <p><strong>Pagador:</strong>
                {{ $factura->pagador instanceof \App\Models\Persona ? $factura->pagador->nombreCompleto() : ($factura->pagador->razon_social ?? 'No disponible') }}
            </p>
            <p><strong>Estado:</strong> {{ $factura->estado }}</p>
            <p><strong>Subtotal:</strong> ${{ number_format($factura->subtotal, 0, ',', '.') }}</p>
            <p><strong>Total:</strong> ${{ number_format($factura->total, 0, ',', '.') }}</p>
            @if ($factura->observaciones)
                <p><strong>Observaciones:</strong> {{ $factura->observaciones }}</p>
            @endif
            <a href="{{ route('facturas') }}" class="inline-block rounded bg-primary px-4 py-2 text-white">Volver a facturas</a>
        </div>
    </x-canva>
</x-app-layout>

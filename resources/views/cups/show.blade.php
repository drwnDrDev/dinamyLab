<x-app-layout>
    <x-slot name="title">CUPS {{ $codigoCup->codigo }}</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-text">Código CUPS {{ $codigoCup->codigo }}</h1>
    </x-slot>

    <x-canva>
        <div class="space-y-2 text-text">
            <p><strong>Nombre:</strong> {{ $codigoCup->nombre }}</p>
            <p><strong>Grupo:</strong> {{ $codigoCup->grupo ?: 'No especificado' }}</p>
            <p><strong>Estado:</strong> {{ $codigoCup->activo ? 'Activo' : 'Inactivo' }}</p>
            <a href="{{ route('cups.index') }}" class="inline-block rounded bg-primary px-4 py-2 text-white">Volver a CUPS</a>
        </div>
    </x-canva>
</x-app-layout>

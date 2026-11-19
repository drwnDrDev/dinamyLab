@props(['action', 'submitLabel' => 'Filtrar'])

{{-- Barra de filtros GET: los campos van en el slot; Filtrar y Limpiar se agregan solos. --}}
<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'flex flex-wrap gap-2 border-b border-borders/70 bg-background/60 p-3 dark:border-slate-800 dark:bg-slate-900']) }}>
    {{ $slot }}
    <div class="flex gap-2">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ $action }}" class="btn btn-secondary">Limpiar</a>
    </div>
</form>

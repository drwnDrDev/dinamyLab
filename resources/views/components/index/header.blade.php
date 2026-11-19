@props(['title', 'subtitle' => null])

{{-- Encabezado de vista: título + subtítulo a la izquierda, acciones a la derecha. --}}
<div {{ $attributes->merge(['class' => 'flex min-h-10 flex-wrap items-end justify-between gap-3 print:hidden']) }}>
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-bold leading-tight tracking-tight text-text dark:text-slate-50">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-muted dark:text-slate-400">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>

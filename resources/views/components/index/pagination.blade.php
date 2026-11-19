@props(['paginator'])

{{-- Pie de tabla: resumen "Mostrando X–Y de Z" + navegación compacta. --}}
<footer class="flex flex-wrap items-center justify-between gap-2 border-t border-borders/70 px-4 py-2.5 text-[13px] text-muted dark:border-slate-800 dark:text-slate-400">
    <span>
        @if ($paginator->total() > 0)
            Mostrando {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} de {{ $paginator->total() }}
        @else
            Sin registros
        @endif
    </span>

    @if ($paginator->hasPages())
        @php
            $actual = $paginator->currentPage();
            $ultima = $paginator->lastPage();
            $desde = max(1, $actual - 2);
            $hasta = min($ultima, $actual + 2);
        @endphp
        <nav class="flex items-center gap-1" aria-label="Paginación">
            @if ($paginator->onFirstPage())
                <span class="btn btn-sm btn-secondary pointer-events-none opacity-50" aria-disabled="true">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-sm btn-secondary" rel="prev">Anterior</a>
            @endif

            @if ($desde > 1)
                <a href="{{ $paginator->url(1) }}" class="btn btn-sm btn-secondary min-w-8">1</a>
                @if ($desde > 2)<span class="px-1">…</span>@endif
            @endif

            @foreach ($paginator->getUrlRange($desde, $hasta) as $pagina => $url)
                @if ($pagina === $actual)
                    <span class="btn btn-sm min-w-8 border border-primary-strong bg-secondary text-text dark:bg-slate-800 dark:text-slate-100" aria-current="page">{{ $pagina }}</span>
                @else
                    <a href="{{ $url }}" class="btn btn-sm btn-secondary min-w-8">{{ $pagina }}</a>
                @endif
            @endforeach

            @if ($hasta < $ultima)
                @if ($hasta < $ultima - 1)<span class="px-1">…</span>@endif
                <a href="{{ $paginator->url($ultima) }}" class="btn btn-sm btn-secondary min-w-8">{{ $ultima }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-sm btn-secondary" rel="next">Siguiente</a>
            @else
                <span class="btn btn-sm btn-secondary pointer-events-none opacity-50" aria-disabled="true">Siguiente</span>
            @endif
        </nav>
    @endif
</footer>

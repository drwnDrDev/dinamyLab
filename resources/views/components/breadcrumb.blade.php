@props(['items' => []])

<nav class="flex items-center gap-1 text-xs mb-3 print:hidden">
    @foreach($items as $item)
        @if(!$loop->last)
            <a href="{{ $item['href'] }}" class="text-muted dark:text-slate-500 hover:text-primary dark:hover:text-cyan-400 transition-colors font-medium">{{ $item['label'] }}</a>
            <svg class="w-3 h-3 text-borders dark:text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @else
            <span class="text-text dark:text-slate-200 font-semibold">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>

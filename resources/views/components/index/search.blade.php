@props(['name' => 'busqueda', 'label' => 'Buscar', 'placeholder' => ''])

<label class="relative flex min-w-0 flex-[1_1_280px]">
    <span class="sr-only">{{ $label }}</span>
    <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-muted dark:text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
        <circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path>
    </svg>
    <input type="search" name="{{ $name }}" value="{{ request($name) }}" placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'input pl-10']) }}>
</label>

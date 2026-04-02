@props(['active'=> false, 'href' => null, 'type' => 'submit'])

@php
    $classes = 'inline-flex items-center px-5 py-2.5 bg-primary border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-wider shadow-sm shadow-primary/25 hover:bg-primary/90 hover:shadow-md hover:shadow-primary/20 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition-all ease-in-out duration-200';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif

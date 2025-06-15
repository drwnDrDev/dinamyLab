@props(['active'=> false, 'href' => null, 'type' => 'submit'])

@php
    $classes = 'inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-[#50D5F7] focus:bg-primary active:bg-primary focus:outline-none focus:ring-1 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150';
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

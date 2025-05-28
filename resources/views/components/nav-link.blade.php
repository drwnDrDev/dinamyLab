@props(['active'])

@php
$classes = ($active ?? false)
? 'inline-flex items-end mb-2 px-2 pt-2 border-b-2 border-indigo-400 text-md font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
: 'inline-flex items-end mb-2 px-2 pt-2 border-b-2 border-transparent text-md font-medium leading-5 text-cyan-600 hover:bg-cyan-300 hover:text-white hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="inline-flex w-8 h-8 items-center justify-center mr-2">
    {{-- Icon --}}
    <svg class="fill-current {{$active? "w-8": "w-6"}} h-auto aspect-square" xmlns="http://www.w3.org/2000/svg"  viewBox="0 -960 960 960" >
        <path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z" />
    </svg>
    </span>
    {{ $slot }}
</a>

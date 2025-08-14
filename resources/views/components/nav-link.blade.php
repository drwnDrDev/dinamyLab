@props(['active', 'icono'])

@php
$classes = ($active ?? false)
? 'rounded-2xl inline-flex items-center p-2 text-md bg-secondary font-medium leading-5 text-primary focus:outline-none focus:bg-secondary transition duration-150 ease-in-out text-nowrap'
: 'rounded-2xl inline-flex items-center p-2 text-md font-medium leading-5 text-text hover:bg-secondary focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out text-nowrap';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="inline-flex w-6 h-6 items-center justify-center mr-4">
        {{-- Icon --}}

        @switch ($icono)
        @case('panel')
        <x-iconos.panel :active='$active' />
        @break
        @case('personas')
        <x-iconos.personas :active='$active' />
        @break
        @case('ordenes')
        <x-iconos.ordenes :active='$active' />
        @break
        @case('procedimientos')
        <x-iconos.procedimientos :active='$active' />
        @break
        @case('resultados')
        <x-iconos.resultados :active='$active' />
        @break
        @case('caja')
        <x-iconos.caja :active='$active' />
        @break
        @case('facturas')
        <x-iconos.facturas :active='$active' />
        @break
        @case('reportes')
        <x-iconos.reportes :active='$active' />
        @break
        @case('admin')
        <x-iconos.admin :active='$active' />
        @break

        @default
        <x-iconos.personas :active='$active' />
        @break
        @endswitch


    </span>
    {{ $slot }}
</a>
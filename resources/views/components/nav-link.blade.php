@props(['active', 'icono'])

@php
$classes = ($active ?? false)
    ? 'w-full rounded-xl inline-flex items-center px-2 py-2.5 text-sm font-semibold leading-5 text-primary dark:text-cyan-400 bg-primary/10 dark:bg-cyan-400/10 border border-primary/20 dark:border-cyan-400/20 focus:outline-none transition-all duration-150 text-nowrap'
    : 'w-full rounded-xl inline-flex items-center px-2 py-2.5 text-sm font-medium leading-5 text-muted dark:text-slate-400 hover:bg-secondary dark:hover:bg-slate-800 hover:text-primary dark:hover:text-slate-200 focus:outline-none transition-all duration-150 text-nowrap';
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
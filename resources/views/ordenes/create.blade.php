<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Inicio', 'href' => route('inicio')],
            ['label' => 'Órdenes', 'href' => route('ordenes')],
            ['label' => 'Nueva Orden'],
        ]" />
        <h2 class="font-serif text-2xl font-bold text-text dark:text-white leading-tight">
            Nueva Orden Médica
        </h2>
    </x-slot>

    <article id="react-crear-orden" data-persona='@json($persona)'></article>

    @vite(['resources/js/appOrden.jsx'])
</x-app-layout>

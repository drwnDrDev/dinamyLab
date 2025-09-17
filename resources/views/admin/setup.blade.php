<x-app-layout>
    <x-slot name="title">
        Configuración Inicial
    </x-slot>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Configuración Inicial') }}
    </h2>
</x-slot>
@vite('resources/js/app.jsx')
<div id="root" class="p-4"></div>

</x-app-layout>

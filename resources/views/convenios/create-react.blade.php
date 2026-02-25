{{-- Este archivo reemplaza el formulario tradicional con un componente React --}}
<x-app-layout>
    <x-slot name="title">
        Convenios
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Convenio
        </h2>
    </x-slot>

    <x-canva>
        <div id="convenio-form-root"></div>
    </x-canva>

    @vite(['resources/js/convenioCreate.jsx'])
</x-app-layout>

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
        {{-- Script embebido para pasar datos del backend a React --}}
        <script type="application/json" id="documentos-data">
            @json($documentos)
        </script>

        {{-- Contenedor donde React montará el componente --}}
        <div id="convenio-form-root"></div>
    </x-canva>

    {{-- Script que importa el componente React --}}
    @vite(['resources/js/convenioCreate.jsx'])
</x-app-layout>

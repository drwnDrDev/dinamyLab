<x-app-layout>
    <x-slot name="title">
        Configuración Inicial
    </x-slot>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Configuración Inicial') }}
    </h2>
</x-slot>
<nav class="grid grid-cols-6 gap-2 px-4 mb-4">
    <x-primary-button class="bg-blue-300" >Servicios habilitados</x-primary-button>
    <x-primary-button class="bg-yellow-300">Diagnosticos frecuentes</x-primary-button>
    <x-primary-button class="bg-purple-300">Finalidad esperada</x-primary-button>
    <x-primary-button class="bg-green-300">Tipos de atencion</x-primary-button>
    <x-primary-button class="bg-red-300">Causa externa</x-primary-button>
    <x-primary-button class="bg-orange-300">Via ingreso</x-primary-button>
</nav>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <nav class="flex gap-4 mb-4">

            <x-primary-button class="bg-cyan-500" >activos</x-primary-button>
            <x-primary-button class="bg-yellow-500">inactivos</x-primary-button>
            <x-primary-button class="bg-purple-500">Top 8</x-primary-button>
            <form action="">
                <input type="text" name="search" class="border-2 border-gray-300 rounded-md">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Buscar</button>
            </form>

        </nav>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            Contenido de la página de configuración inicial

        </div>

    </div>
</x-app-layout>

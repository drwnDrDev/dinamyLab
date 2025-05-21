
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $persona->nombreCompleto() }}
        </h2>
        <h3>
            {{ $persona->tipo_documento }} {{ $persona->numero_documento }}
        </h3>
    </x-slot>


</x-app-layout>

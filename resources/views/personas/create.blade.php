<x-app-layout>
    @vite('resources/js/buscarPersona.js')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pacientes
        </h2>
    </x-slot>
    <x-canva class="max-w-5xl">

    <x-formPersona perfil="Paciente"/>
    </x-canva>

</x-app-layout>

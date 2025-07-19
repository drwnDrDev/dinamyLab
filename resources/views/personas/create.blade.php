<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pacientes
        </h2>
    </x-slot>
    <x-canva class="max-w-5xl">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Error!</strong>
                <span class="block sm:inline">Por favor, corrige los errores en el formulario.</span>
            </div>
            <ul class="mt-2">
            @foreach ($errors->all() as $error)
                <li class="text-red-600">{{ $error }}</li>

            @endforeach
            </ul>

        @endif
    <x-formPersona :accion="route('personas.store')" perfil="Paciente"/>

    </x-canva>
 @vite('resources/js/crearPersona.js')
</x-app-layout>

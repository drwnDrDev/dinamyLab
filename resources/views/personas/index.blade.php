<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pacientes') }}
        </h2>
    </x-slot>

    <form  class="mb-4 flex items-center gap-2">
        <input
            type="text"
           
            id="llave_busqueda"

            placeholder="número de documento"
            class="border rounded px-3 py-2"
        >
        <button type="submit" id="buscar"  class="bg-blue-500 text-white px-4 py-2 rounded">
            Buscar
        </button>
        <label for="buscar_nombre" class="flex items-center">
            <input
                type="checkbox"
                name="buscar_nombre"
                id="buscar_nombre"
                value="1"
                {{ request('buscar_nombre') ? 'checked' : '' }}
            >
            <span class="ml-2">Buscar por nombre</span>

        </label>

    </form>

            <h2>Pacientes</h2>
            <div class="w-full justify-around gap-2">
                <div class="grid grid-cols-8 bg-slate-300 p-2">
                    <p class="text-gray-900 col-span-5">Nombres y apellidos</p>
                    <p class="text-gray-900 col-span-3">Numero de Documento</p>
                </div>
                <div id="pacientes">


                </div>

                {{-- @foreach ($personas as $paciente)
                    <a href="{{route('personas.show', $paciente)}}" class="grid grid-cols-8 gap-2 hover:bg-slate-600 hover:text-slate-50">
                        <p class="text-gray-900 col-span-5">{{ $paciente->nombreCompleto() }}</p>
                        <p class="text-gray-900 col-span-3">{{ $paciente->tipo_documento }} {{ $paciente->numero_documento }}</p>
                    </a>
                @endforeach --}}
            </div>

@vite('resources/js/busquedaAvanzada.js')
</x-app-layout>

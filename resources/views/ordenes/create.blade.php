<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Orden') }}
        </h2>
    </x-slot>
    <x-canva>
        <div class="section_paciente bg-white">
            <x-formPersona perfil="paciente" :tipos_documento="$tipos_documento" :ciudades="$ciudades" :eps="$eps"/>
        <div x-data="{ open: false }">
            <label for="mostrarAcompaniante">Acompañante</label>
            <input type="checkbox" id="mostrarAcompaniante" @change="open = $event.target.checked">

            <div x-show="open" x-transition>
                <x-formPersona perfil="acompaniante" :tipos_documento="$tipos_documento" :ciudades="$ciudades" />
            </div>
        </div>

           <div>
                Exmamenes a realziar
            </div>
        </div>
    </x-canva>
</x-app-layout>

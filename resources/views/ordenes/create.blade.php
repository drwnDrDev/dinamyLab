<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Orden') }}
        </h2>
    </x-slot>
    <x-canva>
        <div class="section_paciente bg-white">
            
            <x-formPersona type="pagador" :tipos_documento="$tipos_documento" :ciudades="$ciudades" :eps="$eps"/>
                      
        </div>
    </x-canva>
</x-app-layout>
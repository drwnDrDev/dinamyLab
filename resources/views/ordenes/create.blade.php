<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Orden') }}
        </h2>
    </x-slot>
    <x-canva>
        <div class="section_paciente bg-white">
            <x-formPersona perfil="Paciente" :tipos_documento="$tipos_documento" :ciudades="$ciudades" :eps="$eps"/>
        </div>
        <div x-data="{ open: false }">
            <label for="mostrarAcompaniante">Acompañante</label>
            <input type="checkbox" id="mostrarAcompaniante" @change="open = $event.target.checked">

            <div x-show="open" x-transition>
                <x-formPersona perfil="acompaniante" :tipos_documento="$tipos_documento" :ciudades="$ciudades" />
            </div>
        </div>
            <div x-data="{ open: false }">
            <label for="mostrarAcompaniante">Pago</label>
            <input type="checkbox" id="mostrarAcompaniante" @change="open = !$event.target.checked" checked>

            <div x-show="open" x-transition>
                <x-input-label for="abono">Abono</x-input-label>
                <x-text-input type="number" id="abono" name="abono" value="0" class="form-input w-32"/>
            </div>
        </div>
        <section class="section_examenes bg-white p-4" x-data="{ examenes: [] }">	
            <h2 class="font-bold mb-4 text-xl text-secondary">Exámenes</h2>
           
                @foreach($examenes as $examen)
                    <div class="examen-item grid grid-cols-4 items-center gap-4 p-2 border-b border-gray-200">
                        <input type="checkbox" name="examenes[{{ $examen->id }}]" @change="examenes.push({{ $examen->id }})" class="form-checkbox">
                        <span>{{ $examen->cup }}</span>
                        <span>{{ $examen->nombre }}</span>
                        <span>${{ $examen->valor }}</span>
                    </div>
                @endforeach
               <input type="text" name="examenes_seleccionados" x-model="examenes" class="hidden">
            <button type="submit" class="btn btn-primary mt-4">Crear Orden</button>
        </section>
    </x-canva>
    @vite('resources/js/crearOrdenes.js')
</x-app-layout>

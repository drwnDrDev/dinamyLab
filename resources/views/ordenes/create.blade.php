<x-app-layout>
 <x-canva>
        <div class="section_paciente">
            <x-formPersona perfil="Paciente" :tipos_documento="$tipos_documento" />
        </div>
        <div x-data="{ open: false }">
            <label for="mostrarAcompaniante">Acompañante</label>
            <input type="checkbox" id="mostrarAcompaniante" @change="open = $event.target.checked">
            <div x-show="open" x-transition>
                <x-formPersona perfil="acompaniante" :tipos_documento="$tipos_documento" />
            </div>
        </div>
<form  method="post" id="crearOrden" action="{{ route('ordenes.store') }}" class="mt-4">
            @csrf
    <h2 class="font-bold mb-4 text-xl text-text">Datos de la Orden</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="w-full">
                <x-input-label for="numero_orden">Número de Orden</x-input-label>
                <x-text-input type="text" id="numero_orden" name="numero_orden" class="form-input w-32" value="{{$orden_numero}}" />
            </div>

            <div x-data="{ open: false }" class="w-full flex flex-wrap items-center gap-2">
                <label for="pago">Pago</label>
                <input type="checkbox" id="pago" name="pago" @change="open = !$event.target.checked" checked>
                <div x-show="open" x-transition>
                    <x-input-label for="abono">Abono</x-input-label>
                    <x-text-input type="number" id="abono" name="abono" value="0" class="form-input w-32"/>
                </div>
            </div>
        </div>
        <section class="section_examenes p-4" >
            <h2 class="font-bold mb-4 text-xl text-text">Exámenes</h2>
            <div>

                <x-input-label for="16000">16000</x-input-label>
                 <input type="checkbox" name="16000" id="16000">
                </div>
            <div>
                <x-input-label for="busquedaExamen">Buscar por nombre</x-input-label>
                <input type="text" id="busquedaExamen" placeholder="Buscar examen..." class="form-input w-full mb-2">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <span class="text-lg font-semibold" id="totalExamenes">Total: $ 0.00</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4" id="examenesContainer">


            </div>
               <input type="hidden" name="paciente_id" id="paciente_id" >
               <input type="hidden" name="acompaniante_id" id="acompaniante_id" >

            <div class="w-full p-4 border border-borders rounded-sm shadow-md my-4" x-data="{ open: false }">
            <label for="mostrarObservaciones">Observaciones</label>
            <input type="checkbox" id="mostrarObservaciones" @change="open = $event.target.checked">
            <div x-show="open" x-transition>
                <x-input-label for="observaciones">Observaciones</x-input-label>

            <textarea id="observaciones" name="observaciones" class="form-textarea w-full h-32" placeholder="Escribe aquí las observaciones..."></textarea>
            </div>
        </div>

            <x-primary-button id="enviarOrden" class="btn btn-primary mt-4">Crear Orden</x-primary-button>
            </form>
        </section>
    </x-canva>
    @vite('resources/js/crearOrdenes.js')
</x-app-layout>

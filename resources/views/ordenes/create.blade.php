<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-text leading-tight">
            {{ __('Nueva orden de laboratorio') }}
        </h2>
    </x-slot>
    <x-canva class="mb-4">
        <div class="section_paciente">
            <x-formPersona perfil="Paciente" />
        </div>
    </x-canva>
    <x-canva class="mb-4">
        <div x-data="{ open: false }">
            <label for="mostrarAcompaniante">Quieres agregar un acompañante</label>
            <input type="checkbox" id="mostrarAcompaniante" @change="open = $event.target.checked">
            <div x-show="open" x-transition>
                <x-formPersona perfil="acompaniante" />
            </div>
        </div>
    </x-canva>
    <x-canva class="mb-4">
        <form method="post" id="crearOrden" action="{{ route('ordenes.store') }}" class="mt-4">
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
                        <x-text-input type="number" id="abono" name="abono" value="0" class="form-input w-32" />
                    </div>
                </div>
            </div>

            <section class="section_examenes p-4">

                <h2 class="font-bold mb-4 text-xl text-text">Exámenes</h2>

                <div>
                    <x-input-label for="busquedaExamen">Buscar por nombre</x-input-label>
                    <input type="text" id="busquedaExamen" placeholder="Buscar examen..." class="form-input w-full mb-2">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    <span class="text-lg font-semibold" id="totalExamenes">Total: $ 0.00</span>
                </div>
                <div class="w-full" id="examenesContainer">

                </div>
                <input type="hidden" name="paciente_id" id="paciente_id">
                <input type="hidden" name="acompaniante_id" id="acompaniante_id">

                <div class="w-full p-4 border border-borders rounded-sm shadow-md my-4" x-data="{ open: false }">
                    <label for="mostrarObservaciones" x-show="!open" >Observaciones</label>
                    <input type="checkbox" id="mostrarObservaciones" @change="open = $event.target.checked">
                    <div x-show="open" x-transition>
                        <x-input-label for="observaciones">Observaciones</x-input-label>
                        <textarea id="observaciones" name="observaciones" class="form-textarea w-full h-32" placeholder="Escribe aquí las observaciones..."></textarea>
                    </div>
                </div>

                <x-primary-button id="enviarOrden" class="btn btn-primary mt-4">Crear Orden</x-primary-button>


        </form>

        </div>
        @if (session('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>


        @endif
        @if (session('error'))
        <div class="alert alert-error mt-4">
            {{ session('error') }}
        </div>
        @endif

    </x-canva>
    @vite('resources/js/main.js')
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Orden') }}
        </h2>
    </x-slot>
    <x-canva>
        <div class="bg-white">
            <h2 class="text-xl pb-4">Datos del Paciente</h2>
            <form action="" class="paciente_orden">
                <section class="paciente">
                    <div class="w-full pb-2 lg:max-w-60">
                        <x-input-label for="numero_documento">Número de documento</x-input-label>
                        <x-text-input type="number" id="numero_documento" name="numero_documento" required />
                    </div>

                    <div class="row-inputs pt-2 w-full md:flex justify-between gap-2">
                        <div class="w-full pb-2">
                            <x-input-label for="nombres">Nombres</x-input-label>
                            <x-text-input type="text" id="nombres" name="nombres" required />
                        </div>
                        <div class="w-full pb-2">
                            <x-input-label for="apellidos">Apellidos</x-input-label>
                            <x-text-input type="text" id="apellidos" name="apellidos" required />
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </x-canva>
</x-app-layout>
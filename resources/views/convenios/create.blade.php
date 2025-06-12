<x-app-layout >
    <x-slot name="title">
     Convenios
    </x-slot>
<x-slot name="header">

    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
       Crear Convenio
    </h2>
</x-slot>
<div class="py-12">
<!-- 
            $table->string('razon_social');
            $table->string('nit')->unique();
            $table->foreignId('contacto_id') -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('convenios.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <x-input-label for="razon_social" >Razón Social:</x-input-label>
                        <x-text-input type="text" name="razon_social" id="razon_social" required>
                    </div>
                    <div class="mb-4">
                        <x-input-label for="nit" class="block text-gray-700 text-sm font-bold mb-2">NIT:</x-input-label>
                        <input type="text" name="nit" id="nit" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                
                <hr class="my-8 border-1 border-borders">
        <div class="row-inputs pt-2 w-full md:flex justify-between gap-2">
            <div class="w-full pb-2">
                <x-input-label for="telefono">Teléfono</x-input-label>
                <x-text-input type="number" id="telefono" name="telefono" />
            </div>
            <div class="w-full pb-2">
                <x-input-label for="correo">Correo</x-input-label>
                <x-text-input type="email" id="correo" name="correo" placeholder="example@mail.com" />
            </div>

        </div>
        <div class="row-inputs pt-2 w-full md:grid md:grid-cols-3 gap-2">

            <div class="w-full pb-2 relative">
                <x-input-label for="ciudad">Ciudad</x-input-label>
                <x-text-input id="municipioBusqueda" />
                <div class="w-full absolute flex-col-reverse bottom-10 bg-background border border-borders max-h-60 overflow-y-auto
                        z-10 hidden rounded-md shadow-lg" id="opciones"></div>
                <select name="municipio" id="municipio" class="hidden">
                    <option value="11007">Bosa</option>
                </select>
            </div>

            <div class="w-full pb-2 md:col-span-2">
                <x-input-label for="direccion">Dirección</x-input-label>
                <x-text-input type="text" id="direccion" name="direccion" :required="false" />
            </div>

        </div>
                    <div class="row-inputs py-8 w-full flex justify-center gap-2">
                        <x-primary-button id="tipoGuardado" name="tipoGuardado" class="w-40">Guardar</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>



</x-app-layout>

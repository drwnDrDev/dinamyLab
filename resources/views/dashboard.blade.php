<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <section class="canva mx-auto sm:p-2 md:p-4 lg:p-8">
        <p>Hola! </p>
        <div class="flex gap-4 sm:flex-flow-wrapp">
            <div class="bg-violet-50 w-full p-4  rounded-xl shadow-md">
                <div class="h2 pb-4 border-b border-slate-200">
                    <h2 class="font-bold text-2xl">Ordenes Medicas</h2>
                </div>
                <div class="flex gap-2 py-4">
                    <div class="icono">*</div>
                    <div class="h3">En proceso</div>
                    <div class="text-2xl font-bold">15</div>
                </div>
                <div class="flex gap-2 py-4">
                    <div class="icono">*</div>
                    <div class="h3">Sin resultados</div>
                    <div class="text-2xl font-bold">15</div>
                </div>
                <div class="flex gap-2 py-4">
                    <div class="icono">*</div>
                    <div class="h3">Por Facturar</div>
                    <div class="text-2xl font-bold">15</div>
                </div>
            </div>
            <div class="bg-pink-50 w-full p-4 rounded-xl shadow-md">
                <div class="h2 pb-4 border-b border-slate-200">
                    <h2 class="font-bold text-2xl">Procedimientos</h2>
                </div>
                <div class="flex gap-2 py-4">
                    <div class="icono">*</div>
                    <div class="h3">En proceso</div>
                    <div class="text-2xl font-bold">15</div>
                </div>
                <div class="flex gap-2 py-4">
                    <div class="icono">*</div>
                    <div class="h3">Sin resultados</div>
                    <div class="text-2xl font-bold">15</div>
                </div>
                <div class="flex gap-2 py-4">
                    <div class="icono">*</div>
                    <div class="h3">Por Facturar</div>
                    <div class="text-2xl font-bold">15</div>
                </div>
            </div>

        </div>
        <!-- otras cosas -->
        
            <div class="py-4">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="p-2 bg-slate-300">{{$empleado->sede->empresa->nombre_comercial}}</p>
                        <p class="p-2 bg-slate-300">{{$empleado->persona->nombreCompleto()}}</p>
                        {{$empleado->cargo}}
                    </div>
                </div>
            </div>

            <x-modal name="car">
                <h1>Modal</h1>
                <p>
                    Cual es esta ruta?

                </p>
            </x-modal>

        
    </section>


</x-app-layout>
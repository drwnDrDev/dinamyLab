<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-text leading-tight">
            Dashboard
        </h2>
    </x-slot>

   <x-canva>

        <div class="flex gap-4 sm:flex-flow-wrapp dark:text-slate-50 dark:bg-slate-950 p-4 rounded-xl">
            <div class="bg-violet-50 w-full p-4 dark:bg-stone-800 rounded-xl shadow-md">
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
            <div class="bg-pink-50 w-full p-4 rounded-xl shadow-md dark:bg-slate-800">
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
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-slate-950">
                    <div class="p-6 text-gray-900">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-slate-900">
                    <div class="p-6 text-gray-900">
                        <p class="p-2 bg-slate-300 dark:bg-slate-700">{{$empleado->empresa->nombre_comercial}}</p>

                        {{$empleado->cargo}}
                    </div>
                </div>
            </div>

</x-canva>

    @vite('resources/js/obtenerStaticos.js')
</x-app-layout>

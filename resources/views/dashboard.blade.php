<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
      
    </div>
</x-app-layout>

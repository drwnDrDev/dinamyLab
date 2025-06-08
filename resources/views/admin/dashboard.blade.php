<x-app-layout>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>

    </x-slot>

        Procedimientos Pendientes:
        <div class="flex flex-col gap-1 overflow-hidden shadow-sm sm:rounded-lg">
            @foreach ($pendientes as $procedimiento)
            <div class="grid grid-cols-7">
                <p class="p-1 col-span-3 bg-slate-100 hover:bg-violet-300 rounded-md" >{{ $procedimiento->examen->nombre }} - {{ $procedimiento->fecha}}</p>
                <a class="bg-green-100 hover:bg-green-300 p-2" href="">Ir a reultado</a>
                <a class="bg-orange-100 hover:bg-orange-300 p-2" href="">Pendiente por muestra</a>
                <a class="bg-red-100 hover:bg-red-300 p-2" href="">Anulado</a>
            </div>
            @endforeach
        </div>
   
     @vite('resources/js/obtenerStaticos.js')
</x-app-layout>

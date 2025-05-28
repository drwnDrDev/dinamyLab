<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           {{ __('Medical order') }}
        </h2>
    </x-slot>
    <x-canva>        
        <div class="flex justify-between items-center mb-4">
                <a href="{{ route('ordenes.create') }}" class="p-4 bg-slate-300 text-2xl rounded-md hover:bg-blue-300">Nueva orden</a>
        </div>

        <div class="grid grid-cols-5">
            <p >Orden</p><p class="col-span-2">progreso</p><p class="col-span-2">fecha estimada de entrega</p>
        </div>
        <a href=""  class="grid grid-cols-5">
            <p class="text-primary">#12345</p>
            <p class="col-span-2">

                <progress id="progrso" max="100" value="70" class="text-red-600" >70%</progress>

            </p>
            <p class="col-span-2">2023-10-31</p>

        </a>

    </x-canva>
</x-app-layout>
<x-app-layout>
    <x-slot name="title">
        Procedimientos
    </x-slot>
    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Procedimientos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                
                @foreach ($procedimientos as $procedimiento)
                <div class="flex gap-4 p-6 bg-white border-b border-gray-200">
            

                    <p>{{ $procedimiento->id}}</p>
                    <p>{{ $procedimiento->orden->id }}</p> 
                    <p>{{ $procedimiento->orden->paciente->nombres()}}</p>
                    <p>{{ $procedimiento->descripcion }}</p> 
                    <p class="bg-red-100">{{ $procedimiento->estado }}</p>



                </div>
                 @endforeach
            </div>
        </div>
   
</x-app-layout>
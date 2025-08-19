<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">
           {{ __('Medical order') }} #{{$orden->numero}}
        </h2>
    </x-slot>
    <x-canva>
        <div class=" items-center mb-4">
                <a href="{{ route('ordenes.create') }}" class="p-4 bg-secondary rounded-md hover:bg-primary">Nueva orden</a>
        </div>
    <div class="container" id="ticket">
        <div class="grid grid-cols-5 border border-borders rounded-md">
            <div class="col-span-2 p-2">
                <p class="text-titles">Paciente</p>
                <p class="text-text">{{$orden->paciente->nombres()}} {{$orden->paciente->apellidos()}}</p>
                <p class="text-titles">Identificacion</p>
                <p class="text-text">{{$orden->paciente->tipo_documento}} {{$orden->paciente->numero_documento}}</p>
            </div>


            <p class="text-titles">#{{$orden->numero}} Inicio de Procedimiento {{$orden->created_at}}</p>

        </div>

        <div class="grid grid-cols-5 p-2 justify-center border border-borders">
           <p class="col-span-2">{{$orden->updated_at}}</p>
            <div class="col-span-4 grid grid-cols-4">
                @dump($orden->examenes)
            </div>

        </div>

    </div>

    </x-canva>

</x-app-layout>

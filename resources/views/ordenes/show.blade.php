<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">
           {{ __('Medical order') }}
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
           
        @if($orden->acompaniante)
            <div class="col-span-3 p-2">
                <p class="text-titles">Acompañante</p>
                <p class="text-text">{{$orden->acompaniante->nombres()}} {{$orden->acompaniante->apellidos()}}</p>
                <p class="text-titles">Identificacion</p>
                <p class="text-text">{{$orden->acompaniante->tipo_documento}} {{$orden->acompaniante->numero_documento}}</p>
            </div>
        @endif
            <p class="text-titles">#{{$orden->numero}} Inicio de Procedimiento {{$orden->created_at}}</p>

        </div>
       
        <div class="grid grid-cols-5 p-2 justify-center border border-borders">
           
            <div class="col-span-3 grid grid-cols-3">
                @foreach ($orden->procedimientos as $item)
                
                <p>{{$item->examen->nombre}}</p> 
                <p>1</p>
                <p>{{$item->examen->valor}} </p>
                @endforeach
            </div>
            <p class="col-span-2">{{$orden->updated_at}} {{$orden->estado}}</p>
        </div>
       
    </div>
    <x-primary-button type="button" class="mt-4" id="imprimir" >
        {{ __('Print') }}
    </x-primary-button>

    </x-canva>
    @vite(['resources/js/ticket.js'])
</x-app-layout>

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

        <div class="grid grid-cols-5 border border-borders rounded-md">
            <p >Orden</p><p class="col-span-2">PASAR RESULTAODS</p><p class="col-span-2">fecha estimada de entrega</p>
        </div>
        @foreach ($ordenes as $orden)
        <div class="grid grid-cols-5 p-2 justify-center border border-borders">
            <p class="text-titles">#{{$orden->numero}}</p>
            <div class="col-span-2 grid">
                @foreach ($orden->procedimientos as $item)
                
                <a href="{{route('procedimientos.show',$item)}}"  class=" hover:bg-gray-100 border-b border-gray-200" >  {{$item->examen->nombre}} </a>
                @endforeach
            </div>
            <p class="col-span-2">{{$orden->updated_at}} {{$orden->estado}}</p>
        </div>
        @endforeach


    </x-canva>
</x-app-layout>

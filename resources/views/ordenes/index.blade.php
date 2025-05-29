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
            <p >Orden</p><p class="col-span-2">PASAR RESULTAODS</p><p class="col-span-2">fecha estimada de entrega</p>
        </div>
        @foreach ($ordenes as $orden)
        <div class="grid grid-cols-5 p-2">
            <p class="text-primary">#{{$orden->numero}}</p>
            <div class="col-span-2 grid">
                @foreach ($orden->examenes as $item)
                <a href="{{route('resultados.create',[$orden,$item->id])}}"  class=" hover:bg-gray-100 border-b border-gray-200" >  {{$item->nombre}} </a>
                @endforeach
            </div>
            <p class="col-span-2">{{$orden->updated_at}} {{$orden->estado}}</p>
        </div>
        @endforeach


    </x-canva>
</x-app-layout>

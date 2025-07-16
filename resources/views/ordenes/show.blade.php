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
                <p class="text-text">{{$orden->paciente->tipo_documento->cod_rips}} {{$orden->paciente->numero_documento}}</p>
            </div>


            <p class="text-titles">#{{$orden->numero}} Inicio de Procedimiento {{$orden->created_at->format('d-m-Y') }}</p>

        </div>


        <div class="grid grid-cols-5 p-2 justify-center border border-borders">
           <p class="col-span-2">{{$orden->updated_at}} </p>
            <div class="col-span-4 grid grid-cols-4">


                @foreach ($orden->examenes as $examen)
                <p>{{$examen->nombre}}</p>
                <p>{{$examen->pivot->cantidad}}</p>
                <p>{{$examen->valor}}</p>
                <p>{{$examen->valor*$examen->pivot->cantidad}}</p>


                @endforeach
            </div>
            
        </div>    

    </div>
    <div class="print:hidden my-3 flex overflow-hidden rounded-xl border border-borders bg-background">
            <table class="flex-1">
                <thead>

                    <tr class="bg-background">
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Fecha</th>
                        <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Exámen</th>
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Estado</th>
                    </tr>
                </thead>
                <tbody>

                        @if ($orden->procedimientos->isEmpty())
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-titles text-sm font-normal leading-normal">
                                    No hay procedimientos en proceso.
                                </td>
                            </tr>

                        @else
                         @foreach ($orden->procedimientos as $procedimiento)

                        <tr data-url="{{ route('procedimientos.show', $procedimiento) }}" onclick="window.location.href=this.dataset.url" class="cursor-pointer border-t border-borders hover:bg-secondary">
                            <td class="px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                                {{ $procedimiento->created_at->format('Y-m-d') }}
                            </td>                            
                            <td class="px-4 py-2 w-60 text-titles text-sm font-normal leading-normal">
                                {{ $procedimiento->examen->nombre }}
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                                {{ $procedimiento->estado }}
                            </td>
                        </tr>

                            @endforeach
                        @endif

                </tbody>
            </table>
        </div>

    <x-primary-button type="button" class="mt-4" id="imprimir" >
        {{ __('Print') }}
    </x-primary-button>

    </x-canva>
    @vite(['resources/js/ticket.js'])
</x-app-layout>

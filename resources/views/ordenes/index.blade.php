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

        <div class="py-3">
            <label class="flex flex-col min-w-40 h-12 w-full">
                <div class="flex w-full flex-1 items-stretch rounded-xl h-full">
                    <div
                        class="text-titles flex border-none bg-secondary items-center justify-center pl-4 rounded-l-xl border-r-0"
                        data-icon="MagnifyingGlass"
                        data-size="24px"
                        data-weight="regular">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                            <path
                                d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>
                        </svg>
                    </div>
                    <input
                        placeholder="Search by patient name or report ID"
                        class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-text focus:outline-0 focus:ring-0 border-none bg-secondary focus:border-none h-full placeholder:text-titles px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                        value="" />
                </div>
            </label>
        </div>

        <h2 class="text-text text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Órdenes recientes</h2>
        <div class="my-3 flex overflow-hidden rounded-xl border border-borders bg-background">
            <table class="flex-1">
                <thead>

                    <tr class="bg-background">
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Fecha</th>
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Orden Médica</th>
                        <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Paciente</th>
                        <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Procedimientos</th>
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ordenes as $orden)
                    <tr class="border-t border-borders">
                        <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                            {{ $orden->created_at->format('Y-m-d') }}
                        </td>
                        <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                            <a href="{{route('ordenes.show',$orden)}}" class="text-titles">{{$orden->numero}}</a>
                        </td>
                        <td class="content-start px-4 py-2 w-60 text-sm font-normal leading-normal">
                            {{ $orden->paciente->nombreCompleto() }}
                        </td>
                            
                            <td class="flex flex-col gap-2 px-4 py-2 w-60 text-titles text-sm font-normal leading-normal">
                                <!-- no me di mañana de traer el nombre del examen -->
                                 @foreach ($orden->examenes as $item)
                                    <p>{{$item->nombre}} - {{$item->pivot->cantidad}}</p>
                                 @endforeach
                                    
                            <!-- </td>
                            <td class="grid py-2 w-40 text-titles text-sm font-normal leading-normal">
                             <label for="pendiente">pendiente<meter min="0" max="100" low="10" high="3" optimum="0"  value="{{$orden->pendiente}}"></meter></label>
                             <label for="proceso">en proceso<meter min="0" max="100" low="66" high="33" optimum="0"  value="{{$orden->proceso}}"></meter></label>
                             <label for="entreagdo">entreagdo<meter min="0" max="100" low="66" high="33" optimum="0"  value="{{$orden->entreagdo}}"></meter></label>
                             <label for="terminado">terminado<meter min="0" max="100" low="45" high="66" optimum="100"  value="{{$orden->terminado}}"></meter></label>
                             <label for="anulado">anulado<meter min="0" max="100" low="0" high="0" optimum="0"  value="{{$orden->anulado}}"></meter></label>                    
                            </td> -->
                                
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

    </x-canva>
</x-app-layout>
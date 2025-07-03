<x-app-layout>

    <x-slot name="header" titulo="Persona">

    </x-slot>
    @if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4">
        {{ session('success') }}
    </div>
    @endif
    <x-canva>
        <div class="py-4">
            <p class="text-2xl font-bold leading-tight tracking-[-0.015em]">{{ $persona->nombreCompleto() }}</p>
            <p class="text-titles">Paciente ID: {{ $persona->numero_documento }}</p>
        </div>
        <div class="py-4">
            <div class="flex border-b border-borders pl-2 gap-8" id="info">
                <a class="flex flex-col items-center justify-center border-b-4  border-primary py-3" href="#info">
                    <p class="font-bold tracking-[0.015em]">Personal</p>
                </a>
                <a class="flex flex-col items-center justify-center border-b-4 border-b-transparent text-titles py-3" href="#historia">
                    <p class="text-titles  font-bold leading-normal tracking-[0.015em]">Historia</p>
                </a>
                <a class="flex flex-col items-center justify-center border-b-4 border-b-transparent text-titles py-3" href="#examenes">
                    <p class="text-titles  font-bold leading-normal tracking-[0.015em]">Examenes</p>
                </a>
            </div>
        </div>
        <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 pl-0">Información del Paciente</h2>
        <div class="py-4 grid grid-cols-2" id="info">
            <div class="flex flex-col gap-1 border-t border-solid border-t-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">Fecha de Nacimiento</p>
                <p class=" font-normal leading-normal">{{ $persona->fecha_nacimiento->format('d/m/Y') }}</p>
            </div>
            <div class="flex flex-col gap-1 border-t border-solid border-t-borders py-4 pl-2">
                <p class="text-titles  font-normal leading-normal">Sexo</p>
                <p class=" font-normal leading-normal">{{$persona->sexo}}</p>
            </div>
            <div class="flex flex-col gap-1 border-t border-solid border-t-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">Telefonos</p>

                <p class=" font-normal leading-normal">
                  @if($persona->telefonos->isEmpty())
                    No tiene telefonos registrados
                  @else
                    @foreach($persona->telefonos as $telefono)
                     <span>{{ $telefono->numero}} </span>
                    @endforeach
                  @endif

                </p>
            </div>
            @if ( $persona->direccion)


            <div class="flex flex-col gap-1 border-t border-solid border-t-borders py-4 pl-2">
                <p class="text-titles  font-normal leading-normal">Muncipio</p>
                <p class=" font-normal leading-normal">{{ $persona->direccion->municipio->departamento }}-{{ $persona->direccion->municipio->municipio }}</p>
            </div>

            <div class="flex flex-col gap-1 border-t border-solid border-t-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">Dirección</p>
                <p class=" font-normal leading-normal">{{ $persona->direccion->direccion}}</p>
            </div>
              @endif
            @if($persona->afiliacionSalud)
            <div class="flex flex-col gap-1 border-t border-solid border-t-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">Eps</p>
                <p class=" font-normal leading-normal">{{ $persona->afiliacionSalud->eps}}</p>
            </div>
            @endif


        </div>
        <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] py-4">Historia Clínica</h2>
        <div class="py-4" id="historia">
            <div class="flex overflow-hidden rounded-xl border border-borders">
                <table class="flex-1">
                    <thead>
                        <tr class="">
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Date')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Test')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Status')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Result')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @isset($procedimientos['terminado'])


                    @foreach ($procedimientos['terminado'] as $procedimiento)


                        <tr class="border-t border-t-borders">
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                {{$procedimiento->fecha}}
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                               {{$procedimiento->examen->nombre}}
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                <button
                                    class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-8 px-4 bg-[#e7f2f3]  font-medium leading-normal w-full">
                                    <span class="truncate">Entregado</span>
                                </button>
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">Normal</td>
                        </tr>

                    @endforeach
                    @else
                    <tr><td>No hay resultados que mostrar</td> </tr>

                @endisset

                    </tbody>
                </table>
            </div>
        </div>



    <section class="otra_info  mt-6">
        <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] py-4">Examenes pendientes por resiltado</h2>
        <div class="py-4" id="historia">
            <div class="flex overflow-hidden rounded-xl border border-borders">
                <table class="flex-1">
                    <thead>
                        <tr class="">
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Date')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Test')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Status')}}</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">{{__('Result')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                @isset($procedimientos['en proceso'])


                    @foreach ($procedimientos['en proceso'] as $procedimiento)


                        <tr class="border-t border-t-borders">
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                {{$procedimiento->fecha}}
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                               {{$procedimiento->examen->nombre}}
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                <button
                                    class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-8 px-4 bg-[#e7f2f3]  font-medium leading-normal w-full">
                                    <span class="truncate">Entregado</span>
                                </button>
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">Normal</td>
                        </tr>

                    @endforeach
                @else
                        <tr>
                            No existen registros aun
                        </tr>
                @endisset

                    </tbody>
                </table>
            </div>
        </div>


        </section>
    </x-canva>

</x-app-layout>

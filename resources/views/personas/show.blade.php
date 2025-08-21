<x-app-layout>


    @if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4">
        {{ session('success') }}
    </div>
    @endif
    <x-canva>
        <div class="py-4 flex justify-between items-center">
            <div>
                <p class="text-2xl font-bold leading-tight tracking-[-0.015em]">{{ $persona->nombreCompleto() }}</p>
                <p class="text-titles">Paciente ID: {{ $persona->numero_documento }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('personas.edit', $persona) }}">
                    <x-primary-button>Editar</x-primary-button>
                </a>
                <a href="{{route('facturas.create',$persona)}}">
                    <x-secondary-button>Facturar</x-secondary-button>
                </a>
                <a href="{{route('resultados.historia',$persona)}}">
                    <x-secondary-button>Resultados</x-secondary-button>
                </a>
            </div>

        </div>

        <div class="flex border-b border-borders">
            <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 border-b-4 border-primary">Información del Paciente</h2>
        </div>

        <div class="py-4 grid grid-cols-2" id="info">
            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">Fecha de Nacimiento</p>
                <p class=" font-normal leading-normal">{{ $persona->fecha_nacimiento->format('d/m/Y') }}</p>
            </div>
            <div class="flex flex-col gap-1 border-b border-borders py-4 pl-2">
                <p class="text-titles  font-normal leading-normal">Sexo</p>
                <p class=" font-normal leading-normal">{{$persona->sexo==='M' ? 'Masculino':'Femenino'}}</p>
            </div>
            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
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

            <!-- se debe procurar mostrar todos los elementos asi no existan datos -->



            <div class="flex flex-col gap-1 border-b border-borders py-4 pl-2">
                <p class="text-titles  font-normal leading-normal">Muncipio</p>
                <p class=" font-normal leading-normal">{{ $persona->direccion->municipio->municipio }}-{{ $persona->direccion->municipio->departamento }}</p>
            </div>

            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">Dirección</p>
                <p class=" font-normal leading-normal">{{ $persona->direccion->direccion}}</p>
            </div>


            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">EPS</p>
                <p class=" font-normal leading-normal">{{ optional($persona->afiliacionSalud)->eps?? 'Sin Información'}}</p>
            </div>
        </div>

        <div class="flex border-b border-borders">
        <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 border-b-4 border-primary">Historia Clínica</h2>
        </div>
        <div class="py-4" id="historia">
            <div class="flex overflow-hidden rounded-xl border border-borders">
                @isset($procedimientos['terminado'])
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
                        @foreach ($procedimientos['terminado'] as $procedimiento)

                        <tr class="border-t border-borders hover:bg-secondary">
                            <td class="px-3 py-4"><span class="w-40">{{ $procedimiento->fecha}}</span></td>
                            <td class="px-3 py-4"><span class="w-40">{{ $procedimiento->orden_id }}</span></td>
                            <td class="px-3 py-4"><span class="w-60">{{ $procedimiento->examen->nombre }}</span></td>
                            <td class="px-3 py-4">
                                <a href="{{ route('resultados.show', $procedimiento) }}">
                                    <span class="w-40 text-titles">{{ $procedimiento->estado }}</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else

                <p class="self-center p-4">No existen registros aun</p>

                @endisset
            </div>
        </div>

        <section class="otra_info  mt-6">

        <div class="flex border-b border-borders">
        <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 border-b-4 border-primary">Examenes en Proceso</h2>
        </div>
            <div class="py-4" id="historia">
                <div class="flex overflow-hidden rounded-xl border border-borders">
                    @if(isset($procedimientos['en proceso']) && $procedimientos['en proceso']->count() > 0)
                    <table class="flex-1">
                        <thead>
                            <tr class="">
                                <th class="p-2 border border-spacing-1 border-stone-900 text-text w-40 text-sm font-medium leading-normal">{{__('Date')}}</th>
                                <th class="p-2 border border-spacing-1 border-stone-900 text-text w-40 text-sm font-medium leading-normal">{{__('Order')}}</th>
                                <th class="p-2 border border-spacing-1 border-stone-900 text-text w-40 text-sm font-medium leading-normal">{{__('Procedure')}}</th>
                                <th class="p-2 border border-spacing-1 border-stone-900 text-text w-40 text-sm font-medium leading-normal">{{__('Status')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($procedimientos['en proceso'] as $procedimiento)

                            <tr class="border-t border-borders hover:bg-secondary">
                                <td colspan="4" class="p-0">
                                    <a href="{{ route('resultados.create', $procedimiento) }}" class="flex w-full h-full cursor-pointer px-4 py-2 text-inherit no-underline">
                                        <span class="w-40">{{ $procedimiento->created_at->format('Y-m-d') }}</span>
                                        <span class="w-40 text-center">{{ $procedimiento->orden_id }}</span>
                                        <span class="w-60 text-center">{{ $procedimiento->examen->nombre }}</span>
                                        <span class="w-40 text-end">{{ $procedimiento->estado }}</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="block text-center p-4">No existen registros aun</p>
                    @endif
                </div>
            </div>
        </section>
    </x-canva>
</x-app-layout>

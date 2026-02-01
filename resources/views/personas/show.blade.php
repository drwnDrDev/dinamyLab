<x-app-layout>
    @if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4">
        {{ session('success') }}
    </div>
    @endif
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-text leading-tight">
            {{ __('Historia') }}
        </h2>
    </x-slot>
    <x-canva>
        <div class="py-4 flex justify-between items-center">
            <div>
                <p class="text-2xl font-bold leading-tight tracking-[-0.015em]">{{ $persona->nombreCompleto() }}</p>
                <p class="text-titles">
                 <span id="tipo-documento">{{ $persona->tipo_documento->nombre }}</span>
                 <span id="numero-documento">{{ $persona->numero_documento }}</span>
                </p>
            </div>
            <div class="flex gap-2">

                    <x-primary-button id="editar-button">Editar</x-primary-button>

                <a href="{{route('ordenes.create',$persona)}}">
                    <x-secondary-button>Nueva Orden</x-secondary-button>
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
                <p class=" font-normal leading-normal" id="fecha-nacimiento">{{ $persona->fecha_nacimiento ? $persona->fecha_nacimiento->format('d/m/Y') : 'Actualizar Fecha de Nacimiento' }}
                <span class=" font-ubuntu leading-normal text-sm text-blue-900" id="edad">
                    @if($persona->fecha_nacimiento)
                       ( {{ $persona->edad() }})
                    @endif
                </span>
                </p>
            </div>
            <div class="flex flex-col gap-1 border-b border-borders py-4 pl-2">
                <p class="text-titles  font-normal leading-normal">Sexo</p>
                <p class=" font-normal leading-normal" id="sexo">
                    @switch($persona->sexo)
                        @case('M')
                            Masculino
                            @break

                        @case('F')
                            Femenino
                            @break

                        @default
                            Intesexual
                    @endswitch
                </p>
            </div>
            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">Telefonos</p>

                <p class="font-normal leading-normal" id="telefonos">
                    @if($persona->telefonos->isEmpty())
                    No tiene telefonos registrados
                    @else
                    @foreach($persona->telefonos as $telefono)
                    <span>{{ $telefono->numero}}</span>
                    @endforeach
                    @endif

                </p>
            </div>

            <!-- se debe procurar mostrar todos los elementos asi no existan datos -->

    @isset($persona->direccion)
        <div class="flex flex-col gap-1 border-b border-borders py-4 pl-2">
            <p class="text-titles  font-normal leading-normal">Muncipio</p>
            <p class=" font-normal leading-normal">{{ $persona->direccion->municipio->municipio ?? 'Sin Municipio' }}-{{ $persona->direccion->municipio->departamento ?? 'Sin Departamento' }}</p>
        </div>

        <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
            <p class="text-titles  font-normal leading-normal">Dirección</p>
            <p class=" font-normal leading-normal">{{ $persona->direccion->direccion ?? 'Sin Dirección' }}</p>
        </div>
    @else
    <form method="POST" id="direccion-form" class="max-w-md">
        @csrf
        <div class="flex flex-col gap-1 border-b border-borders py-4 pl-2">
            <p class="text-titles  font-normal leading-normal">Muncipio</p>
            <input type="text" name="municipio"
                    class="border-b border-borders py-2"
                    placeholder="Ingrese el municipio">
        </div>

        <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
            <p class="text-titles  font-normal leading-normal">Dirección</p>
            <input  type="text" name="direccion"
                    class="border-b border-borders py-2"
                    placeholder="Ingrese la dirección">
        </div>
    </form>
    @endisset
            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">EPS</p>
                <p class=" font-normal leading-normal" id="eps">{{ optional($persona->afiliacionSalud)->eps?? 'Sin Información'}}</p>
            </div>
            <div>
                <p class="text-titles  font-normal leading-normal">Tipo de Afiliación</p>
                <p class=" font-normal leading-normal" id="tipo-afiliacion">{{optional($persona->afiliacionSalud)->tipoAfiliacion->codigo ?? NULL}} - {{ optional($persona->afiliacionSalud)->tipoAfiliacion->descripcion ?? 'Sin Información'}}</p>
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

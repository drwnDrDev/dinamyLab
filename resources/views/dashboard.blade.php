<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-text leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <x-canva class="bg-transparent border-none shadow-none">

        <div class="flex justify-between items-center w-full p-6 text-gray-900">
            <p class="text-4xl font-bold">Hola {{$empleado->user->name}}</p>
            <p class="text-lg">{{$empleado->cargo}}</p>
        </div>

        @php

        $totalProcedimientos = $procedimientos->count();
        $pendientes = $procedimientosByEstado->where('estado', 'en proceso')->sum('count');


        @endphp


        <div class="dashboard w-full grid md:grid-cols-3 md:grid-row-3 gap-2">

            <div class="card bg-background w-full p-4 dark:bg-stone-800 rounded-xl shadow-md row-span-1">
                <div class="h2 pb-4">
                    <h2 class="text-neutral-600 font-semibold">Procedimientos</h2>
                </div>
                <div class="flex gap-4 items-center">
                    <div class="p-4 bg-gradient-radial from-primary/50 to-primary rounded-full">
                        <x-iconos.procedimientos :active="false" class="text-white" />
                    </div>
                    <div class="w-full pl-4">
                        <span class="text-4xl font-bold">{{$procedimientos->count()}}</span>
                    </div>
                </div>

            </div>

            <div class="card bg-background w-full p-4 rounded-xl shadow-md dark:bg-slate-800 row-span-1">
                <div class="h2 pb-4">
                    <h2 class="text-neutral-600 font-semibold">Pacientes</h2>
                </div>
                <div class="flex gap-4 items-center">
                    <div class="p-4 bg-gradient-radial from-teal-500/50 to-teal-500 rounded-full">
                        <x-iconos.personas :active="false" class="text-white" />
                    </div>
                    <div class="w-full pl-4">
                        <span class="text-4xl font-bold">{{$pacientesHoy}}</span>
                    </div>
                </div>
            </div>

            <div class="card w-full p-4 bg-background rounded-xl row-span-3 shadow-md dark:bg-slate-800">
                <div class="h2 pb-4">
                    <h2 class="text-neutral-600 font-semibold">En Proceso</h2>
                </div>
                <div class="flex gap-4 items-center">
                    <div class="p-4 bg-gradient-radial from-rose-500/50 to-pink-400 rounded-full">
                        <x-iconos.resultados :active="false" class="text-white" />
                    </div>
                    <div class="w-full pl-4">
                        @foreach ($procedimientosByEstado as $estado)
                        <span class="text-4xl font-bold">
                            {{ $estado['count'] }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @foreach ($procedimientosByExamen as $examen)
                <div class="w-full mt-2">
                    <div class="border w-full h-4 rounded-md ">
                        <div class="rounded-md bg-gradient-radial from-rose-300 to-pink-400 h-full" style="width: {{$examen['count'] / $totalProcedimientos * 100}}%">
                        </div>
                    </div>
                    <p class="text-sm text-neutral-500">
                        {{$examen['count']}}
                        {{$examen['examen']}}

                    </p>
                </div>
                @endforeach
            </div>
            <div class="card w-full p-4 bg-background rounded-xl col-span-2 row-span-2 shadow-md dark:bg-slate-800">
                <div class="h2 pb-4">
                    <h2 class="text-neutral-600 font-semibold">Ordenes Recientes</h2>
                </div>
                <table class="w-full text-neutral-700 text-xs">
                    <thead>
                        <tr class="text-left text-sm font-medium text-neutral-500 border-b">
                            <th class="py-2">N.</th>
                            <th class="py-2">Fecha</th>
                            <th class="py-2">Paciente</th>
                            <th class="py-2">Examenes</th>
                            <th class="py-2">Estado</th>
                        </tr>
                    <tbody>
                        @foreach ($ordenes as $orden)

                        <tr class=" hover:bg-slate-100 dark:hover:bg-slate-700">
                            <td class="p-2 py-1">
                                {{$orden->numero}}
                            </td>
                            <td class="p-2 py-1">
                                {{$orden->created_at->format('d-m-Y')}}
                            </td>
                            <td class="p-2 py-1">
                                {{$orden->paciente->primer_nombre}}
                                {{$orden->paciente->primer_apellido}}
                            </td>
                            <td class="p-2 py-1">
                                {{$orden->procedimientos->count()}}
                            </td>
                            <td class="p-2 py-1 text-center">
                                <a href="{{ route('ordenes.show', $orden->numero)}}">
                                    <button class="w-auto text-center rounded-xl p-4 py-1 bg-rose-300">
                                        {{$orden->terminada ? 'Terminada' : 'Pendiente'}}
                                    </button>
                                </a>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- otras cosas -->
        </div>

    </x-canva>

    @vite('resources/js/obtenerStaticos.js')
</x-app-layout>
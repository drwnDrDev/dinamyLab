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
                <p class="text-titles  font-normal leading-normal">Telefono</p>
                <p class=" font-normal leading-normal">{{ $persona->contacto->telefono }}</p>
            </div>
            <div class="flex flex-col gap-1 border-t border-solid border-t-borders py-4 pl-2">
                <p class="text-titles  font-normal leading-normal">Muncipio</p>
                <p class=" font-normal leading-normal">{{ $persona->contacto->municipio->departamento }}-{{ $persona->contacto->municipio->municipio }}</p>
            </div>
            @if ($persona->contacto->infoAdicional('email')->first())
            <div class="flex flex-col gap-1 border-t border-solid border-t-borders py-4 pr-2">
                <p class="text-titles  font-normal leading-normal">Dirección</p>
                <p class=" font-normal leading-normal">{{ $persona->contacto->infoAdicional('email')->first()->valor}}</p>
            </div>
            @endif
 
        </div>
        <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] py-4">Historia Clínica</h2>
        <div class="py-4" id="historia">
            <div class="flex overflow-hidden rounded-xl border border-borders">
                <table class="flex-1">
                    <thead>
                        <tr class="">
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Date</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Test</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Status</th>
                            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-t-borders">
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                2023-07-20
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                Cuadro Hematológico
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                <button
                                    class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-8 px-4 bg-[#e7f2f3]  font-medium leading-normal w-full">
                                    <span class="truncate">Entregado</span>
                                </button>
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">Normal</td>
                        </tr>
                        <tr class="border-t border-t-borders">
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                2023-07-20
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                Cuadro Hematológico
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                <button
                                    class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-8 px-4 bg-[#e7f2f3]  font-medium leading-normal w-full">
                                    <span class="truncate">Entregado</span>
                                </button>
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">Normal</td>
                        </tr>
                        <tr class="border-t border-t-borders">
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                2023-07-20
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                Cuadro Hematológico
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                <button
                                    class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-8 px-4 bg-[#e7f2f3]  font-medium leading-normal w-full">
                                    <span class="truncate">Entregado</span>
                                </button>
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">Normal</td>
                        </tr>
                        <tr class="border-t border-t-borders">
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                2023-07-20
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                Cuadro Hematológico
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">
                                <button
                                    class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-8 px-4 bg-[#e7f2f3]  font-medium leading-normal w-full">
                                    <span class="truncate">Entregado</span>
                                </button>
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm">Normal</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] py-4">Examenes</h2>
        <div class="py-4" id="examenes">
            <div class="flex overflow-hidden rounded-xl border border-borders">
                @if($persona->historialClinico)
                <!-- aqui iria una tabla como la de arriba -->
                <ul class="list-disc pl-5">
                    @foreach($persona->historialClinico as $historia)
                    <li>{{ $historia->descripcion }} - {{ $historia->fecha->format('d/m/Y') }}</li>
                    @endforeach
                </ul>
                @else
                <p class="py-6 px-12 m-auto my-4 text-center bg-secondary rounded-xl">No hay historial clínico disponible.</p>
                @endif
            </div>
        </div>



        <section class="otra_info hidden mt-6">
            <div class="bg-white shadow-md rounded-lg p- ">
                <h3 class="text-lg font-semibold mb-4">Resultados de Exámenes</h3>
                @if($persona->resultadosExamenes)
                <ul class="list-disc pl-5">
                    @foreach($persona->resultadosExamenes as $resultado)
                    <li>{{ $resultado->examen->nombre }} - Resultado: {{ $resultado->resultado }} - Fecha: {{ $resultado->fecha->format('d/m/Y') }}</li>
                    @endforeach
                </ul>
                @else
                <p>No hay resultados de exámenes disponibles.</p>
                @endif
            </div>
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Procedimientos Pendientes</h3>
                @if($persona->procedimientosPendientes)
                <ul class="list-disc pl-5">
                    @foreach($persona->procedimientosPendientes as $procedimiento)
                    <li>{{ $procedimiento->examen->nombre }} - Fecha: {{ $procedimiento->fecha->format('d/m/Y') }}</li>
                    @endforeach
                </ul>
                @else
                <p>No hay procedimientos pendientes.</p>
                @endif
            </div>
        </section>
    </x-canva>

</x-app-layout>
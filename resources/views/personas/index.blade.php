<x-app-layout>
    <x-canva>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Pacientes recientes</h2>
            <a href="{{ route('personas.create') }}" class="bg-primary text-white px-4 py-2 rounded hover:bg-titles">
                Nuevo Paciente
            </a>
        </div>
 
        <div class="my-3 flex overflow-hidden rounded-xl border border-borders bg-background">
            <table class="flex-1">
                <thead>

                    <tr class="bg-background">
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Fecha de Registro</th>
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Número de Documento</th>
                        <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Nombres</th>
                        <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Nacionalidad</th>
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Edad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($personas as $paciente)
                    <tr class="border-t border-borders">
                        <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                            {{ $paciente->created_at->format('Y-m-d') }}
                        </td>
                        <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                            <a href="{{route('personas.show',$paciente)}}" class="text-titles">{{$paciente->numero_documento}}</a>
                        </td>
                        <td class="content-start px-4 py-2 w-60 text-sm font-normal leading-normal">
                            {{ $paciente->nombreCompleto() }}
                        </td>

                        <td class="flex flex-col gap-2 px-4 py-2 w-60 text-titles text-sm font-normal leading-normal">

                            {{ $paciente->nacionalidad ? $paciente->nacionalidad : 'N/A' }}
                        </td>
                        <td class="px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">

                        </td>

                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </x-canva>

</x-app-layout>
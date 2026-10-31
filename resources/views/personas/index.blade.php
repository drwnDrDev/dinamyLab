<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-text leading-tight">
            {{ __('Pacientes') }}
        </h2>
    </x-slot>
    <x-canva>
        <div class="flex justify-end items-center mb-4">
            <a href="{{ route('personas.create') }}" class="bg-primary text-white px-4 py-2 rounded hover:bg-titles">
                Nuevo Paciente
            </a>
        </div>

        <form method="GET" action="{{ route('personas') }}" class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-5">
            <label class="md:col-span-2">
                <span class="sr-only">Buscar pacientes</span>
                <input type="search" name="busqueda" value="{{ request('busqueda') }}" placeholder="Documento, nombres o apellidos"
                    class="w-full rounded-lg border-borders bg-background text-text placeholder:text-titles">
            </label>
            <label>
                <span class="sr-only">Filtrar por sexo</span>
                <select name="sexo" class="w-full rounded-lg border-borders bg-background text-text">
                    <option value="">Todos los sexos</option>
                    <option value="M" @selected(request('sexo') === 'M')>Masculino</option>
                    <option value="F" @selected(request('sexo') === 'F')>Femenino</option>
                    <option value="I" @selected(request('sexo') === 'I')>Intersexual</option>
                </select>
            </label>
            <label>
                <span class="sr-only">Filtrar por municipio</span>
                <select name="municipio_id" class="w-full rounded-lg border-borders bg-background text-text">
                    <option value="">Todos los municipios</option>
                    @foreach ($municipios as $municipio)
                        <option value="{{ $municipio->id }}" @selected((string) request('municipio_id') === (string) $municipio->id)>
                            {{ $municipio->municipio }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="sr-only">Filtrar por EPS</span>
                <select name="eps" class="w-full rounded-lg border-borders bg-background text-text">
                    <option value="">Todas las EPS</option>
                    @foreach ($eps as $nombreEps)
                        <option value="{{ $nombreEps }}" @selected(request('eps') === $nombreEps)>{{ $nombreEps }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex gap-2 md:col-span-5">
                <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-white">Buscar y filtrar</button>
                <a href="{{ route('personas') }}" class="rounded-lg border border-borders px-4 py-2 text-text">Limpiar</a>
            </div>
        </form>

        <div class="my-3 flex overflow-hidden rounded-xl border border-borders bg-background dark:bg-slate-950 dark:text-slate-50">
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
                    @forelse ($personas as $paciente)
                    <tr class="border-t border-borders">
                        <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                            {{ $paciente->created_at->format('Y-m-d') }}
                        </td>
                        <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                            <a href="{{route('personas.show',$paciente)}}" class="text-titles hover:text-primary">{{$paciente->numero_documento}}</a>
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
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-text">No se encontraron pacientes.</td></tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        {{ $personas->links() }}
    </x-canva>

</x-app-layout>

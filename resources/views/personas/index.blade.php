<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patients') }}
        </h2>
    </x-slot>
    <x-canva>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Buscar Paciente</h2>
            <a href="{{ route('personas.create') }}" class="bg-primary text-white px-4 py-2 rounded hover:bg-titles">
                Nuevo Paciente
            </a>
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
                        placeholder="Search by patient name or number ID"
                        class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-text focus:outline-0 focus:ring-0 border-none bg-secondary focus:border-none h-full placeholder:text-titles px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                        value="" />
                </div>
            </label>
        </div>

 

        <h2 class="text-text text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Paciente encontrado</h2>
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

        <!-- datos quemados------------------------------------- -->
        <hr class="my-8">
       <form class="my-8 flex items-center gap-2">
            <input
                type="text"
                id="llave_busqueda"
                placeholder="número de documento"
                class="border rounded px-3 py-2">
            <button type="submit" id="buscar" class="bg-blue-500 text-white px-4 py-2 rounded">
                Buscar
            </button>
            <label for="buscar_nombre" class="flex items-center">
                <input
                    type="checkbox"
                    name="buscar_nombre"
                    id="buscar_nombre"
                    value="1"
                    {{ request('buscar_nombre') ? 'checked' : '' }}>
                <span class="ml-2">Buscar por nombre</span>

            </label>

        </form>    
        
        <h2>Pacientes</h2>
        <div class="w-full justify-around gap-2">
            <div class="grid grid-cols-8 bg-slate-300 p-2">
                <p class="text-gray-900 col-span-5">Nombres y apellidos</p>
                <p class="text-gray-900 col-span-3">Numero de Documento</p>
            </div>
            <div id="pacientes">


            </div>

            {{-- @foreach ($personas as $paciente)
                    <a href="{{route('personas.show', $paciente)}}" class="grid grid-cols-8 gap-2 hover:bg-slate-600 hover:text-slate-50">
            <p class="text-gray-900 col-span-5">{{ $paciente->nombreCompleto() }}</p>
            <p class="text-gray-900 col-span-3">{{ $paciente->tipo_documento }} {{ $paciente->numero_documento }}</p>
            </a>
            @endforeach --}}
        </div>
    </x-canva>

</x-app-layout>
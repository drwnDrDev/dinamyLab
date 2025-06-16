<x-app-layout>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Examens') }}
    </h2>
</x-slot>
<x-canva>

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
                    placeholder="Buscar por nombre de examen o ID"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-text focus:outline-0 focus:ring-0 border-none bg-secondary focus:border-none h-full placeholder:text-titles px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                    value="" />
            </div>
        </label>
    </div>

    <h2 class="text-text text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Exámenes</h2>
    <div class="my-3 flex overflow-hidden rounded-xl border border-borders bg-background">
        <div class="flex w-full flex-col">
            <div class="flex items-center justify-between border-b border-borders px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="text-text text-base font-semibold">Exámenes</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href=""
                        class="btn btn-primary flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor"
                            viewBox="0 0 256 256">
                            <path
                                d="M224,128a96,96,0,1,1-96-96A96.11,96.11,0,0,1,224,128Zm-16,0a80,80,0,1,0-80,80A80.09,80.09,0,0,0,208,128Z"></path>
                            <path
                                d="M136,88a8,8,0,0,1-8-8V72a8,8,0,0,1,16,0v8A8,8,0,0,1,136,88Zm32.49-16.49a8.06,8.06,0,0,1-5.66-2.34l-5.66-5.66a8.06,8.06,0,1,1,11.32-11.32l5.66,5.66a8.06,8.06,0,0,1-5.66,13.66ZM88.49 104.49a8 8 0 01-11.32 11.32l-5.66-5.66a8 8 0 0111.32-11.32l5.66 5.66A7.94 7.94 0 0188.49 104.49ZM128 144a8 8 0 01-8 8H112a8 8 0 010-16h8A8 8 0 01128 144Zm64-16a8 8 0 01-16 0V120a8 8 0 0116 0v8Z"></path>
                        </svg>
                        Crear examen
                    </a>    

                </div>
                </div>
            <div class="flex flex-col gap-2 px-4 py-3">
                @if ($examenes->isEmpty())
                    <div class="text-text text-base font-normal">No hay exámenes disponibles.</div>
                @else
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="text-left">
                                <th class="px-4 py-2 text-text font-semibold">CUP</th>
                                <th class="px-4 py-2 text-text font-semibold">Nombre</th>
                                <th class="px-4 py-2 text-text font-semibold">Descripcion</th>
                                <th class="px-4 py-2 text-text font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($examenes as $examen)
                                <tr class="border-b border-borders">
                                    <td class="px-4 py-2">{{ $examen->cup }}</td>
                                    <td class="px-4 py-2">{{ $examen->nombre }}</td>
                                    <td class="px-4 py-2">{{ $examen->descripcion }}</td>
                                    <td class="px-4 py-2">
                                        <a href=""
                                            class="text-primary hover:underline">Ver</a>
                                        |
                                        <a href=""
                                            class="text-secondary hover:underline">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

    </div>
</x-canva>
</x-app-layout>
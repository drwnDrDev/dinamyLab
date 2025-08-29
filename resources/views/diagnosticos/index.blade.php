<x-app-layout>
    
       
    
    <x-canva>

        <section class="flex flex-wrap items-center justify-between mb-4">
            <h2 class="font-semibold text-xl text-text leading-tight">Codigos CIE-10</h2>


            <form method="POST" action="{{ route('diagnosticos.search') }}" class="py-3 w-1/2 mx-auto">
                @csrf
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
                            name="search" />
                    </div>
                </label>
        </form>

        </section>
        <div class="my-3 flex overflow-hidden rounded-xl border border-borders bg-background">
            <table class="flex-1">
                <thead>

                    <tr class="bg-background">
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Descripción</th>
                        <th class="px-4 py-3 text-left text-text w-32 text-sm font-medium leading-normal">Código</th>
                        <th class="px-4 py-3 text-left text-text w-96 text-sm font-medium leading-normal">Grupo</th>
                        <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($codigoDiagnostico as $cie)
                    <tr data-url="{{ route('diagnosticos.show', $cie) }}" onclick="window.location.href=this.dataset.url" class="border-t border-borders cursor-pointer hover:bg-secondary">
                        <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                            {{ $cie->descripcion }}
                        </td>
                        <td class="content-start px-4 py-2 w-32 text-titles text-sm font-normal leading-normal">
                           {{$cie->codigo}}
                        <td class="content-start px-4 py-2 w-96 text-sm font-normal leading-normal">
                            {{ $cie->grupo }}
                        </td>

                        <td class="content-start px-4 py-2 w-60 text-titles text-sm font-normal leading-normal">
                            @if ($cie->estado)
                                <span class="text-green-500">Activo</span>
                            @else
                                <span class="text-red-500">Inactivo</span>
                            @endif
                        </td>

                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        {{ $codigoDiagnostico->links() }}
    </x-canva>
</x-app-layout>

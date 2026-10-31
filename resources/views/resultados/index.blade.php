<x-app-layout >
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-text leading-tight">
            {{ __('Resultados') }}
        </h2>
    </x-slot>
    <x-canva>

        <form method="GET" action="{{ route('resultados') }}" class="mb-4 flex flex-wrap gap-3">
            <input type="search" name="busqueda" value="{{ request('busqueda') }}"
                placeholder="Orden, documento, paciente o examen"
                class="min-w-60 flex-1 rounded-lg border-borders bg-background text-text">
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-white">Buscar</button>
            <a href="{{ route('resultados') }}" class="rounded-lg border border-borders px-4 py-2 text-text">Limpiar</a>
        </form>

        <div class="my-3 flex overflow-hidden rounded-xl border border-borders bg-background">
            <table class="flex-1">
                <thead>

                    <tr class="bg-background">
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Fecha</th>
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Orden Médica</th>
                        <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Paciente</th>
                        <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Exámen</th>
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Estado</th>
                    </tr>
                </thead>
                <tbody>

                        @if ($resultados->isEmpty())
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-titles text-sm font-normal leading-normal">
                                    No se encontraron resultados recientes.
                                </td>
                            </tr>

                        @else
                         @foreach ($resultados as $procedimiento)

                        <tr data-url="{{ route('resultados.show', $procedimiento) }}" onclick="window.location.href=this.dataset.url" class="cursor-pointer border-t border-borders hover:bg-secondary">
                            <td class="px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                                {{ $procedimiento->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                                {{ $procedimiento->orden->numero }}
                            </td>
                            <td class="px-4 py-2 w-60 text-sm font-normal leading-normal">
                                {{ $procedimiento->orden->paciente->nombreCompleto() }}
                            </td>
                            <td class="px-4 py-2 w-60 text-titles text-sm font-normal leading-normal">
                                {{ $procedimiento->examen->nombre }}
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                                {{ $procedimiento->estado }}
                            </td>
                        </tr>

                            @endforeach
                        @endif



                </tbody>
            </table>
        </div>
        {{ $resultados->links() }}

    </x-canva>





</x-app-layout>

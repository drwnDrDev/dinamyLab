<x-app-layout >
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-text leading-tight">
            {{ __('Resultados') }}
        </h2>
    </x-slot>
    <x-canva>

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
                                    No hay procedimientos en proceso.
                                </td>
                            </tr>

                        @else
                         @foreach ($resultados as $procedimiento)

                        <tr data-url="{{ route('resultados.show', $procedimiento) }}" onclick="window.location.href=this.dataset.url" class="cursor-pointer border-t border-borders hover:bg-secondary">
                            <td class="px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                                {{ $procedimiento->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                                {{ $procedimiento->orden_id }}
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

    </x-canva>





</x-app-layout>

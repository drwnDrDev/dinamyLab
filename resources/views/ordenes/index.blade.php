<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-evenly items-center mb-4">
            <h1 class="text-text text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">
                {{ __('Medical order') }}
            </h1>
            <x-primary-button href="{{ route('ordenes.create') }}" class="w-full sm:w-40">
                {{ __('Create new order') }}
            </x-primary-button>
        </div>
    </x-slot>
    <x-canva>

        <section class="flex flex-wrap items-center justify-between mb-4 dark:text-slate-50 dark:bg-slate-950 p-4 rounded-xl">
            <h2 class="font-semibold text-xl text-text leading-tight dark:text-yellow-100">Órdenes recientes</h2>

            <form method="GET" action="{{ route('ordenes') }}" class="flex w-full flex-wrap gap-3 py-3">
                <input type="search" name="busqueda" value="{{ request('busqueda') }}" placeholder="Número de orden, documento o nombre del paciente"
                    class="min-w-60 flex-1 rounded-xl border-borders bg-background text-text placeholder:text-titles">
                <select name="estado" class="rounded-xl border-borders bg-background text-text">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" @selected(request('estado') === 'pendiente')>Pendientes</option>
                    <option value="completada" @selected(request('estado') === 'completada')>Completadas</option>
                </select>
                <button type="submit" class="rounded-xl bg-primary px-4 py-2 text-white">Buscar</button>
                <a href="{{ route('ordenes') }}" class="rounded-xl border border-borders px-4 py-2 text-text">Limpiar</a>
            </form>

        </section>
        <div class="my-3 flex overflow-hidden rounded-xl border border-borders bg-background">
            <table class="flex-1">
                <thead>

                    <tr class="bg-background">
                        <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Fecha</th>
                        <th class="px-4 py-3 text-left text-text w-32 text-sm font-medium leading-normal">Orden Médica</th>
                        <th class="px-4 py-3 text-left text-text w-96 text-sm font-medium leading-normal">Paciente</th>
                        <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Estado</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordenes as $orden)
                    <tr data-url="{{ route('ordenes.show', $orden) }}" onclick="window.location.href=this.dataset.url" class="border-t border-borders cursor-pointer hover:bg-secondary">
                        <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
                            {{ $orden->created_at->format('d-m-Y') }}
                        </td>
                        <td class="content-start px-4 py-2 w-32 text-titles text-sm font-normal leading-normal">
                            <a href="{{route('ordenes.show',$orden)}}" class="text-titles">{{$orden->numero}}</a>
                        </td>
                        <td class="content-start px-4 py-2 w-96 text-sm font-normal leading-normal">
                            {{ $orden->paciente->nombreCompleto() }}
                        </td>

                        <td class="content-start px-4 py-2 w-60 text-titles text-sm font-normal leading-normal">
                            <button
                                    class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-8 px-4 bg-[#e7f2f3]  font-medium leading-normal w-full">
                                    <span class="truncate">{{ $orden->terminada==null ? 'Pendiente':'Completada' }}</span>
                            </button>
                        </td>

                    </tr>


                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-text">No se encontraron órdenes.</td></tr>
                    @endforelse

                </tbody>

            </table>

        </div>
        {{ $ordenes->links() }}
    </x-canva>
</x-app-layout>

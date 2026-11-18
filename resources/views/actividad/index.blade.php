<x-app-layout>
    <x-canva>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
            <h1 class="text-2xl font-bold text-text dark:text-white">Monitor de eventos</h1>
            <p class="text-sm text-titles mt-1">Actividad de usuarios · Fecha y hora de Colombia ({{ $zona }})</p>
            </div>
            <a href="{{ request()->fullUrl() }}" class="rounded-lg border border-primary px-4 py-2 text-sm text-primary hover:bg-secondary">Actualizar</a>
        </div>
        <form method="GET" action="{{ route('actividad.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6">
            <label class="text-sm text-text">Actividad
                <select name="evento" class="block w-full rounded-lg border-borders mt-1 text-gray-900">
                    <option value="">Todas</option>
                    @foreach (\App\Models\EventoActividad::EVENTOS as $clave => $nombre)
                        <option value="{{ $clave }}" @selected(request('evento') === $clave)>{{ $nombre }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm text-text">Usuario
                <select name="usuario_id" class="block w-full rounded-lg border-borders mt-1 text-gray-900">
                    <option value="">Todos</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->usuario_id }}" @selected((string) request('usuario_id') === (string) $usuario->usuario_id)>{{ $usuario->usuario_nombre }} (#{{ $usuario->usuario_id }})</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm text-text">Desde
                <input type="date" name="desde" value="{{ request('desde') }}" class="block w-full rounded-lg border-borders mt-1 text-gray-900">
            </label>
            <label class="text-sm text-text">Hasta
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="block w-full rounded-lg border-borders mt-1 text-gray-900">
            </label>
            <div class="flex items-end gap-3">
                <button class="bg-primary text-white rounded-lg px-4 py-2" type="submit">Filtrar</button>
                <a href="{{ route('actividad.index') }}" class="text-primary py-2 hover:underline">Limpiar</a>
            </div>
        </form>
        @if ($errors->any())
            <div role="alert" class="text-red-600 mb-4">{{ $errors->first() }}</div>
        @endif
        <p class="text-sm text-titles mb-3">{{ $eventos->total() }} eventos encontrados</p>
        <div class="overflow-x-auto rounded-xl border border-borders">
            <table class="w-full text-sm text-left text-text dark:text-white">
                <thead class="bg-secondary dark:bg-slate-700"><tr>
                    <th class="p-3">Fecha y hora</th><th class="p-3">Usuario</th><th class="p-3">Actividad</th><th class="p-3">Referencia</th><th class="p-3">Empresa / sede</th><th class="p-3">Detalles</th>
                </tr></thead>
                <tbody class="divide-y divide-borders">
                    @forelse ($eventos as $evento)
                        <tr class="hover:bg-secondary/30">
                            <td class="p-3 whitespace-nowrap">{{ \Carbon\CarbonImmutable::parse($evento->ocurrio_en, 'UTC')->timezone($zona)->format('d/m/Y H:i:s') }}</td>
                            <td class="p-3">{{ $evento->usuario_nombre }} <span class="text-xs text-titles">#{{ $evento->usuario_id }}</span></td>
                            <td class="p-3">{{ \App\Models\EventoActividad::EVENTOS[$evento->evento] ?? $evento->evento }}</td>
                            <td class="p-3">{{ $evento->recurso_tipo ? $evento->recurso_tipo.' #'.$evento->recurso_id : '—' }}</td>
                            <td class="p-3">{{ $evento->empresa_id ?? '—' }} / {{ $evento->sede_id ?? '—' }}</td>
                            <td class="p-3"><span class="text-xs break-words">{{ $evento->detalles ? json_encode($evento->detalles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '—' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-titles">No hay eventos para los filtros seleccionados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $eventos->links() }}</div>
        <p class="text-xs text-titles mt-5">La impresión registra la solicitud desde el reporte; no confirma la salida física. Las descargas registran la entrega del PDF por el servidor. Los resultados se registran por parámetro; los lotes incluyen un resumen adicional.</p>
    </x-canva>
</x-app-layout>

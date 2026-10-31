<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-text">Códigos CUPS</h1>
    </x-slot>

    <x-canva>
        <form method="GET" action="{{ route('cups.index') }}" class="mb-4 flex flex-wrap gap-3">
            <input type="search" name="busqueda" value="{{ request('busqueda') }}"
                placeholder="Buscar por código, nombre o grupo"
                class="min-w-60 flex-1 rounded-lg border-borders bg-background text-text">
            <select name="activo" class="rounded-lg border-borders bg-background text-text">
                <option value="">Todos los estados</option>
                <option value="1" @selected(request('activo') === '1')>Activos</option>
                <option value="0" @selected(request('activo') === '0')>Inactivos</option>
            </select>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-white">Buscar</button>
            <a href="{{ route('cups.index') }}" class="rounded-lg border border-borders px-4 py-2 text-text">Limpiar</a>
        </form>

        <div class="overflow-x-auto rounded-xl border border-borders bg-background">
            <table class="w-full">
                <thead>
                    <tr class="bg-secondary text-left text-text">
                        <th class="px-4 py-3">Código</th>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Grupo</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($codigoCups as $codigoCup)
                        <tr class="border-t border-borders text-text">
                            <td class="px-4 py-3">{{ $codigoCup->codigo }}</td>
                            <td class="px-4 py-3">{{ $codigoCup->nombre }}</td>
                            <td class="px-4 py-3">{{ $codigoCup->grupo ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $codigoCup->activo ? 'Activo' : 'Inactivo' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('cups.show', $codigoCup->codigo) }}" class="text-primary hover:underline">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-text">No se encontraron códigos CUPS.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $codigoCups->links() }}</div>
    </x-canva>
</x-app-layout>

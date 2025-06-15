<x-app-layout>
    <x-slot name="title">
        Convenios
    </x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold">Convenios</h1>
    </x-slot>

    <x-canva>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Lista de Convenios</h2>
            <a href="{{ route('convenios.create') }}" class="btn btn-primary">Nuevo Convenio</a>
        </div>

        @if($convenios->isEmpty())
            <p>No hay convenios registrados.</p>
        @else
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Razon Social</th>
                        <th class="py-2 px-4 border-b">Descripción</th>
                        <th class="py-2 px-4 border-b">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($convenios as $convenio)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $convenio->nombre }}</td>
                            <td class="py-2 px-4 border-b">{{ $convenio->descripcion }}</td>
                            <td class="py-2 px-4 border-b">
                                <a href="{{ route('convenios.edit', $convenio) }}" class="btn btn-secondary">Editar</a>
                                <form action="{{ route('convenios.destroy', $convenio) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-canva>  
</x-app-layout>
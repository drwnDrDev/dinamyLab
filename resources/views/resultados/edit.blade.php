<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Editar Resultado del Paciente: {{$procedimiento->orden->paciente->nombreCompleto()}}</h3>
        <form action="{{route('resultados.update', $procedimiento)}}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                @php
                $lastGroup = null;
                @endphp
                @foreach ( $parametros as $p)
                @if ($p['grupo'] && $p['grupo'] !== $lastGroup)
                <h3 class="pt-2 pl-2 font-semibold uppercase col-span-full">{{ $p['grupo']}}</h3>
                @php
                $lastGroup = $p['grupo'];
                @endphp
                @endif

                <x-parametro-edit :parametro="$p" />
                @endforeach
            </div>
            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar Resultados</button>
            </div>
        </form>
    </div>
</x-app-layout>

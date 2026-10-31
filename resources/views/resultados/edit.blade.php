<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">
        <h3 class="text-lg font-medium text-titles dark:text-white mb-2">Editar Resultado del Paciente: {{$procedimiento->orden->paciente->nombreCompleto()}}</h3>
        <p class="mb-4 text-sm text-text dark:text-gray-300">{{ $procedimiento->examen->nombre }} · Orden {{ $procedimiento->orden->numero }}</p>
        @if ($errors->any())
            <div class="mb-4 rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200" role="alert">
                <p class="font-semibold">Revise la información antes de guardar:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{route('resultados.update', $procedimiento)}}" method="POST" onsubmit="return confirm('¿Confirma guardar los cambios en los resultados?');">
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
                <label for="observacion" class="mb-1 block text-sm font-semibold text-text dark:text-gray-200">
                    Motivo de la modificación <span class="text-red-600">*</span>
                </label>
                <textarea
                    id="observacion"
                    name="observacion"
                    rows="3"
                    maxlength="1000"
                    required
                    class="w-full rounded-md border border-borders bg-white p-3 text-text focus:border-primary focus:ring-primary dark:bg-slate-800 dark:text-white"
                    placeholder="Describa por qué se modifican los resultados"
                >{{ old('observacion') }}</textarea>
                <p class="mt-1 text-xs text-text dark:text-gray-300">Esta observación quedará asociada al historial de cambios.</p>
            </div>
            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:bg-titles">Guardar Resultados</button>
            </div>
        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="mx-auto max-w-6xl">
            <h1 class="text-xl font-semibold text-titles dark:text-white">{{ $examen->nombre }}</h1>
            <p class="text-sm text-text dark:text-gray-300">
                CUP-{{ $examen->cup }}
                @if ($examen->nombre_alternativo)
                    · {{ $examen->nombre_alternativo }}
                @endif
            </p>
        </div>
    </x-slot>

    <x-canva>
        <div
            id="resultados-lote-root"
            data-action="{{ route('examenes.lote.store', $examen) }}"
        >
            <script id="resultados-lote-data" type="application/json">@json($muestras)</script>
        </div>

        @if ($procedimientos->hasPages())
            <nav class="mt-4" aria-label="Paginación de muestras">
                {{ $procedimientos->links() }}
            </nav>
        @endif
    </x-canva>
</x-app-layout>

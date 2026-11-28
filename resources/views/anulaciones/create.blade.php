<x-app-layout>
    <x-index.page>
        <x-index.header title="Solicitar anulación de examen">
            <a href="{{ route('anulaciones.index') }}" class="btn btn-secondary">Mis solicitudes</a>
        </x-index.header>

        <section class="surface space-y-2 p-4">
            <h2 class="text-lg font-semibold">{{ $procedimiento->examen->nombre }}</h2>
            <p>CUP {{ $procedimiento->examen->cup }} · Orden #{{ $procedimiento->orden->numero }}</p>
            <p>{{ $procedimiento->orden->paciente->nombreCompleto() }} · {{ $procedimiento->sede->nombre }}</p>
            <x-status-badge :estado="$procedimiento->estado" />
        </section>

        @if ($pendiente)
            <p role="status" class="rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
                Este examen ya tiene una solicitud pendiente de administración. Conserva su estado mientras se revisa.
            </p>
        @elseif ($procedimiento->puedeSolicitarAnulacion())
            <form method="POST" action="{{ route('anulaciones.store', $procedimiento) }}" class="surface space-y-4 p-4">
                @csrf
                <p class="text-sm text-muted dark:text-slate-400">Explique por qué solicita la anulación. Administración deberá aprobarla antes de que el examen cambie de estado.</p>
                <label for="motivo-anulacion" class="block text-sm font-semibold">Motivo de la solicitud</label>
                <textarea id="motivo-anulacion" name="motivo" required maxlength="1000" rows="4" class="w-full rounded-md border-borders dark:bg-slate-900">{{ old('motivo') }}</textarea>
                @error('motivo')<p role="alert" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</p>@enderror
                <div class="flex flex-wrap justify-end gap-2">
                    <a href="{{ route('procedimientos') }}" class="btn btn-secondary">Volver a procedimientos</a>
                    <button type="submit" class="btn btn-primary">Enviar solicitud a administración</button>
                </div>
            </form>
        @else
            <p class="surface p-4 text-sm">Este examen ya no admite solicitudes de anulación porque fue finalizado, anulado o exportado.</p>
        @endif

        @if ($solicitudes->isNotEmpty())
            <section class="surface space-y-4 p-4">
                <h2 class="font-semibold">Historial de solicitudes</h2>
                @foreach ($solicitudes as $solicitud)
                    <article class="space-y-2 border-t border-borders pt-3">
                        <div class="flex flex-wrap items-center gap-2 text-sm">
                            <x-status-badge :tone="match($solicitud->estado) { 'aprobada' => 'success', 'rechazada' => 'danger', default => 'warning' }">{{ ucfirst($solicitud->estado) }}</x-status-badge>
                            <span>{{ $solicitud->solicitante?->name ?? 'Usuario eliminado' }} · {{ $solicitud->created_at->timezone('America/Bogota')->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="whitespace-pre-wrap break-words text-sm">{{ $solicitud->motivo }}</p>
                        @if ($solicitud->resuelta_at)
                            <p class="text-sm text-muted dark:text-slate-400">Resuelta por {{ $solicitud->resueltaPor?->name ?? 'Usuario eliminado' }} · {{ $solicitud->resuelta_at->timezone('America/Bogota')->format('d/m/Y H:i') }}</p>
                            @if ($solicitud->respuesta)<p class="whitespace-pre-wrap break-words text-sm">{{ $solicitud->respuesta }}</p>@endif
                        @endif
                    </article>
                @endforeach
            </section>
        @endif
    </x-index.page>
</x-app-layout>

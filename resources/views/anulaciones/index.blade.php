<x-app-layout>
    <x-index.page>
        <x-index.header :title="$esAdmin ? 'Solicitudes de anulación' : 'Mis solicitudes de anulación'" :subtitle="$pendientes.' solicitudes pendientes'">
            <a href="{{ request()->fullUrl() }}" class="btn btn-secondary">Actualizar</a>
        </x-index.header>

        <p class="text-sm text-muted dark:text-slate-400">{{ $esAdmin ? 'Revise el motivo y apruebe o rechace cada solicitud. Solo la aprobación cambia el examen a Anulado.' : 'El examen conserva su estado hasta que administración apruebe la solicitud.' }}</p>
        @if ($errors->any())<p role="alert" class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">{{ $errors->first() }}</p>@endif

        <x-index.card>
            <x-slot name="filters">
                <x-index.filter-bar :action="route('anulaciones.index')">
                    <x-index.select name="estado" label="Estado de la solicitud">
                        @foreach (['pendiente' => 'Pendientes', 'aprobada' => 'Aprobadas', 'rechazada' => 'Rechazadas', 'todas' => 'Todas'] as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected($estado === $valor)>{{ $etiqueta }}</option>
                        @endforeach
                    </x-index.select>
                </x-index.filter-bar>
            </x-slot>

            <div class="divide-y divide-borders dark:divide-slate-800">
                @forelse ($solicitudes as $solicitud)
                    @php $procedimiento = $solicitud->procedimiento; @endphp
                    <article class="space-y-3 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="space-y-1">
                                <h2 class="font-semibold">{{ $procedimiento?->examen?->nombre ?? 'Examen no disponible' }}</h2>
                                <p class="text-sm">{{ $procedimiento?->orden?->paciente?->nombreCompleto() ?? 'Paciente no disponible' }} · Orden #{{ $procedimiento?->orden?->numero ?? '—' }} · {{ $solicitud->sede?->nombre }}</p>
                                <p class="text-xs text-muted dark:text-slate-400">Solicitada por {{ $solicitud->solicitante?->name ?? 'Usuario eliminado' }} · {{ $solicitud->created_at->timezone('America/Bogota')->format('d/m/Y H:i') }}</p>
                            </div>
                            <x-status-badge :tone="match($solicitud->estado) { 'aprobada' => 'success', 'rechazada' => 'danger', default => 'warning' }">{{ ucfirst($solicitud->estado) }}</x-status-badge>
                        </div>
                        <p class="whitespace-pre-wrap break-words text-sm"><span class="font-semibold">Motivo:</span> {{ $solicitud->motivo }}</p>
                        @if ($procedimiento)
                            <div class="flex flex-wrap items-center gap-3 text-sm">
                                <span>Estado del examen: <x-status-badge :estado="$procedimiento->estado" /></span>
                                @unless ($procedimiento->trashed())
                                    <a href="{{ $esAdmin ? route('procedimientos.show', $procedimiento) : route('anulaciones.create', $procedimiento) }}" class="font-semibold text-primary hover:underline dark:text-cyan-300">Ver examen y solicitud</a>
                                @endunless
                            </div>
                        @endif

                        @if ($esAdmin && $solicitud->estado === 'pendiente')
                            @if (! $procedimiento?->puedeSolicitarAnulacion())
                                <p class="text-sm text-amber-800 dark:text-amber-300">El examen cambió de estado o dejó de estar disponible. La solicitud puede rechazarse, pero ya no puede aprobarse.</p>
                            @endif
                            <form method="POST" action="{{ route('anulaciones.resolver', $solicitud) }}" class="space-y-2"
                                onsubmit="return event.submitter?.value !== 'aprobar' || confirm('¿Aprobar la solicitud y anular este examen?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="solicitud_id" value="{{ $solicitud->id }}">
                                <label for="respuesta-{{ $solicitud->id }}" class="block text-sm">Respuesta a la solicitud (obligatoria para rechazar)</label>
                                <textarea id="respuesta-{{ $solicitud->id }}" name="respuesta" maxlength="1000" rows="2" class="w-full rounded-md border-borders dark:bg-slate-900">{{ (string) old('solicitud_id') === (string) $solicitud->id ? old('respuesta') : '' }}</textarea>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <button name="decision" value="rechazar" type="submit" class="btn btn-secondary">Rechazar solicitud</button>
                                    <button name="decision" value="aprobar" type="submit" @disabled(! $procedimiento?->puedeSolicitarAnulacion()) class="btn btn-primary disabled:opacity-50">Aprobar y anular examen</button>
                                </div>
                            </form>
                        @elseif ($solicitud->resuelta_at)
                            <p class="text-xs text-muted dark:text-slate-400">Resuelta por {{ $solicitud->resueltaPor?->name ?? 'Usuario eliminado' }} · {{ $solicitud->resuelta_at->timezone('America/Bogota')->format('d/m/Y H:i') }}</p>
                            @if ($solicitud->respuesta)<p class="whitespace-pre-wrap break-words text-sm">{{ $solicitud->respuesta }}</p>@endif
                        @endif
                    </article>
                @empty
                    <p class="p-8 text-center text-sm text-muted dark:text-slate-400">No hay solicitudes para este filtro.</p>
                @endforelse
            </div>
            <x-slot name="footer"><x-index.pagination :paginator="$solicitudes" /></x-slot>
        </x-index.card>
    </x-index.page>
</x-app-layout>

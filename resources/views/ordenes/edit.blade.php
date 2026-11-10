<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">
            Editar orden #{{ $orden->numero }}
        </h2>
    </x-slot>

    <x-canva>
        <form method="POST" action="{{ route('ordenes.update', $orden) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div role="alert" class="rounded-md border border-red-300 bg-red-50 p-4 text-red-800">
                    <p class="font-semibold">No fue posible guardar los cambios.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @if (collect($errors->all())->contains(fn ($error) => str_contains($error, 'No se puede quitar este examen')))
                        <p class="mt-2 font-semibold">Los resultados terminados se conservaron. Cree una nueva orden para solicitar otros exámenes.</p>
                    @endif
                </div>
            @endif

            <section class="space-y-3">
                <h3 class="text-lg font-semibold text-titles">Exámenes</h3>
                <p class="text-sm text-text">Cambie la cantidad a cero para quitar un examen. Los procedimientos con resultados o terminados no se pueden quitar.</p>
                <div class="overflow-x-auto rounded-lg border border-borders">
                    <table class="w-full text-sm">
                        <thead class="bg-secondary text-left text-titles">
                            <tr>
                                <th class="px-3 py-2">Examen</th>
                                <th class="px-3 py-2">Valor unitario</th>
                                <th class="px-3 py-2">Cantidad</th>
                                <th class="px-3 py-2">Procedimientos terminados</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-borders">
                            @foreach ($orden->examenes as $examen)
                                @php
                                    $terminados = $orden->procedimientos
                                        ->where('examen_id', $examen->id)
                                        ->filter(fn ($procedimiento) => in_array($procedimiento->estado, [
                                            \App\Estado::TERMINADO->value,
                                            \App\Estado::ENTREGADO->value,
                                        ], true) || $procedimiento->resultado()->exists())
                                        ->count();
                                @endphp
                                <tr>
                                    <td class="px-3 py-2">{{ $examen->nombre }}</td>
                                    <td class="px-3 py-2">${{ number_format($examen->valor, 2) }}</td>
                                    <td class="px-3 py-2">
                                        <input
                                            type="number"
                                            name="examenes[{{ $examen->id }}]"
                                            min="0"
                                            max="255"
                                            required
                                            value="{{ old('examenes.'.$examen->id, $examen->pivot->cantidad) }}"
                                            class="w-24 rounded-md border border-borders px-2 py-1"
                                            aria-label="Cantidad de {{ $examen->nombre }}"
                                        >
                                    </td>
                                    <td class="px-3 py-2">{{ $terminados }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="nuevos-examenes" class="space-y-2">
                    <h4 class="font-medium text-titles">Agregar exámenes</h4>
                    @foreach (old('examenes_nuevos', []) as $index => $examenNuevo)
                        <div class="flex flex-wrap items-center gap-2" data-examen-row>
                            <select name="examenes_nuevos[{{ $index }}][id]" class="min-w-60 flex-1 rounded-md border border-borders px-3 py-2">
                                <option value="">Seleccione un examen</option>
                                @foreach ($examenes as $examen)
                                    @unless ($orden->examenes->contains('id', $examen->id))
                                        <option value="{{ $examen->id }}" @selected(old("examenes_nuevos.{$index}.id") == $examen->id)>
                                            {{ $examen->nombre }} — ${{ number_format($examen->valor, 2) }}
                                        </option>
                                    @endunless
                                @endforeach
                            </select>
                            <input
                                type="number"
                                name="examenes_nuevos[{{ $index }}][cantidad]"
                                min="1"
                                max="255"
                                value="{{ old("examenes_nuevos.{$index}.cantidad", 1) }}"
                                class="w-24 rounded-md border border-borders px-3 py-2"
                                aria-label="Cantidad del nuevo examen"
                            >
                            <button type="button" class="text-red-600" data-remove-examen>Quitar fila</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="agregar-examen" class="rounded-md border border-primary px-3 py-2 text-sm text-primary">
                    Agregar otra fila de examen
                </button>
                <template id="plantilla-examen">
                    <div class="flex flex-wrap items-center gap-2" data-examen-row>
                        <select name="examenes_nuevos[__INDEX__][id]" class="min-w-60 flex-1 rounded-md border border-borders px-3 py-2">
                            <option value="">Seleccione un examen</option>
                            @foreach ($examenes as $examen)
                                @unless ($orden->examenes->contains('id', $examen->id))
                                    <option value="{{ $examen->id }}">{{ $examen->nombre }} — ${{ number_format($examen->valor, 2) }}</option>
                                @endunless
                            @endforeach
                        </select>
                        <input type="number" name="examenes_nuevos[__INDEX__][cantidad]" min="1" max="255" value="1" class="w-24 rounded-md border border-borders px-3 py-2" aria-label="Cantidad del nuevo examen">
                        <button type="button" class="text-red-600" data-remove-examen>Quitar fila</button>
                    </div>
                </template>
            </section>

            <section class="space-y-3">
                <h3 class="text-lg font-semibold text-titles">Remisiones existentes</h3>
                @forelse ($orden->remisiones as $remision)
                    <div class="grid grid-cols-1 gap-3 rounded-lg border border-borders p-4 md:grid-cols-2" data-remision-row>
                        <input type="hidden" name="remisiones[{{ $remision->id }}][id]" value="{{ $remision->id }}">
                        <p class="text-sm text-titles md:col-span-2">{{ $remision->nombre }} (CUP: {{ $remision->cup }}) — ${{ number_format($remision->valor, 2) }}</p>
                        <label class="text-sm text-titles md:col-span-2">
                            Destino registrado
                            <select
                                name="remisiones[{{ $remision->id }}][destino_sede_id]"
                                data-destino-sede
                                class="mt-1 w-full rounded-md border border-borders px-3 py-2"
                            >
                                <option value="">Profesional o IPS externa</option>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}" @selected(old("remisiones.{$remision->id}.destino_sede_id", $remision->destino_sede_id) == $sede->id)>
                                        {{ $sede->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label class="text-sm text-titles md:col-span-2" data-destino-externo-wrapper>
                            Nombre del profesional o IPS externa
                            <input
                                type="text"
                                name="remisiones[{{ $remision->id }}][destino]"
                                value="{{ old("remisiones.{$remision->id}.destino", $remision->destino_sede_id ? '' : $remision->destino) }}"
                                maxlength="255"
                                class="mt-1 w-full rounded-md border border-borders px-3 py-2"
                            >
                        </label>
                        <label class="flex items-center gap-2 text-sm text-red-600 md:col-span-2">
                            <input type="checkbox" name="remisiones[{{ $remision->id }}][eliminar]" value="1" @checked(old("remisiones.{$remision->id}.eliminar"))>
                            Quitar esta remisión
                        </label>
                    </div>
                @empty
                    <p class="text-sm text-text">Esta orden no tiene remisiones.</p>
                @endforelse
            </section>

            <section class="space-y-3">
                <h3 class="text-lg font-semibold text-titles">Agregar remisiones</h3>
                <div id="nuevas-remisiones" class="space-y-3">
                    @foreach (old('nuevas_remisiones', []) as $index => $remisionNueva)
                        <div class="grid grid-cols-1 gap-3 rounded-lg border border-borders p-4 md:grid-cols-2" data-remision-row>
                            <label class="text-sm text-titles">
                                Buscar por nombre o código CUP
                                <input type="search" data-buscar-cup autocomplete="off" class="mt-1 w-full rounded-md border border-borders px-3 py-2" placeholder="Nombre o código CUP">
                                <span data-sugerencias-cup class="mt-1 block" role="listbox"></span>
                            </label>
                            <label class="text-sm text-titles">
                                CUP
                                <input required maxlength="20" name="nuevas_remisiones[{{ $index }}][cup]" data-cup value="{{ old("nuevas_remisiones.{$index}.cup") }}" class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                            </label>
                            <label class="text-sm text-titles md:col-span-2">
                                Nombre del examen o procedimiento
                                <input required maxlength="255" name="nuevas_remisiones[{{ $index }}][nombre]" data-nombre value="{{ old("nuevas_remisiones.{$index}.nombre") }}" class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                            </label>
                            <label class="text-sm text-titles">
                                Valor
                                <input required type="number" min="0" step="0.01" name="nuevas_remisiones[{{ $index }}][valor]" value="{{ old("nuevas_remisiones.{$index}.valor") }}" class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                            </label>
                            <label class="text-sm text-titles">
                                Sede de destino (opcional)
                                <select name="nuevas_remisiones[{{ $index }}][destino_sede_id]" data-destino-sede class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                                    <option value="">Profesional o IPS externa</option>
                                    @foreach ($sedes as $sede)
                                        <option value="{{ $sede->id }}" @selected(old("nuevas_remisiones.{$index}.destino_sede_id") == $sede->id)>{{ $sede->nombre }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="text-sm text-titles md:col-span-2" data-destino-externo-wrapper>
                                Nombre del profesional o IPS externa
                                <input maxlength="255" name="nuevas_remisiones[{{ $index }}][destino]" value="{{ old("nuevas_remisiones.{$index}.destino") }}" class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                            </label>
                            <button type="button" class="justify-self-start text-red-600" data-remove-remision>Quitar fila</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="agregar-remision" class="rounded-md border border-primary px-3 py-2 text-sm text-primary">
                    Agregar otra fila de remisión
                </button>
                <template id="plantilla-remision">
                    <div class="grid grid-cols-1 gap-3 rounded-lg border border-borders p-4 md:grid-cols-2" data-remision-row>
                        <label class="text-sm text-titles">
                            Buscar por nombre o código CUP
                            <input type="search" data-buscar-cup autocomplete="off" class="mt-1 w-full rounded-md border border-borders px-3 py-2" placeholder="Nombre o código CUP">
                            <span data-sugerencias-cup class="mt-1 block" role="listbox"></span>
                        </label>
                        <label class="text-sm text-titles">
                            CUP
                            <input required maxlength="20" name="nuevas_remisiones[__INDEX__][cup]" data-cup class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                        </label>
                        <label class="text-sm text-titles md:col-span-2">
                            Nombre del examen o procedimiento
                            <input required maxlength="255" name="nuevas_remisiones[__INDEX__][nombre]" data-nombre class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                        </label>
                        <label class="text-sm text-titles">
                            Valor
                            <input required type="number" min="0" step="0.01" name="nuevas_remisiones[__INDEX__][valor]" class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                        </label>
                        <label class="text-sm text-titles">
                            Sede de destino (opcional)
                            <select name="nuevas_remisiones[__INDEX__][destino_sede_id]" data-destino-sede class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                                <option value="">Profesional o IPS externa</option>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="text-sm text-titles md:col-span-2" data-destino-externo-wrapper>
                            Nombre del profesional o IPS externa
                            <input maxlength="255" name="nuevas_remisiones[__INDEX__][destino]" class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                        </label>
                        <button type="button" class="justify-self-start text-red-600" data-remove-remision>Quitar fila</button>
                    </div>
                </template>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <label class="text-sm text-titles">
                    Abono
                    <input type="number" name="abono" min="0" step="0.01" value="{{ old('abono', $orden->abono) }}" class="mt-1 w-full rounded-md border border-borders px-3 py-2">
                </label>
                <label class="text-sm text-titles">
                    Observaciones
                    <textarea name="observaciones" maxlength="1000" rows="3" class="mt-1 w-full rounded-md border border-borders px-3 py-2">{{ old('observaciones', $orden->observaciones) }}</textarea>
                </label>
            </section>

            <div class="flex justify-end gap-3">
                <a href="{{ route('ordenes.show', $orden) }}" class="rounded-md border border-borders px-4 py-2 text-text">Cancelar</a>
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-white">Guardar cambios</button>
            </div>
        </form>
    </x-canva>

    <script>
        (() => {
            const examenesContainer = document.getElementById('nuevos-examenes');
            const remisionesContainer = document.getElementById('nuevas-remisiones');
            let examenIndex = {{ empty(old('examenes_nuevos', [])) ? 0 : max(array_map('intval', array_keys(old('examenes_nuevos', []))) + [0]) + 1 }};
            let remisionIndex = {{ empty(old('nuevas_remisiones', [])) ? 0 : max(array_map('intval', array_keys(old('nuevas_remisiones', []))) + [0]) + 1 }};

            const addRow = (container, template, index) => {
                container.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(index)));
            };

            document.getElementById('agregar-examen').addEventListener('click', () => {
                addRow(examenesContainer, document.getElementById('plantilla-examen'), examenIndex++);
            });
            document.getElementById('agregar-remision').addEventListener('click', () => {
                addRow(remisionesContainer, document.getElementById('plantilla-remision'), remisionIndex++);
            });

            document.addEventListener('click', event => {
                if (event.target.matches('[data-remove-examen]')) event.target.closest('[data-examen-row]').remove();
                if (event.target.matches('[data-remove-remision]')) event.target.closest('[data-remision-row]').remove();
                if (event.target.matches('[data-cup-sugerencia]')) {
                    const row = event.target.closest('[data-remision-row]');
                    row.querySelector('[data-cup]').value = event.target.dataset.codigo;
                    row.querySelector('[data-nombre]').value = event.target.dataset.nombre;
                    row.querySelector('[data-buscar-cup]').value = `${event.target.dataset.codigo} - ${event.target.dataset.nombre}`;
                    row.querySelector('[data-sugerencias-cup]').replaceChildren();
                }
            });

            document.addEventListener('change', event => {
                if (!event.target.matches('[data-destino-sede]')) return;
                const wrapper = event.target.closest('[data-remision-row]')?.querySelector('[data-destino-externo-wrapper]');
                if (!wrapper) return;
                const input = wrapper.querySelector('input');
                const isExternal = event.target.value === '';
                wrapper.hidden = !isExternal;
                input.required = isExternal;
                if (!isExternal) input.value = '';
            });

            document.addEventListener('input', event => {
                if (!event.target.matches('[data-buscar-cup]')) return;
                const input = event.target;
                const row = input.closest('[data-remision-row]');
                const results = row.querySelector('[data-sugerencias-cup]');
                clearTimeout(input.searchTimer);
                const query = input.value.trim();
                results.replaceChildren();
                if (query.length < 2) return;

                input.searchTimer = setTimeout(async () => {
                    try {
                        const url = new URL('/api/cups/buscar', window.location.origin);
                        url.searchParams.set('query', query);
                        const response = await fetch(url, { headers: { Accept: 'application/json' } });
                        if (!response.ok) throw new Error(`La búsqueda respondió ${response.status}`);
                        const body = await response.json();
                        const cups = body.data || [];
                        if (cups.length === 0) {
                            results.textContent = 'No se encontraron procedimientos.';
                            return;
                        }
                        for (const cup of cups) {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.dataset.cupSugerencia = '';
                            button.dataset.codigo = cup.codigo;
                            button.dataset.nombre = cup.nombre;
                            button.className = 'mr-2 mb-1 rounded border border-borders bg-white px-2 py-1 text-left text-xs hover:bg-secondary';
                            button.textContent = `${cup.codigo} — ${cup.nombre}`;
                            results.append(button);
                        }
                    } catch (error) {
                        console.error('Error al buscar procedimientos CUP', error);
                        results.textContent = 'No fue posible buscar procedimientos. Intenta de nuevo.';
                    }
                }, 300);
            });

            document.querySelectorAll('[data-destino-sede]').forEach(select => {
                select.dispatchEvent(new Event('change', { bubbles: true }));
            });
        })();
    </script>
</x-app-layout>

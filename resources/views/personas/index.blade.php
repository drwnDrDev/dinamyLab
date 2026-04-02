<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Inicio', 'href' => route('inicio')],
            ['label' => 'Pacientes'],
        ]" />
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-text dark:text-white tracking-tight">Pacientes</h1>
                <p class="text-sm text-muted dark:text-slate-400 mt-0.5">Directorio de pacientes registrados</p>
            </div>
            <x-primary-button href="{{ route('personas.create') }}">
                <svg class="w-4 h-4 mr-1.5 -ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo Paciente
            </x-primary-button>
        </div>
    </x-slot>

    <x-canva>
        <!-- Tabla -->
        <div class="overflow-hidden rounded-xl border border-borders/60 dark:border-slate-800">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-background dark:bg-slate-800/60 border-b border-borders/60 dark:border-slate-800">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-muted dark:text-slate-400 uppercase tracking-wider w-36">Registro</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-muted dark:text-slate-400 uppercase tracking-wider w-40">Documento</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-muted dark:text-slate-400 uppercase tracking-wider">Nombre completo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-muted dark:text-slate-400 uppercase tracking-wider w-36">Nacionalidad</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-muted dark:text-slate-400 uppercase tracking-wider w-20">Edad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-borders/40 dark:divide-slate-800">
                    @foreach ($personas as $paciente)
                    <tr class="bg-white dark:bg-slate-900 hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors group">
                        <td class="px-4 py-3 text-muted dark:text-slate-400 font-normal">
                            {{ $paciente->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('personas.show', $paciente) }}"
                               class="font-semibold text-primary dark:text-cyan-400 hover:underline">
                                {{ $paciente->numero_documento }}
                            </a>
                        </td>
                        <td class="px-4 py-3 font-medium text-text dark:text-slate-200">
                            {{ $paciente->nombreCompleto() }}
                        </td>
                        <td class="px-4 py-3 text-muted dark:text-slate-400">
                            {{ $paciente->nacionalidad ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-muted dark:text-slate-400">
                            —
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-canva>

</x-app-layout>

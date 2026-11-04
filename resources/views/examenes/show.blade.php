<x-app-layout>

<x-slot name="header">
<div class="max-screen mx-auto">
  <div class="flex items-center justify-between gap-1 mb-0">
    <div class="flex items-center gap-1">
      <h2 class="text-xl font-semibold">{{ $examen->nombre }}</h2>
      <h3 class="text-lg font-light text-gray-700 mt-0">({{ $examen->nombre_alternativo }})</h3>
    </div>
    @can('registrar_resultado')
      <a href="{{ route('examenes.lote', $examen) }}" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition">
        Procesar Lotes
      </a>
    @endcan
  </div>

</x-slot>
<x-canva>
    <div
      id="examen-detail-root"
      data-examen="{{ json_encode($examen) }}"
      data-can-edit-exam="{{ auth()->user()->can('editar_examen') ? 'true' : 'false' }}"
      data-can-edit-description="{{ auth()->user()->can('editar_descripcion_examen') ? 'true' : 'false' }}"
    ></div>
</x-canva>
@vite('resources/js/ExamenDetail.jsx')
</x-app-layout>

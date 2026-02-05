<x-app-layout>

<x-slot name="header">
<div class="max-screen mx-auto">
  <div class="flex items-center justify-center gap-1 mb-0">
    <h2 class="text-xl font-semibold">{{ $examen->nombre }}</h2>
    <h3 class="text-lg font-light text-gray-700 mt-0">({{ $examen->nombre_alternativo }})</h3>
</div>
<div class="flex items-center justify-center gap-2 ">
    <h3 class="text-lg font-light text-gray-700">CUP-{{ $examen->cup }}</h3>
    <h3  class="text-lg font-light text-green-700">${{ $examen->valor }} COP</h3>
</div>
    <p class="max-w-11/12 p-4">{{ ucfirst($examen->descripcion) }}</p>

</div>
</x-slot>
<x-canva>
    <section class="w-full">
        <!-- Formulario de procesamiento por lotes -->
        <div class="bg-white rounded-lg border border-borders shadow-md p-6 mb-6">
            <h3 class="text-2xl font-bold mb-4 text-gray-800">Procesar por Lotes</h3>

            <!-- Información del examen seleccionado -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                <p class="text-sm text-gray-600"><strong>Examen:</strong> {{ $examen->nombre }}</p>
                <p class="text-sm text-gray-600"><strong>Total de parámetros:</strong> {{ $examen->parametros->count() }}</p>
            </div>

            <!-- Tabla de procedimientos pendientes -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b-2 border-borders">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                <input type="checkbox" id="selectAllProcedimientos" class="w-4 h-4 rounded cursor-pointer">
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Paciente</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Documento</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Orden #</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Enviar</th>
                        </tr>
                    </thead>
                    <tbody id="procedimientosTableBody">
                        <!-- Se llenará dinámicamente -->
                    </tbody>
                </table>
                <div id="noProcedimientos" class="text-center py-8 text-gray-500">
                    No hay procedimientos pendientes para este examen
                </div>
            </div>

            <!-- Indicador de cantidad seleccionada -->
            <div class="mt-4 p-3 bg-gray-50 rounded border border-gray-200">
                <p class="text-sm text-gray-600">
                    <strong id="countSelected">0</strong> procedimientos seleccionados
                </p>
            </div>
        </div>

        <!-- Formulario de parámetros -->
        <div id="parametrosFormContainer" class="hidden bg-white rounded-lg border border-borders shadow-md p-6">
            <h3 class="text-2xl font-bold mb-4 text-gray-800">Completar Parámetros</h3>

            <form id="parametrosForm">
                @csrf

                <!-- Tabla de parámetros -->
                <div class="overflow-x-auto mb-6">
                    <table class="w-full border-collapse">
                        <thead id="parametrosTableHead">
                            <tr class="bg-gray-100 border-b-2 border-borders">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Paciente</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Orden #</th>
                                <!-- Parámetros se agregarán dinámicamente -->
                            </tr>
                        </thead>
                        <tbody id="parametrosTableBody">
                            <!-- Se llenará dinámicamente con los parámetros del examen por procedimiento -->
                        </tbody>
                    </table>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition">
                        Guardar Resultados
                    </button>
                    <button type="button" onclick="limpiarFormulario()" class="px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </section>

        <div id="loteData"
            data-examen-id="{{ $examen->id }}"
            data-parametros="{{ e($examen->parametros->toJson()) }}">
    </div>
    @vite('resources/js/examenes/lote.jsx')
</x-canva>
</x-app-layout>

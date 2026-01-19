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
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-borders">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Persona
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Posición</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Unidades</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Resultado</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Valor Referencia</th>
                            </tr>
                        </thead>
                        <tbody id="parametrosTableBody">
                            <!-- Se llenará dinámicamente con los parámetros del examen -->
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

    <script>
        // Datos del examen y parámetros
        const examenId = {{ $examen->id }};
        const examenNombre = "{{ $examen->nombre }}";
        const parametros = @json($examen->parametros);
        const procedimientosSeleccionados = new Set();
        let procedimientosDisponibles = [];

        // Cargar procedimientos pendientes
        async function cargarProcedimientosPendientes() {
            try {
                const response = await fetch(`/api/procedimientos/examen/${examenId}/pendientes`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) throw new Error('Error al cargar procedimientos');

                const data = await response.json();
                procedimientosDisponibles = data.procedimientos || [];
                renderizarProcedimientos();
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('noProcedimientos').textContent = 'Error al cargar los procedimientos';
            }
        }

        // Renderizar tabla de procedimientos
        function renderizarProcedimientos() {
            const tbody = document.getElementById('procedimientosTableBody');
            const noProcedimientos = document.getElementById('noProcedimientos');

            if (procedimientosDisponibles.length === 0) {
                tbody.innerHTML = '';
                noProcedimientos.classList.remove('hidden');
                return;
            }

            noProcedimientos.classList.add('hidden');
            tbody.innerHTML = procedimientosDisponibles.map(proc => `
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="procedimientoCheckbox w-4 h-4 rounded cursor-pointer"
                               data-procedimiento-id="${proc.id}"
                               onchange="actualizarSeleccion(${proc.id}, this.checked)">
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">${proc.paciente_nombre}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${proc.paciente_documento}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${proc.orden_id}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${proc.fecha}</td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full ${getEstadoClass(proc.estado)}">
                            ${proc.estado}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded ${proc.enviar ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${proc.enviar ? 'Sí' : 'No'}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        // Obtener clase de estilo para el estado
        function getEstadoClass(estado) {
            const estadoClases = {
                'pendiente': 'bg-yellow-100 text-yellow-800',
                'en proceso': 'bg-blue-100 text-blue-800',
                'completado': 'bg-green-100 text-green-800',
                'revirtió': 'bg-red-100 text-red-800'
            };
            return estadoClases[estado.toLowerCase()] || 'bg-gray-100 text-gray-800';
        }

        // Actualizar selección de procedimientos
        function actualizarSeleccion(procedimientoId, seleccionado) {
            if (seleccionado) {
                procedimientosSeleccionados.add(procedimientoId);
            } else {
                procedimientosSeleccionados.delete(procedimientoId);
            }

            actualizarContador();
            actualizarCheckboxPrincipal();

            if (procedimientosSeleccionados.size > 0) {
                mostrarFormularioParametros();
            } else {
                ocultarFormularioParametros();
            }
        }

        // Actualizar contador de seleccionados
        function actualizarContador() {
            document.getElementById('countSelected').textContent = procedimientosSeleccionados.size;
        }

        // Actualizar checkbox "Seleccionar Todo"
        function actualizarCheckboxPrincipal() {
            const checkboxPrincipal = document.getElementById('selectAllProcedimientos');
            const todosSeleccionados = procedimientosDisponibles.length > 0 &&
                                      procedimientosDisponibles.every(proc => procedimientosSeleccionados.has(proc.id));
            checkboxPrincipal.checked = todosSeleccionados;
        }

        // Manejar "Seleccionar Todo"
        document.getElementById('selectAllProcedimientos').addEventListener('change', function() {
            procedimientosDisponibles.forEach(proc => {
                if (this.checked) {
                    procedimientosSeleccionados.add(proc.id);
                } else {
                    procedimientosSeleccionados.delete(proc.id);
                }
            });

            document.querySelectorAll('.procedimientoCheckbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });

            actualizarContador();

            if (procedimientosSeleccionados.size > 0) {
                mostrarFormularioParametros();
            } else {
                ocultarFormularioParametros();
            }
        });

        // Mostrar formulario de parámetros
        function mostrarFormularioParametros() {
            const container = document.getElementById('parametrosFormContainer');
            if (container.classList.contains('hidden')) {
                renderizarParametros();
                container.classList.remove('hidden');
                container.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Ocultar formulario de parámetros
        function ocultarFormularioParametros() {
            document.getElementById('parametrosFormContainer').classList.add('hidden');
        }

        // Renderizar tabla de parámetros
        function renderizarParametros() {
            const tbody = document.getElementById('parametrosTableBody');

            tbody.innerHTML = parametros.map((param, index) => {
                const valoresRef = param.valores_referencia || [];
                const textoValoresRef = valoresRef.length > 0
                    ? valoresRef.map(v => `${v.minimo}-${v.maximo} ${v.unidades}`).join(', ')
                    : 'N/A';

                return `
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">${param.nombre}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${param.posicion || 'N/A'}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${param.unidades || ''}</td>
                        <td class="px-4 py-3">
                            <input type="text"
                                   name="resultados[${param.id}]"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="${param.nombre}"
                                   required>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">${textoValoresRef}</td>
                    </tr>
                `;
            }).join('');
        }

        // Limpiar formulario
        function limpiarFormulario() {
            procedimientosSeleccionados.clear();
            document.querySelectorAll('.procedimientoCheckbox').forEach(checkbox => checkbox.checked = false);
            document.getElementById('selectAllProcedimientos').checked = false;
            actualizarContador();
            ocultarFormularioParametros();
            document.getElementById('parametrosForm').reset();
        }

        // Enviar formulario
        document.getElementById('parametrosForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (procedimientosSeleccionados.size === 0) {
                alert('Debe seleccionar al menos un procedimiento');
                return;
            }

            const formData = new FormData(this);
            const resultados = {};

            // Recolectar resultados del formulario
            parametros.forEach(param => {
                const valor = formData.get(`resultados[${param.id}]`);
                if (valor) {
                    resultados[param.id] = valor;
                }
            });

            try {
                // Enviar para cada procedimiento seleccionado
                const promesas = Array.from(procedimientosSeleccionados).map(procedimientoId => {
                    return fetch(`/resultados/${procedimientoId}/store`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            resultados: resultados
                        })
                    });
                });

                const respuestas = await Promise.all(promesas);

                if (respuestas.every(r => r.ok)) {
                    alert('✓ Resultados guardados exitosamente para ' + procedimientosSeleccionados.size + ' procedimientos');
                    limpiarFormulario();
                    cargarProcedimientosPendientes();
                } else {
                    alert('⚠ Hubo un error al guardar algunos resultados');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al enviar los resultados');
            }
        });

        // Cargar procedimientos al iniciar
        document.addEventListener('DOMContentLoaded', cargarProcedimientosPendientes);
    </script>
</x-canva>
</x-app-layout>

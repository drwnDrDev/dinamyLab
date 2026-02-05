const dataEl = document.getElementById('loteData');

if (dataEl) {
    const examenId = dataEl.dataset.examenId;
    const parametros = JSON.parse(dataEl.dataset.parametros || '[]');
    const procedimientosSeleccionados = new Set();
    let procedimientosDisponibles = [];

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
            const noProcedimientos = document.getElementById('noProcedimientos');
            if (noProcedimientos) {
                noProcedimientos.textContent = 'Error al cargar los procedimientos';
            }
        }
    }

    function renderizarProcedimientos() {
        const tbody = document.getElementById('procedimientosTableBody');
        const noProcedimientos = document.getElementById('noProcedimientos');

        if (!tbody || !noProcedimientos) return;

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
                        ${proc.enviar ? 'Si' : 'No'}
                    </span>
                </td>
            </tr>
        `).join('');
    }

    function getEstadoClass(estado) {
        const estadoClases = {
            'pendiente': 'bg-yellow-100 text-yellow-800',
            'en proceso': 'bg-blue-100 text-blue-800',
            'completado': 'bg-green-100 text-green-800',
            'revirtio': 'bg-red-100 text-red-800'
        };
        const normalizado = String(estado || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
        return estadoClases[normalizado] || 'bg-gray-100 text-gray-800';
    }

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

    function actualizarContador() {
        const countSelected = document.getElementById('countSelected');
        if (countSelected) {
            countSelected.textContent = procedimientosSeleccionados.size;
        }
    }

    function actualizarCheckboxPrincipal() {
        const checkboxPrincipal = document.getElementById('selectAllProcedimientos');
        if (!checkboxPrincipal) return;

        const todosSeleccionados = procedimientosDisponibles.length > 0 &&
            procedimientosDisponibles.every(proc => procedimientosSeleccionados.has(proc.id));
        checkboxPrincipal.checked = todosSeleccionados;
    }

    const selectAll = document.getElementById('selectAllProcedimientos');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
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
    }

    function mostrarFormularioParametros() {
        const container = document.getElementById('parametrosFormContainer');
        if (container && container.classList.contains('hidden')) {
            renderizarParametros();
            container.classList.remove('hidden');
            container.scrollIntoView({ behavior: 'smooth' });
        }
    }

    function ocultarFormularioParametros() {
        const container = document.getElementById('parametrosFormContainer');
        if (container) {
            container.classList.add('hidden');
        }
    }

    function renderizarParametros() {
        const thead = document.getElementById('parametrosTableHead');
        const tbody = document.getElementById('parametrosTableBody');

        if (!thead || !tbody) return;

        const paramHeaders = parametros.map(param => `
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">${param.nombre} (${param.unidades || ''})</th>
        `).join('');

        thead.innerHTML = `
            <tr class="bg-gray-100 border-b-2 border-borders">
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Paciente</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Orden #</th>
                ${paramHeaders}
            </tr>
        `;

        tbody.innerHTML = Array.from(procedimientosSeleccionados).map(procedimientoId => {
            const proc = procedimientosDisponibles.find(p => p.id === procedimientoId);
            const paramInputs = parametros.map(param => `
                <td class="px-4 py-3">
                    <input type="text"
                           name="resultados[${procedimientoId}][${param.id}]"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="${param.nombre}"
                           required>
                </td>
            `).join('');

            return `
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-semibold text-gray-800">${proc ? proc.paciente_nombre : ''}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${proc ? proc.orden_id : ''}</td>
                    ${paramInputs}
                </tr>
            `;
        }).join('');
    }

    function limpiarFormulario() {
        procedimientosSeleccionados.clear();
        document.querySelectorAll('.procedimientoCheckbox').forEach(checkbox => checkbox.checked = false);
        const selectAllCheckbox = document.getElementById('selectAllProcedimientos');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
        }
        actualizarContador();
        ocultarFormularioParametros();
        const form = document.getElementById('parametrosForm');
        if (form) {
            form.reset();
        }
    }

    const form = document.getElementById('parametrosForm');
    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (procedimientosSeleccionados.size === 0) {
                alert('Debe seleccionar al menos un procedimiento');
                return;
            }

            const formData = new FormData(this);
            const resultadosPorProcedimiento = {};

            Array.from(procedimientosSeleccionados).forEach(procedimientoId => {
                resultadosPorProcedimiento[procedimientoId] = {};
                parametros.forEach(param => {
                    const valor = formData.get(`resultados[${procedimientoId}][${param.id}]`);
                    if (valor && valor.trim() !== '') {
                        resultadosPorProcedimiento[procedimientoId][param.id] = valor.trim();
                    }
                });
            });

            const procedimientosSinResultados = Object.keys(resultadosPorProcedimiento).filter(id =>
                Object.keys(resultadosPorProcedimiento[id]).length === 0
            );

            if (procedimientosSinResultados.length > 0) {
                alert('Debe ingresar resultados para todos los parametros de cada procedimiento seleccionado');
                return;
            }

            try {
                const promesas = Object.entries(resultadosPorProcedimiento).map(async ([procedimientoId, resultados]) => {
                    const response = await fetch(`/resultados/${procedimientoId}/store`, {
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

                    const data = await response.json();

                    return {
                        ok: response.ok,
                        status: response.status,
                        procedimientoId: procedimientoId,
                        data: data
                    };
                });

                const respuestas = await Promise.all(promesas);

                const exitosas = respuestas.filter(r => r.ok);
                const fallidas = respuestas.filter(r => !r.ok);

                if (fallidas.length === 0) {
                    alert('Resultados guardados exitosamente para ' + exitosas.length + ' procedimiento(s)');
                    limpiarFormulario();
                    cargarProcedimientosPendientes();
                } else {
                    const mensajesError = fallidas.map(r =>
                        `Procedimiento ${r.procedimientoId}: ${r.data.message || 'Error desconocido'}`
                    ).join('\n');

                    alert('Algunos resultados fallaron:\n\n' + mensajesError);

                    if (exitosas.length > 0) {
                        limpiarFormulario();
                        cargarProcedimientosPendientes();
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al enviar los resultados');
            }
        });
    }

    window.actualizarSeleccion = actualizarSeleccion;
    window.limpiarFormulario = limpiarFormulario;

    document.addEventListener('DOMContentLoaded', cargarProcedimientosPendientes);
}

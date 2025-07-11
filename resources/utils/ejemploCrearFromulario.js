/**
 * @fileoverview Script para gestionar la creación de órdenes, incluyendo la gestión de pacientes,
 * acompañantes y la selección de exámenes médicos.
 * @version 2.0.0
 */

// Usamos el evento DOMContentLoaded para asegurarnos de que todo el HTML está cargado
// antes de ejecutar el script. Esto evita errores y organiza todo el código en un solo bloque.
document.addEventListener('DOMContentLoaded', () => {

    // --- SECCIÓN: CONSTANTES Y ESTADO --- //

    // Congelar los objetos de constantes para hacerlos inmutables y evitar modificaciones accidentales.
    const CONSTANTS = Object.freeze({
        PACIENTE: 'Paciente',
        ACOMPANIANTE: 'Acompañante',
        NUEVO_USUARIO: 'nuevoUsuario',
        ACTUALIZAR_USUARIO: 'actualizarUsuario',
        // ... otras constantes si las hubiera
    });

    // Centralizamos el estado de la aplicación para un manejo más sencillo.
    const appState = {
        todosLosExamenes: [],
        examenesVisibles: [],
        municipios: JSON.parse(localStorage.getItem('municipios_data')) || [],
        paises: JSON.parse(localStorage.getItem('paises_data')) || [],
        eps: JSON.parse(localStorage.getItem('eps_data')) || [],
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    };

    // --- SECCIÓN: SELECTORES DEL DOM --- //

    // Centralizar la obtención de elementos del DOM reduce la redundancia y facilita cambios futuros.
    const dom = {
        crearPacienteForm: document.getElementById('crearPaciente'),
        crearAcompanianteForm: document.getElementById('crearacompaniante'),
        pacienteIdInput: document.getElementById('paciente_id'),
        acompanianteIdInput: document.getElementById('acompaniante_id'),
        filtroExamenes16k: document.getElementById('16000'),
        busquedaExamenInput: document.getElementById('busquedaExamen'),
        examenesContainer: document.getElementById('examenesContainer'),
        totalExamenesSpan: document.getElementById('totalExamenes'),
    };

    // --- SECCIÓN: LÓGICA DE API --- //

    // Crear una instancia de Axios centraliza la configuración (headers, base URL, etc.).
    const apiClient = axios.create({
        headers: {
            'X-CSRF-TOKEN': appState.csrfToken,
            'Accept': 'application/json',
        }
    });

    /**
     * Obtiene la lista de todos los exámenes desde la API.
     * @returns {Promise<Array>} Una promesa que resuelve a un array de exámenes.
     */
    const fetchExamenes = async () => {
        try {
            const response = await apiClient.get('/api/examenes');
            return response.data.data.examenes || [];
        } catch (error) {
            console.error('Error al obtener los exámenes:', error);
            return []; // Devolver un array vacío en caso de error para no romper el resto del código.
        }
    };

    /**
     * Busca una persona por su número de documento.
     * @param {string} numeroDocumento - El número de documento a buscar.
     * @returns {Promise<Object|null>} Los datos de la persona o null si no se encuentra.
     */
    const fetchPersonaPorDocumento = async (numeroDocumento) => {
        if (numeroDocumento.length <= 3) return null;
        try {
            const response = await apiClient.get(`/api/personas/buscar/${numeroDocumento}`);
            return response.data.data || null;
        } catch (error) {
            // Un 404 es esperado si el usuario no existe, no es un error crítico.
            if (error.response?.status !== 404) {
                console.error("Error al buscar persona:", error);
            }
            return null;
        }
    };

    // --- SECCIÓN: MANIPULACIÓN DEL DOM Y UI --- //

    /**
     * Crea y devuelve un elemento HTML para un examen.
     * @param {Object} examen - El objeto del examen.
     * @returns {HTMLElement} El elemento div del examen.
     */
    const createExamenItemElement = (examen) => {
        const item = document.createElement('div');
        item.className = 'examen-item flex items-center justify-between gap-2 p-2 border border-borders rounded-sm shadow-sm';
        item.dataset.examenId = examen.id; // Usar data-attributes es más limpio

        const valorInicial = parseFloat(examen.valor).toFixed(2);
        const totalActual = (examen.currenTotal ?? (examen.valor * (examen.cantidad || 0))).toFixed(2);

        item.innerHTML = `
            <div class="grid flex-grow">
                <label for="examen-${examen.id}" class="text-lg font-semibold">${examen.nombre}</label>
                <span id="precio-${examen.id}" class="text-sm ${examen.currenTotal ? 'text-gray-900' : 'text-gray-500'} precio" >
                    $ ${totalActual}
                </span>
            </div>
            <input type="number" id="examen-${examen.id}" name="examenes[${examen.id}]"
                   class="w-20 px-2 py-1 text-center rounded border border-borders bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                   min="0" value="${examen.cantidad || 0}" step="1">
        `;
        return item;
    };

    /**
     * Renderiza la lista de exámenes en el contenedor.
     * @param {Array} listaExamenes - Los exámenes a mostrar.
     */
    const renderExamenes = (listaExamenes) => {
        dom.examenesContainer.innerHTML = ''; // Limpiar eficientemente el contenedor.
        const fragment = document.createDocumentFragment(); // Usar un fragmento para mejor rendimiento.
        listaExamenes.forEach(examen => fragment.appendChild(createExamenItemElement(examen)));
        dom.examenesContainer.appendChild(fragment);
        updateTotalExamenes();
    };

    /**
     * Actualiza el total monetario de todos los exámenes seleccionados.
     */
    const updateTotalExamenes = () => {
        const total = appState.examenesVisibles.reduce((sum, ex) => sum + (ex.currenTotal || 0), 0);
        if (dom.totalExamenesSpan) {
            dom.totalExamenesSpan.textContent = `Total: $ ${total.toFixed(2)}`;
        }
    };

    /**
     * Rellena un formulario con los datos de una persona encontrada.
     * @param {HTMLFormElement} form - El formulario a rellenar.
     * @param {Object} persona - Los datos de la persona.
     */
    const populateFormWithPersonaData = (form, persona) => {
        form['tipoGuardado'].value = CONSTANTS.ACTUALIZAR_USUARIO;
        form['numero_documento'].value = persona.numero_documento;
        form['tipo_documento'].value = persona.tipo_documento;
        form['nombres'].value = persona.nombre;
        form['apellidos'].value = persona.apellido;
        // ... Llenar otros campos ...
        // Ejemplo para un campo complejo como el sexo:
        form.querySelectorAll('input[name="sexo"]').forEach(radio => {
            radio.checked = radio.value === persona.sexo;
        });

        if (form['perfil'].value === CONSTANTS.PACIENTE) {
            dom.pacienteIdInput.value = persona.id;
        } else {
            dom.acompanianteIdInput.value = persona.id;
        }
    };

    /**
     * Muestra mensajes de error de validación en un formulario.
     * @param {HTMLFormElement} form - El formulario donde mostrar el error.
     * @param {Object} errors - Objeto con los errores de validación.
     */
    const displayValidationErrors = (form, errors) => {
        let errorContainer = form.querySelector('.error-container');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.className = 'error-container text-sm text-red-600 space-y-1 mt-2';
            form.appendChild(errorContainer);
        }

        const errorMessages = Object.values(errors).flat().map(msg => `<li>${msg}</li>`).join('');
        errorContainer.innerHTML = `<ul>${errorMessages}</ul>`;

        setTimeout(() => errorContainer.remove(), 5000);
    };


    // --- SECCIÓN: MANEJADORES DE EVENTOS --- //

    /**
     * Maneja los filtros y la búsqueda de exámenes.
     */
    const handleFiltroExamenes = () => {
        const query = dom.busquedaExamenInput.value.toLowerCase();
        const solo16k = dom.filtroExamenes16k.checked;

        appState.examenesVisibles = appState.todosLosExamenes.filter(examen => {
            const pasaFiltroPrecio = !solo16k || parseFloat(examen.valor) === 16000;
            const pasaFiltroBusqueda = query.length < 3 || examen.nombre.toLowerCase().includes(query);
            return pasaFiltroPrecio && pasaFiltroBusqueda;
        });

        renderExamenes(appState.examenesVisibles);
    };

    /**
     * Maneja el evento de buscar un usuario por su documento.
     * @param {Event} e - El evento (blur).
     */
    const handleBuscarDocumento = async (e) => {
        const form = e.target.form;
        const numeroDocumento = e.target.value;
        const persona = await fetchPersonaPorDocumento(numeroDocumento);

        if (persona) {
            populateFormWithPersonaData(form, persona);
        } else {
            form['tipoGuardado'].value = CONSTANTS.NUEVO_USUARIO;
            // Opcional: limpiar otros campos si no se encontró el usuario.
        }
    };

    /**
     * Maneja el guardado (creación o actualización) de una persona.
     * @param {Event} e - El evento (submit).
     */
    const handleGuardarPersona = async (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const isPaciente = formData.get('perfil') === CONSTANTS.PACIENTE;
        const esActualizacion = form['tipoGuardado'].value === CONSTANTS.ACTUALIZAR_USUARIO;

        let url = '/api/personas';
        if (esActualizacion) {
            const id = isPaciente ? dom.pacienteIdInput.value : dom.acompanianteIdInput.value;
            url = `/api/personas/${id}`;
            formData.append('_method', 'PUT'); // Laravel usa esto para simular un PUT
        }

        try {
            const response = await apiClient.post(url, formData);
            // Lógica de éxito: deshabilitar form, mostrar mensaje, etc.
            console.log('Guardado con éxito:', response.data.data);
            // ... Aquí iría la lógica para deshabilitar el formulario ...
        } catch (error) {
            if (error.response?.status === 422) { // Error de validación
                displayValidationErrors(form, error.response.data.errors);
            } else {
                console.error('Error al guardar:', error);
                // Aquí podrías mostrar un error genérico al usuario
            }
        }
    };

    /**
      * Maneja la actualización de la cantidad y el subtotal de un examen.
      * @param {Event} e - El evento (input o blur) en el campo de cantidad.
      */
    const handleUpdateExamenCantidad = (e) => {
        if (e.target.matches('input[type="number"]')) {
            const input = e.target;
            const examenId = parseInt(input.closest('.examen-item').dataset.examenId, 10);
            const examen = appState.examenesVisibles.find(ex => ex.id === examenId);

            if (examen) {
                let cantidad = parseInt(input.value, 10);
                if (isNaN(cantidad) || cantidad < 0) {
                    cantidad = 0;
                    input.value = '0';
                }

                examen.cantidad = cantidad;
                examen.currenTotal = examen.valor * examen.cantidad;

                const precioSpan = document.getElementById(`precio-${examen.id}`);
                if (precioSpan) {
                    precioSpan.textContent = `$ ${examen.currenTotal.toFixed(2)}`;
                    precioSpan.className = 'text-sm text-gray-900 dark:text-gray-100 precio';
                }

                updateTotalExamenes();
            }
        }
    };


    // --- SECCIÓN: INICIALIZACIÓN --- //

    /**
     * Función principal que inicializa la aplicación.
     */
    const init = async () => {
        // 1. Cargar datos iniciales
        appState.todosLosExamenes = await fetchExamenes();
        appState.examenesVisibles = [...appState.todosLosExamenes]; // Clonar para la vista inicial
        renderExamenes(appState.examenesVisibles);

        // 2. Asignar manejadores de eventos (Event Listeners)
        dom.filtroExamenes16k?.addEventListener('change', handleFiltroExamenes);
        dom.busquedaExamenInput?.addEventListener('input', handleFiltroExamenes);

        dom.crearPacienteForm?.addEventListener('submit', handleGuardarPersona);
        dom.crearAcompanianteForm?.addEventListener('submit', handleGuardarPersona);

        // Usar querySelectorAll para asignar el mismo evento a múltiples elementos
        document.querySelectorAll('input[name="numero_documento"]').forEach(input => {
            input.addEventListener('blur', handleBuscarDocumento);
        });

        // Event Delegation para los inputs de cantidad de exámenes. Es más eficiente.
        dom.examenesContainer?.addEventListener('input', handleUpdateExamenCantidad);
        dom.examenesContainer?.addEventListener('blur', handleUpdateExamenCantidad, true); // Usar captura para el blur

        // ... Otros event listeners ...
    };

    // ¡Arrancamos la aplicación!
    init();

});

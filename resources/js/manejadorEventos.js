
import {dom,appState,DATA_KEYS} from './variables.js'
import { renderExamenes,updateTotalExamenes } from './crearExamenes.js';
import {fetchPersonaPorDocumento} from './api.js'
import { populateFormWithPersonaData, displayValidationErrors } from './formularioPersona.js';

export const handleFiltroExamenes = () => {
        const query = dom.busquedaExamenInput.value.toLowerCase();
        const solo16k = dom.soloDiezYSeisMil.checked;

        appState.examenesVisibles = appState.todosLosExamenes.filter(examen => {
            const pasaFiltroPrecio = !solo16k || parseFloat(examen.valor) === 16000;
            const pasaFiltroBusqueda = query.length < 3 || examen.nombre.toLowerCase().includes(query);
            return pasaFiltroPrecio && pasaFiltroBusqueda;
        });

        renderExamenes(appState.examenesVisibles);
    };


 export const handleBuscarDocumento = async (e) => {
        const form = e.target.form;
        const numeroDocumento = e.target.value;
        const persona = await fetchPersonaPorDocumento(numeroDocumento);
        console.log(persona)

        if (persona) {
            populateFormWithPersonaData(form, persona);
        } else {
            form['tipoGuardado'].value = DATA_KEYS.NUEVO_USUARIO;
            form['tipoGuardado'].textContent="Usuario Nuevo"

        }
    };

  export const handleGuardarPersona = async (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const isPaciente = formData.get('perfil') === DATA_KEYS.PACIENTE;
        const esActualizacion = form['tipoGuardado'].value === DATA_KEYS.ACTUALIZAR_USUARIO;

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


    export const handleUpdateExamenCantidad = (e) => {
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


export const handleTipoGuardadoChange = (e) => {
        const form = e.target.form;
        const tipoGuardado = e.target.value;

        if (tipoGuardado === DATA_KEYS.ACTUALIZAR_USUARIO) {
            form['tipoGuardado'].textContent = "Actualizar Usuario";
            form['numero_documento'].disabled = true;
            form['numero_documento'].required = false;
        } else {
            form['tipoGuardado'].textContent = "Registrar Usuario";
            form['numero_documento'].disabled = false;
            form['numero_documento'].required = true;
        }
    }
export const handlePerfilChange = (e) => {
        const form = e.target.form;
        const perfil = e.target.value;

        if (perfil === DATA_KEYS.PACIENTE) {
            dom.pacienteIdInput.disabled = false;
            dom.acompanianteIdInput.disabled = true;
            dom.acompanianteIdInput.value = '';
            dom.pacienteIdInput.required = true;
            dom.acompanianteIdInput.required = false;
        } else {
            dom.pacienteIdInput.disabled = true;
            dom.pacienteIdInput.value = '';
            dom.acompanianteIdInput.disabled = false;
            dom.pacienteIdInput.required = false;
            dom.acompanianteIdInput.required = true;
        }
    }

export const handleFillPais = (e) => {
    const form = e.target.form;
    }


import {dom,appState,DATA_KEYS} from './variables.js'
import { renderExamenes,updateTotalExamenes } from './crearExamenes.js';
import {fetchPersonaPorDocumento, guardarPersona} from './api.js'
import { populateFormWithPersonaData, displayValidationErrors } from './formularioPersona.js';


export const handleFiltroExamenes = () => {
        const query = dom.busquedaExamenInput.value.toLowerCase();
        const solo16k = dom.soloDiezYSeisMil.checked;

        appState.examenesVisibles = appState.todosLosExamenes.filter(examen => {
            const pasaFiltroPrecio = !solo16k || parseFloat(examen.valor) === 16000;
            const pasaFiltroBusqueda = query.length < 1 || examen.nombre.toLowerCase().includes(query);
            return pasaFiltroPrecio && pasaFiltroBusqueda;
        });

        renderExamenes(appState.examenesVisibles);
    };


 export const handleBuscarDocumento = async (e) => {
        const form = e.target.form;
        const numeroDocumento = e.target.value;
        const persona = await fetchPersonaPorDocumento(numeroDocumento);
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
            const id = isPaciente ? dom.paciente.value : dom.acompaniante.value;
            url = `/api/personas/${id}`;
            formData.append('_method', 'PUT'); // Laravel usa esto para simular un PUT
        }

        const persona = await guardarPersona(url,formData);
        console.log(persona);
        if (!persona) return;   

      if(isPaciente) {
            dom.paciente.value = persona.data.data.id;
      }else{
            dom.acompaniante.value = persona.data.data.id;
        }
    form.classList.add('bg-green-100', 'dark:bg-green-800', 'border-green-400', 'dark:border-green-600', 'text-green-700', 'dark:text-green-300', 'rounded-lg', 'p-4', 'mb-4');

        Array.from(form.elements).forEach(element => {
            if (element.type !== 'hidden') {
                element.setAttribute('disabled', 'disabled');
                element.classList.add('bg-stone-300', 'dark:bg-gray-700', 'cursor-not-allowed');
            }
        });
      }



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

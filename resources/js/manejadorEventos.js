
import {dom,appState,DATA_KEYS} from './variables.js'
import { renderExamenes,updateTotalExamenes } from './crearExamenes.js';
import {fetchPersonaPorDocumento, guardarPersona} from './api.js'
import { populateFormWithPersonaData, displayValidationErrors,displayMunicipios,displayOpciones } from './formularioPersona.js';


export const handleFiltroExamenes = () => {
        const query = dom.busquedaExamenInput.value.toLowerCase();
        appState.examenesVisibles = appState.todosLosExamenes.filter(examen => {
            const pasaFiltroBusqueda = query.length < 1 || examen.nombre.toLowerCase().includes(query);
            return pasaFiltroBusqueda;
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

export const handleBuscarMunicipio = (currentBusquedaFormMunicipio) => {
    currentBusquedaFormMunicipio.addEventListener(
        'focus', (e) => {
            e.target.value = ''; // Limpiar el campo al enfocar
            appState.filteredMunicipios = appState.municipios; // Reiniciar la lista filtrada
            displayOpciones(currentBusquedaFormMunicipio.form, appState.filteredMunicipios);
        }
    )
    const formularioActual = currentBusquedaFormMunicipio.form;
    const selectMunicipio = formularioActual.querySelector('select[name="municipio"]');
        currentBusquedaFormMunicipio.addEventListener('focus', () => {
            displayMunicipios(selectMunicipio);
        });

        currentBusquedaFormMunicipio.addEventListener('input', () => {
        const searchValue = currentBusquedaFormMunicipio.value.toLowerCase();
            appState.filteredMunicipios = appState.municipios.filter(municipio =>
            municipio.municipio.toLowerCase().includes(searchValue) ||
            municipio.departamento.toLowerCase().includes(searchValue)  );
        displayOpciones(formularioActual, appState.filteredMunicipios);

        });
   };

  export const handleGuardarPersona = async (e) => {

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

        return persona;
    };


export const notificarGuardado = (persona,isPaciente=true,form) => {
      if (!persona) return;
      if(isPaciente) {
            dom.paciente.value = persona.id;
      }else{
            dom.acompaniante.value = persona.id;
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
                    precioSpan.className = 'text-sm text-gray-900 dark:text-green-500 precio';

                    if (examen.currenTotal == 0) {
                        precioSpan.textContent = `$ 0.00`;
                        precioSpan.className = 'text-sm text-gray-900 dark:text-gray-500 precio';
                    }
                   
                }
                    const ciePrincipalSelect = input.closest('.examen-item').querySelector(`select[name="cie_principal[${examen.id}]"]`);
                    if (ciePrincipalSelect) {
                        if (cantidad > 0) {
                            ciePrincipalSelect.removeAttribute('disabled');
                            ciePrincipalSelect.setAttribute('aria-disabled', 'false');
                        } else {
                            ciePrincipalSelect.setAttribute('disabled', 'disabled');
                            ciePrincipalSelect.value = '';
                        }
                    }
                    const cieSecundarioSelect = input.closest('.examen-item').querySelector(`select[name="cie_secundario[${examen.id}]"]`);
                    if (cieSecundarioSelect) {
                        if (cantidad > 0) {
                            cieSecundarioSelect.removeAttribute('disabled');
                            cieSecundarioSelect.setAttribute('aria-disabled', 'false');
                        } else {
                            cieSecundarioSelect.setAttribute('disabled', 'disabled');
                            cieSecundarioSelect.value = '';
                        }
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

    export const validacionTiposDocumento = (evento) => {


        const selectedOption = evento.options[evento.selectedIndex];
        const numeroDocumento = evento.form['numero_documento'];
        const pais = evento.form['pais'];
        paisSegunTipoDocumento(selectedOption.value, pais);
        numeroDocumento.setCustomValidity('');
        numeroDocumento.classList.remove('border-red-500', 'dark:border-red-600');


        if (selectedOption) {
            const codRips = selectedOption.dataset.valor;
            const tipoDoc = appState.tiposDocumento.find(tipo => tipo.cod_rips === codRips);
            const patron = new RegExp(tipoDoc.regex_validacion || '^[A-Z0-9]+$');

            if (!patron.test(numeroDocumento.value)) {
                numeroDocumento.classList.add('border-red-500', 'dark:border-red-600');
                numeroDocumento.setCustomValidity('El número de documento no es válido según el tipo de documento ' + tipoDoc.nombre);

                displayValidationErrors(evento.form, { numero_documento: ['El número de documento no es válido para ' + tipoDoc.nombre] });
                return;
            }
            if(codRips==="MS"|| codRips==="AS") {
                alert('Población Especial del Régimen Subsidiado que no están identificados por la Registraduría Nacional del Estado Civil (RNEC) y que se encuentren en el correspondiente listado censal');
            }

            if (!tipoDoc.cod_dian || tipoDoc.requiere_acudiente) {
                dom.handleShowAcompaniante.checked = true;
                dom.handleShowAcompaniante.disabled = true;
                dom.handleShowAcompaniante.classList.add('cursor-not-allowed', 'opacity-50');
            }
        }
    }

    const paisSegunTipoDocumento = (tipoDocumento,inputPais) => {
        const documentosNacionales = appState.tiposDocumento.filter(doc => doc.es_nacional);
        const nacional = documentosNacionales.some(doc => doc.cod_rips === tipoDocumento);
        if (nacional) {
            inputPais.value = '170';
            return;
        }

    }

    const actualizarPaisResidencia = (form) => {
        const resideColombia = form['reside_colombia'].value;
        const pais = form['pais_residencia'];
        if (resideColombia == 'si') {
            pais.value = '170';
        }
            pais.addEventListener('focus', () => displayPaieses(pais));

    }
    const paisResidencia= (form) => {
        const resideColombia = form['reside_colombia'];
        resideColombia.addEventListener('change', () => actualizarPaisResidencia(form));

    }

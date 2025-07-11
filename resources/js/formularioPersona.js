 import { DATA_KEYS,dom,appState } from "./variables";

 export const populateFormWithPersonaData = (form, persona) => {
        form['tipoGuardado'].value = DATA_KEYS.ACTUALIZAR_USUARIO;
        form['tipoGuardado'].textContent = "Actualziar Usuario";
        form['numero_documento'].value = persona.numero_documento;
        form['tipo_documento'].value = persona.tipo_documento;
        form['nombres'].value = persona.nombre;
        form['apellidos'].value = persona.apellido;
        form['fecha_nacimiento'].value = persona.fecha_nacimiento;
        form['telefono'].value = persona.telefono;
        form['correo'].value= persona.correo;

        // ... Llenar otros campos ...
        // Ejemplo para un campo complejo como el sexo:
        form.querySelectorAll('input[name="sexo"]').forEach(radio => {
            radio.checked = radio.value === persona.sexo;
        });

        if (form['perfil'].value === DATA_KEYS.PACIENTE) {
            dom.paciente.value = persona.id;
        } else {
            dom.acompaniante.value = persona.id;
        }
    };

    /**
     * Muestra mensajes de error de validación en un formulario.
     * @param {HTMLFormElement} form - El formulario donde mostrar el error.
     * @param {Object} errors - Objeto con los errores de validación.
     */
 export const displayValidationErrors = (form, errors) => {
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


    const crearOpcion = (label, valor) => {
        const item = document.createElement('option');
        item.value = valor;
        item.textContent = label;
        item.className = 'text-gray-700 dark:text-gray-300';
        item.dataset.valor = valor;
        item.dataset.label = label;
        return item;
    };

    export const displayPaieses = (selectActual) => {
        // Limpiar opciones existentes antes de agregar nuevas
        selectActual.innerHTML = '';
        // Agregar opciones de países
        appState.paises.forEach(pais => {
            selectActual.appendChild(crearOpcion(pais.nombre, pais.codigo_iso));
        });
    };

export const displayEps = (selectActual) => {
        // Limpiar opciones existentes antes de agregar nuevas
        selectActual.innerHTML = '';
        appState.eps.forEach(eps => {
            selectActual.appendChild(crearOpcion(eps.nombre, eps.nombre));
        });
    }
export const displayMunicipios = (selectActual,) => {
        // Limpiar opciones existentes antes de agregar nuevas
        selectActual.innerHTML = '';

        // Agregar opciones de municipios
        appState.municipios.forEach(municipio => {
            selectActual.appendChild(crearOpcion( `${municipio.municipio} - ${municipio.departamento}` , municipio.id));
        });
    }



import { appState, dom } from './variables.js';
import { fetchExamenes } from './api.js';
import { renderExamenes } from './crearExamenes.js';
import { handleFiltroExamenes, handleBuscarDocumento, handleUpdateExamenCantidad, handleGuardarPersona } from './manejadorEventos.js';
import { displayEps, displayPaieses,displayMunicipios,dispalyDocumentos,displayOpciones } from './formularioPersona.js';


const init = async () => {
    appState.todosLosExamenes = await fetchExamenes();
    appState.examenesVisibles = [...appState.todosLosExamenes]; // Clonar para la vista inicial
    renderExamenes(appState.examenesVisibles);

    dom.soloDiezYSeisMil?.addEventListener('change', handleFiltroExamenes);
    dom.busquedaExamenInput?.addEventListener('input', handleFiltroExamenes);

    dom.crearPaciente?.addEventListener('submit', handleGuardarPersona);
    dom.crearAcompaniante?.addEventListener('submit', handleGuardarPersona);

    // Usar querySelectorAll para asignar el mismo evento a múltiples elementos
    document.querySelectorAll('input[name="numero_documento"]').forEach(input => {
        input.addEventListener('blur', handleBuscarDocumento);
    });

    document.querySelectorAll('select[name="pais"]').forEach(currenFormPais => {
        currenFormPais.addEventListener('focus',() => displayPaieses(currenFormPais));

    });
    document.querySelectorAll('input[name="eps"]').forEach(currenFormEps  => {
        currenFormEps.addEventListener('focus', () =>   {
             displayEps(dom.listaEps);
        });
    });
    document.querySelectorAll('select[name="tipo_documento"]').forEach(currentFormMunicipio => {
        currentFormMunicipio.addEventListener('focus', () => {
            dispalyDocumentos(currentFormMunicipio);
        });
        currentFormMunicipio.addEventListener('change', (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            if (selectedOption) {
                const codRips = selectedOption.dataset.valor;
                const tipoDoc = appState.tiposDocumento.find(tipo => tipo.cod_rips === codRips);
                console.log('Tipo de documento seleccionado:', tipoDoc);
                if (!tipoDoc.cod_dian || tipoDoc.requiere_acudiente) {
                    console.log('Este tipo de documento requiere un acudiente o no tiene un código DIAN asociado.');
                }
            }
        }
        );
    }
    );
    document.querySelectorAll('input[name="municipioBusqueda"]').forEach(currentBusquedaFormMunicipio => {
      const formularioActual = currentBusquedaFormMunicipio.form;
      const contenedorMunicipios = formularioActual.querySelector('.municipio-busqueda');


        currentBusquedaFormMunicipio.addEventListener('input', () => {
        const searchValue = currentBusquedaFormMunicipio.value.toLowerCase();
           appState.filteredMunicipios = appState.municipios.filter(municipio =>
                municipio.municipio.toLowerCase().includes(searchValue) ||
                municipio.departamento.toLowerCase().includes(searchValue)

            );
        displayOpciones(formularioActual, appState.filteredMunicipios);
        console.log('Filtered municipios:', appState.filteredMunicipios);

        });
    }
    );

    document.querySelectorAll('select[name="municipio"]').forEach(currentFormMunicipio => {
        currentFormMunicipio.addEventListener('focus',()=>displayMunicipios(currentFormMunicipio))
    })

    // Event Delegation para los inputs de cantidad de exámenes. Es más eficiente.
    dom.examenesContainer?.addEventListener('input', handleUpdateExamenCantidad);
    dom.examenesContainer?.addEventListener('blur', handleUpdateExamenCantidad, true); // Usar captura para el blur

}

document.addEventListener('DOMContentLoaded', init);

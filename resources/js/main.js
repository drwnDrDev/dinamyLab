
import { appState, dom } from './variables.js';
import { fetchExamenes } from './api.js';
import { renderExamenes } from './crearExamenes.js';
import { handleFiltroExamenes, handleBuscarDocumento, handleUpdateExamenCantidad, handleGuardarPersona,notificarGuardado, validacionTiposDocumento,handleBuscarMunicipio } from './manejadorEventos.js';
import { displayEps, displayPaieses,displayDocumentos } from './formularioPersona.js';


const init = async () => {
    appState.todosLosExamenes = await fetchExamenes();
    appState.examenesVisibles = [...appState.todosLosExamenes]; // Clonar para la vista inicial
    renderExamenes(appState.examenesVisibles);
    // Asignar manejadores de eventos
    dom.busquedaExamenInput?.addEventListener('input', handleFiltroExamenes);

    dom.crearPaciente?.addEventListener('submit',async (e)=> {
      e.preventDefault();
      const persona = await handleGuardarPersona(e);

      notificarGuardado(persona,true,e.target);
    });
    dom.crearAcompaniante?.addEventListener('submit',async(e)=> {
      e.preventDefault();
      const persona = await handleGuardarPersona(e);

      notificarGuardado(persona,false,e.target);
    });

    // Usar querySelectorAll para asignar el mismo evento a múltiples elementos
    document.querySelectorAll('input[name="numero_documento"]').forEach(input => {
        input.addEventListener('blur', handleBuscarDocumento);
    });
    document.querySelectorAll('select[name="pais_residencia"]').forEach(currenFormPais => {
        currenFormPais.addEventListener('focus',() => displayPaieses(currenFormPais));

    });
        document.querySelectorAll('select[name="pais"]').forEach(currenFormPais => {
        currenFormPais.addEventListener('focus',() => displayPaieses(currenFormPais));

    });

    document.querySelectorAll('input[name="eps"]').forEach(currenFormEps  => {
        currenFormEps.addEventListener('focus', () =>   {
             displayEps(dom.listaEps);
        });
    });
    document.querySelectorAll('select[name="tipo_documento"]').forEach(currentForm => {
        currentForm.addEventListener('focus', () => {
            displayDocumentos(currentForm);
        });
        currentForm.addEventListener('change', (e) => {
            validacionTiposDocumento(e.target);
        });
    });
    document.querySelectorAll('input[name="municipioBusqueda"]').forEach( (bMunicipio) => handleBuscarMunicipio(bMunicipio));
    dom.examenesContainer?.addEventListener('input', handleUpdateExamenCantidad);
    dom.examenesContainer?.addEventListener('blur', handleUpdateExamenCantidad, true);

}

document.addEventListener('DOMContentLoaded', init);


import { appState,dom } from './variables.js';
import { fetchExamenes } from './api.js';
import { renderExamenes } from './crearExamenes.js';
import { handleFiltroExamenes,handleBuscarDocumento,handleUpdateExamenCantidad,handleGuardarPersona } from './manejadorEventos.js';


const init =async () => {
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

        // Event Delegation para los inputs de cantidad de exámenes. Es más eficiente.
        dom.examenesContainer?.addEventListener('input', handleUpdateExamenCantidad);
        dom.examenesContainer?.addEventListener('blur', handleUpdateExamenCantidad, true); // Usar captura para el blur

}

document.addEventListener('DOMContentLoaded', init);

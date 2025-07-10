
import { DATA_KEYS, appState } from './variables.js';
import { fetchExamenes, fetchPersonaPorDocumento } from './api.js';
import { renderExamenes } from './crearExamenes.js';
import { handleFiltroExamenes } from './manejadorEventos.js';
import {dom} from './variables.js'





const init =async () => {
    appState.todosLosExamenes = await fetchExamenes();
    appState.examenesVisibles = [...appState.todosLosExamenes]; // Clonar para la vista inicial

    console.log(appState.examenesVisibles)

    renderExamenes(appState.examenesVisibles);





}

document.addEventListener('DOMContentLoaded', init);

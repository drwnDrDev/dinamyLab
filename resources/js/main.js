
import { DATA_KEYS, appState } from './variables.js';
import { fetchExamenes, fetchPersonaPorDocumento } from './api.js';
import { renderExamenes } from './crearExamenes.js';

const dom = {
 crearPaciente: document.getElementById(DATA_KEYS.CREAR_PACIENTE),
 crearAcompaniante: document.getElementById(DATA_KEYS.CREAR_ACOMPANIANTE),
 paciente: document.getElementById(DATA_KEYS.PACIENTE_ID),
 acompaniante: document.getElementById(DATA_KEYS.ACOMPANIANTE_ID),
 soloDiezYSeisMil: document.getElementById('16000'),
 busquedaExamen: document.getElementById(DATA_KEYS.BUSQUEDA_EXAMEN),
 examenesContainer: document.getElementById('examenesContainer'),
 totalExamenesSpan: document.getElementById('totalExamenes'),
}

const init =async () => {
    const examenes = await fetchExamenes();
    appState.todosLosExamenes = examenes;
    appState.examenesVisibles = examenes; // Inicialmente, todos los examenes son visibles

    renderExamenes(examenes, dom.examenesContainer, appState.examenesVisibles, dom.totalExamenesSpan);


}

document.addEventListener('DOMContentLoaded', init);

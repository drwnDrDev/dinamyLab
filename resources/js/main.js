
import { DATA_KEYS } from './variables.js';
import { documentos } from './variables.js';
import { paises } from './variables.js';
import { municipios } from './variables.js';
import { eps } from './variables.js';
import { fetchExamenes, fetchPersonaPorDocumento } from './api.js';

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
    const persona = await fetchPersonaPorDocumento(156323);


}

document.addEventListener()
init()

export const DATA_KEYS = Object.freeze({
    PACIENTE: 'Paciente',
    ACOMPANIANTE: 'Acompañante',
    NUEVO_USUARIO: 'nuevoUsuario',
    ACTUALIZAR_USUARIO: 'actualizarUsuario',
    PACIENTE_ID: 'paciente_id',
    ACOMPANIANTE_ID: 'acompaniante_id',
    TIPO_DOCUMENTO: 'tipo_documento',
    tiposDocumento: 'tipos_documento_data',
    paises: 'paises_data',
    municipios: 'municipios_data',
    eps: 'eps_data',
    lastUpdate: 'frontend_data_last_update',
    CREAR_PACIENTE:'crearPaciente',
    CREAR_ACOMPANIANTE:'crearacompaniante',
    BUSQUEDA_EXAMEN:'busquedaExamen'

});

export const appState = {
        todosLosExamenes: [],
        examenesVisibles: [],

        tiposDocumento: JSON.parse(localStorage.getItem('tipos_documento_data')) || [],
        municipios: JSON.parse(localStorage.getItem('municipios_data')) || [],
        paises: JSON.parse(localStorage.getItem('paises_data')) || [],
        eps: JSON.parse(localStorage.getItem('eps_data')) || [],
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        filteredMunicipios: [],
    };

export const dom = {
 crearPaciente: document.getElementById(DATA_KEYS.CREAR_PACIENTE),
 crearAcompaniante: document.getElementById(DATA_KEYS.CREAR_ACOMPANIANTE),
 paciente: document.getElementById(DATA_KEYS.PACIENTE_ID),
 acompaniante: document.getElementById(DATA_KEYS.ACOMPANIANTE_ID),
 soloDiezYSeisMil: document.getElementById('16000'),
 busquedaExamenInput: document.getElementById(DATA_KEYS.BUSQUEDA_EXAMEN),
 examenesContainer: document.getElementById('examenesContainer'),
 totalExamenesSpan: document.getElementById('totalExamenes'),
 listaEps: document.getElementById('lista_eps'),
}


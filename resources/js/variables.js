export const DATA_KEYS = Object.freeze({
    PACIENTE: 'Paciente',
    ACOMPANIANTE: 'Acompañante',
    NUEVO_USUARIO: 'nuevoUsuario',
    ACTUALIZAR_USUARIO: 'actualizarUsuario',
    PACIENTE_ID: 'paciente_id',
    ACOMPANIANTE_ID: 'acompaniante_id',
    TIPO_DOCUMENTO: 'tipo_documento',
    DOCUMENTOS_PACIENTE: 'documentos_paciente',
    PAISES: 'paises',
    MUNICIPIOS: 'municipios',
    EPS: 'eps',
    CREAR_PACIENTE: 'crearPaciente',
    CREAR_ACOMPANIANTE: 'crearacompaniante',
    BUSQUEDA_EXAMEN: 'busquedaExamen'

});

export const appState = {
        todosLosExamenes: [],
        examenesVisibles: [],
        verFomularioPaciente: false,
        verFomularioAcompaniante: false,
        tiposDocumento: JSON.parse(localStorage.getItem(DATA_KEYS.DOCUMENTOS_PACIENTE)) || [],
        municipios: JSON.parse(localStorage.getItem(DATA_KEYS.MUNICIPIOS)) || [],
        paises: JSON.parse(localStorage.getItem(DATA_KEYS.PAISES)) || [],
        eps: JSON.parse(localStorage.getItem(DATA_KEYS.EPS)) || [],
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        filteredMunicipios: [],
    };

export const dom = {
 crearPaciente: document.getElementById(DATA_KEYS.CREAR_PACIENTE),
 crearAcompaniante: document.getElementById(DATA_KEYS.CREAR_ACOMPANIANTE),
 paciente: document.getElementById(DATA_KEYS.PACIENTE_ID),
 acompaniante: document.getElementById(DATA_KEYS.ACOMPANIANTE_ID),
 handleShowAcompaniante: document.getElementById('mostrarAcompaniante'),
 busquedaExamenInput: document.getElementById(DATA_KEYS.BUSQUEDA_EXAMEN),
 examenesContainer: document.getElementById('examenesContainer'),
 totalExamenesSpan: document.getElementById('totalExamenes'),
 listaEps: document.getElementById('lista_eps'),
 residenteColombiano: document.getElementById('residenteColombiano'),
 guardarOtro: document.getElementById('guardarOtro')
}


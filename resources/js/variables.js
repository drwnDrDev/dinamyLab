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

export const documentos = JSON.parse(localStorage.getItem(DATA_KEYS.tiposDocumento)).pacientes || [];
export const paises = JSON.parse(localStorage.getItem(DATA_KEYS.paises)) || [];
export const municipios = JSON.parse(localStorage.getItem(DATA_KEYS.municipios)) || [];
export const eps = JSON.parse(localStorage.getItem(DATA_KEYS.eps)) || [];

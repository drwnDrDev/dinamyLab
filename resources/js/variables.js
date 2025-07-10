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
        municipios: JSON.parse(localStorage.getItem('municipios_data')) || [],
        paises: JSON.parse(localStorage.getItem('paises_data')) || [],
        eps: JSON.parse(localStorage.getItem('eps_data')) || [],
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    };

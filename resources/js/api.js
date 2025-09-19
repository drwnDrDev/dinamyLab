import {displayValidationErrors} from './formularioPersona.js';
import {appState} from './variables.js';
import axios from 'axios';
const TOKEN= document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const apiClient = axios.create({
        headers: {
            'X-CSRF-TOKEN': TOKEN,
            'Accept': 'application/json',
        }
    });

export const fetchExamenes = async () => {

    try {
        const response = await apiClient.get('/api/examenes');
         return response.data.data.examenes || [];

    } catch (error) {
         console.error('Error al obtener los exámenes:', error);
         return [];
    }
}

export const fetchPersonaPorDocumento = async (numeroDocumento) => {
        if (numeroDocumento.length <= 3) return null;
        try {
            const response = await apiClient.get(`/api/personas/buscar/${numeroDocumento}`);


            return response.data.data || null;
        } catch (error) {
            // Un 404 es esperado si el usuario no existe, no es un error crítico.
            if (error.response?.status !== 404) {
                console.error("Error al buscar persona:", error);
            }
            return null;
        }
    };

export const fetchMunicipios = async () => {
    try {
        const response = await apiClient.get('/api/municipios');
        return response.data.data.municipios || [];
    } catch (error) {
        console.error('Error al obtener los municipios:', error);
        return [];
    }
}
export const fetchTiposDocumento = async () => {
    try {
        const response = await apiClient.get('/api/tipos-documento');
        localStorage.setItem('tipos_documento_data', JSON.stringify(response.data.data.tipos_documento || []));
        appState.tiposDocumento = response.data.data.tipos_documento || [];
        return response.data.data.tipos_documento || [];
    } catch (error) {
        console.error('Error al obtener los tipos de documento:', error);
        return [];
    }
}
export const fetchPaises = async () => {
    try {
        const response = await apiClient.get('/api/paises');
        return response.data.data.paises || [];
    } catch (error) {
        console.error('Error al obtener los países:', error);
        return [];
    }
}

export const  guardarPersona =  (url, formData) =>{

    try {
        return apiClient.post(url, formData)
            .then(response => {
                if (response.data.status === 'error') {
                    displayValidationErrors(document.querySelector('form'), response.data.errors);
                    return null;
                }
                return response.data.data;
            })
            .catch(error => {
                console.error("Error al guardar persona:", error);
                if (error.response?.data?.errors) {
                    displayValidationErrors(document.querySelector('form'), error.response.data.errors);
                }
                return null;
            });
    } catch (error) {
        console.error("Error en la solicitud:", error);
        displayValidationErrors(document.querySelector('form'), { general: ['Error al procesar la solicitud.'] });
        return null;
    }

    };
export const fetchFinalidades = async () => {
    try {
        const response = await apiClient.get('/api/finalidades');
        return response.data.data.finalidades || [];
    } catch (error) {
        console.error('Error al obtener las finalidades:', error);
        return [];
    }
}
export const fetchServiciosHabilitados = async () => {
    try {
        const response = await apiClient.get('/api/servicios-habilitados');
        return response.data.data|| [];
    } catch (error) {
        console.error('Error al obtener los servicios habilitados:', error);
        return [];
    }
}
export const fetchViaIngreso = async () => {
    try {
        const response = await apiClient.get('/api/via-ingreso');
        return response.data.data|| [];
    } catch (error) {
        console.error('Error al obtener las vías de ingreso:', error);
        return [];
    }
}
export const fetchCausasExternas = async () => {
    try {
        const response = await apiClient.get('/api/causa-atencion');
        return response.data.data.causas_externas || [];
    } catch (error) {
        console.error('Error al obtener las causas externas:', error);
        return [];
    }
}
export const fetchDiagnosticos = async () => {
    try {
        const response = await apiClient.get('/api/cie10');
        return response.data.data.codigoDiagnostico || [];
    } catch (error) {
        console.error('Error al obtener los diagnósticos:', error);
        return [];
    }
}
export const fetchTiposAfiliacion = async () => {
    try {
        const response = await apiClient.get('/api/tipos-afiliacion');
        return response.data.data.tipos_afiliacion || [];
    } catch (error) {
        console.error('Error al obtener los tipos de afiliación:', error);
        return [];
    }
}
export const fetchTiposAtencion = async () => {
    try {
        const response = await apiClient.get('/api/modalidades-atencion');
        return response.data.data.modalidades_atencion || [];
    } catch (error) {
        console.error('Error al obtener los tipos de atención:', error);
        return [];
    }
}

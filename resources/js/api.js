import {displayValidationErrors} from './formularioPersona.js';
import {appState} from './variables.js';
import axios from 'axios';

axios.defaults.withCredentials = true;

// Función para obtener el token CSRF del meta tag
const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
};

// Crear el cliente sin await en nivel superior
const apiClient = axios.create({

    withCredentials: true,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});

// Interceptor para agregar el CSRF token en cada request
apiClient.interceptors.request.use(
    async (config) => {
        // Obtener cookie de Sanctum si aún no existe
        if (!document.cookie.includes('XSRF-TOKEN')) {
            await axios.get('/sanctum/csrf-cookie');
        }

        // Agregar CSRF token al header
        const token = getCsrfToken();
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor para manejar errores de autenticación
apiClient.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            console.error('No autenticado. Redirigiendo al login...');
            window.location.href = '/login';
        }
        if (error.response?.status === 419) {
            console.error('CSRF token expirado. Recargando página...');
            window.location.reload();
        }
        return Promise.reject(error);
    }
);

export const fetchExamenes = async () => {
    try {
        const response = await apiClient.get('/examenes'); // Ya no necesitas '/api'
        return response.data.data.examenes || [];
    } catch (error) {
        console.error('Error al obtener los exámenes:', error);
        return [];
    }
}

export const fetchPersonaPorDocumento = async (numeroDocumento) => {
    if (numeroDocumento.length <= 3) return null;
    try {
        const response = await apiClient.get(`/personas/buscar/${numeroDocumento}`);
        return response.data.data || null;
    } catch (error) {
        if (error.response?.status !== 404) {
            console.error("Error al buscar persona:", error);
        }
        return null;
    }
};

export const fetchMunicipios = async () => {
    try {
        const response = await apiClient.get('/municipios');
        return response.data.data.municipios || [];
    } catch (error) {
        console.error('Error al obtener los municipios:', error);
        return [];
    }
}
export const fetchTiposDocumento = async () => {
    try {
        const response = await apiClient.get('/tipos-documento');
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
        const response = await apiClient.get('/paises');
        return response.data.data.paises || [];
    } catch (error) {
        console.error('Error al obtener los países:', error);
        return [];
    }
}

export const guardarPersona =  (url, formData) =>{

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
                console.error("Error al guardar persona:", error,error.response.data);
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
        const response = await apiClient.get('/finalidades');
        return response.data.data.finalidades || [];
    } catch (error) {
        console.error('Error al obtener las finalidades:', error);
        return [];
    }
}
export const fetchServiciosHabilitados = async () => {
    try {
        const response = await apiClient.get('/servicios-habilitados');
        return response.data.data|| [];
    } catch (error) {
        console.error('Error al obtener los servicios habilitados:', error);
        return [];
    }
}
export const fetchViaIngreso = async () => {
    try {
        const response = await apiClient.get('/via-ingreso');
        return response.data.data|| [];
    } catch (error) {
        console.error('Error al obtener las vías de ingreso:', error);
        return [];
    }
}
export const fetchCausasExternas = async () => {
    try {
        const response = await apiClient.get('/causa-atencion');
        return response.data.data.causas_externas || [];
    } catch (error) {
        console.error('Error al obtener las causas externas:', error);
        return [];
    }
}
export const fetchDiagnosticos = async () => {
    try {
        const response = await apiClient.get('/cie10');
        return response.data.data || [];
    } catch (error) {
        console.error('Error al obtener los diagnósticos:', error);
        return [];
    }
}
export const fetchTiposAfiliacion = async () => {
    try {
        const response = await apiClient.get('/tipos-afiliacion');
        return response.data.data || [];
    } catch (error) {
        console.error('Error al obtener los tipos de afiliación:', error);
        return [];
    }
}
export const fetchTiposAtencion = async () => {
    try {
        const response = await apiClient.get('/modalidades-atencion');
        return response.data.data.modalidades_atencion || [];
    } catch (error) {
        console.error('Error al obtener los tipos de atención:', error);
        return [];
    }
}

export const fetchOrden = async (id) => {
    try {
        const response = await apiClient.get(`/orden/${id}`);
        return response.data || null;
    } catch (error) {
        console.error('Error al obtener la orden:', error);
        return null;
    }
}

export const guardarOrden = async (url, formData) => {
    try {
        const response = await apiClient.post(url, formData);
        if (response.data.status === 'error') {
            displayValidationErrors(document.querySelector('form'), response.data.errors);
            return null;
        }
        return response.data.data || null;
    } catch (error) {
        console.error("Error al guardar la orden:", error);
        if (error.response?.data?.errors) {
            displayValidationErrors(document.querySelector('form'), error.response.data.errors);
        }
        return null;
    }
}

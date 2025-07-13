
const TOKEN= document.querySelector('meta[name="csrf-token"]').getAttribute('content');
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

export const guardarPersona = (url, formData) =>{

            try {
            const response = apiClient.post(url, formData);
               return response;

            } catch (error) {
                if (error.response?.status === 422) { // Error de validación
                    displayValidationErrors(form, error.response.data.errors);
                } else {
                    console.error('Error al guardar:', error);
                    console.error('Paciente no guardado')
                }
            }
}

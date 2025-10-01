import axios from "axios";

const TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const apiClient = axios.create({
    headers: {
        'X-CSRF-TOKEN': TOKEN,
        'Accept': 'application/json',
    }
});

const ordenTestData = {
        paciente: 5,
        numero_orden: '123456',
        paciente_id: null,
        examenes: [1,5,10],
        cod_servicio: 712,
        via_ingreso: '01',
        cie_principal: null,
        cie_relacionado: null,
        finalidad: '15',
        causa_externa: '38',
        modalidad: '01',
        fecha_orden: new Date().toISOString().slice(0, 10), // Formato YYYY-MM-DD hh:mm:ss
    };


export const ordenCreate  = async (formData) => {
    try {
        const response = await apiClient.post('/api/ordenes', formData);
        return response.data;
    } catch (error) {
        console.error('Error creating orden:', error);
        throw error;
    }
}


const testButton = document.getElementById('testApiButton');
if (testButton) {
    testButton.addEventListener('click', async () => {
        try {
            console.log('Creating test orden with data:', ordenTestData);
            const result = await ordenCreate(ordenTestData);
            console.log('Orden created successfully:', result);
            alert('Orden created successfully. Check console for details.');
        } catch (error) {
            console.error('Error during test orden creation:', error);
            alert('Error creating orden. Check console for details.');
        }
    });
}

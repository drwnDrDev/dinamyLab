import {guardarOrden} from './api.js';

const form = document.getElementById('resultadoForm');

if (form) {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(form);
        console.log('Formulario enviado', JSON.stringify(formData));

        const currentUrl = new URL(window.location.href);
        const idFromPath = currentUrl.pathname.split('/');
        const procedimientoId = idFromPath[2];
        const url = `/api/resultados/${procedimientoId}`;
        const result = await guardarOrden(url, formData);
        if (result) {

            window.location.href = '/resultados'; // Redirigir a la lista de resultados
        } else {
            alert('Hubo un error al guardar el resultado. Por favor, verifica los datos e intenta nuevamente.');
        }
    });
}


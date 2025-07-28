   // Función para cargar y almacenar los datos
    async function loadAndStoreFrontendData() {
        const dataKeys = {
            documentosPaciente: 'documentos_paciente',
            documentosPagador: 'documentos_pagador',
            paises: 'paises',
            municipios: 'municipios',
            eps: 'eps'
        };

        try {

            // Asegúrate que la URL coincida con la definida en routes/api.php
            const response = await fetch('/api/static-data-for-frontend');


            if (!response.ok) {
                throw new Error(`Error en la respuesta del servidor: ${response.status} ${response.statusText}`);
            }

            const serverData = await response.json();
            console.log('Datos recibidos del servidor:', serverData.documentosPaciente);

            if (serverData.error) {
                console.error('Error recibido del servidor:', serverData.error);
                return;
            }




            // Almacenar cada conjunto de datos en localStorage
            if (serverData.documentos_paciente) {
                console.log('Guardando documentos de Paciente en localStorage...');

                localStorage.setItem(dataKeys.documentosPaciente, JSON.stringify(serverData.documentos_paciente));
                console.log('Documentos de Paciente guardados en localStorage.');
            }

            if (serverData.documentos_pagador) {
                localStorage.setItem(dataKeys.documentosPagador, JSON.stringify(serverData.documentos_pagador));
                console.log('Documentos de Pagador guardados en localStorage.');
            }

            if (serverData.paises) {
                localStorage.setItem(dataKeys.paises, JSON.stringify(serverData.paises));
                console.log('Países guardados en localStorage.');
            }

            if (serverData.municipios) {
                localStorage.setItem(dataKeys.municipios, JSON.stringify(serverData.municipios));
                console.log('Municipios guardados en localStorage.');
            }
            if (serverData.eps) {
                localStorage.setItem(dataKeys.eps, JSON.stringify(serverData.eps));
                console.log('EPS guardadas en localStorage.');
            }


            console.log('Estáticos han sido cargados y almacenados en localStorage.');

        } catch (error) {
            console.error('Error al cargar o almacenar datos para el frontend:', error);
            // Aquí podrías implementar una lógica de reintento o notificación al usuario.
        }
    }

    // Estrategia de carga:
    // Puedes llamar a esta función cuando la página carga, o bajo ciertas condiciones.
    // Por ejemplo, solo cargar si los datos no existen o si son muy antiguos.

    function checkAndLoadData() {
        const lastUpdate = localStorage.getItem('frontend_data_last_update');
        const oneDayInMilliseconds = 24 * 60 * 60 * 1000;

        // Cargar si no hay timestamp o si los datos tienen más de 1 día de antigüedad
        if (!lastUpdate || (new Date() - new Date(lastUpdate)) > oneDayInMilliseconds) {
            console.log('Datos en localStorage no encontrados o desactualizados. Cargando desde el servidor...');
            loadAndStoreFrontendData();
        } else {
            console.log('Datos estáticos ya existen en localStorage y están actualizados.');
            // Opcionalmente, puedes verificar si todos los items existen:
            // if (!localStorage.getItem('tipos_documento_data') || !localStorage.getItem('paises_data') || !localStorage.getItem('municipios_data')) {
            //    loadAndStoreFrontendData();
            // }
        }
    }

    // Ejecutar al cargar el DOM:
    document.addEventListener('DOMContentLoaded', () => {
        checkAndLoadData();

        // Ejemplo de cómo acceder a los datos (opcional)
        // setTimeout(() => {
        //     const tipos = JSON.parse(localStorage.getItem('tipos_documento_data'));
        //     if (tipos) {
        //         console.log('Ejemplo de acceso a Tipos de Documento desde localStorage:', tipos[0]);
        //     }
        // }, 1000);
    });

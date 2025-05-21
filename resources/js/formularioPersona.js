import axios from "axios";

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['Accept'] = 'application/json';

const busqudaMunicipio = document.getElementById('busqudaMunicipio');
const numeroDocumento = document.getElementById('numero_documento');
const tipoDocumento = document.getElementById('tipo_documento');
const pais = document.getElementById('pais');
const nacional = pais?.value=="COL" || pais==null;


tipoDocumento.addEventListener('change', function (e) {
    const tipo = tipoDocumento.value;
    if (tipo === 'CC') {
        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de cédula');
        pais.className = 'hidden';


    } else if (tipo === 'TI') {
        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de tarjeta de identidad');
        pais.className = 'hidden';
    } else if (tipo === 'CE') {
        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de cédula de extranjería');
         pais.className = 'hidden';
    } else if (tipo === 'PA') {
        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de pasaporte');
        pais.style.display = 'flex';
        pais.addEventListener('focus', function (e) {
            axios.get('/api/paises')
                .then(response => {

                    if (response.data) {

                        response.data.data.paises.forEach(p => {
                            const option = document.createElement('option');
                            option.value = p.codigo_iso;
                            option.textContent = p.nombre;
                            option.className= ['text-gray-900', 'dark:text-gray-100'];

                            pais.appendChild(option);
                        });
                    } else {
                        alert("No se encontró el país");
                    }
                })
                .catch(error => {
                    console.error('Error fetching countries:', error);
                });
        });

    }
});

pais.addEventListener('change', function (e) {
    const paisSeleccionado = pais;


    if (paisSeleccionado !== 'COL') {
      numeroDocumento.value = paisSeleccionado.value;
    }
})



        const typeableInput = document.getElementById('municipioBusqueda');
        const optionsList = document.getElementById('opciones');
        const hiddenSelect = document.getElementById('municipio');
        let allOptions = Array.from(hiddenSelect.options).map(option => ({
            value: option.value,
            text: option.text
        }));
        // Inicializar la lista de opciones
        console.log(allOptions);

        function filterOptions(query) {
            const filteredOptions = allOptions.filter(option =>
                option.text.toLowerCase().includes(query.toLowerCase())
            );
            displayOptions(filteredOptions);
        }

        function displayOptions(options) {
            optionsList.innerHTML = ''; // Limpiar la lista anterior
            if (options.length > 0) {
                options.forEach(option => {
                    const optionElement = document.createElement('div');
                    optionElement.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-200', 'dark:hover:bg-gray-700');
                    optionElement.textContent = option.text;
                    optionElement.addEventListener('click', () => {
                        typeableInput.value = option.text;
                        hiddenSelect.value = option.value;
                        optionsList.classList.remove('block');
                    });
                    optionsList.appendChild(optionElement);
                });
                 optionsList.classList.remove('hidden');
                optionsList.classList.add('block');
            } else {
                optionsList.classList.remove('block');
                optionsList.classList.add('hidden');
            }
        }

        typeableInput.addEventListener('input', () => {
            const query = typeableInput.value;
            filterOptions(query);
        });

        // Ocultar la lista al perder el foco del input
        typeableInput.addEventListener('blur', () => {
            // Pequeño retraso para permitir que se dispare el clic de la opción
            setTimeout(() => {
                optionsList.classList.remove('block');
            }, 200);
        });

        // Mostrar todas las opciones al enfocar el input (opcional)
        typeableInput.addEventListener('focus', () => {
            filterOptions(typeableInput.value); // Mostrar las opciones filtradas o todas si el input está vacío
        });

        // Inicializar la lista (opcional, si quieres mostrar algo al cargar)
        // displayOptions(allOptions);


// let municipios = axios.get('/api/municipios')
//     .then(response => {
//         if (response.data) {
//             municipios = response.data;
//         } else {
//             alert("No se encontró el municipio");
//         }
//     })
//     .catch(error => {
//         console.error('Error fetching municipios:', error);
//     });


// // Función para mostrar resultados (puedes adaptarla a tu HTML)
// function mostrarResultados(resultados) {
//     // Ejemplo: mostrar en consola
//     console.log('Resultados:', resultados);
// }

// busqudaMunicipio.addEventListener('keyup', function (e) {
//     const busqueda = busqudaMunicipio.value.trim().toLowerCase();
//     if (busqueda.length === 0) {
//         mostrarResultados([]);
//         return;
//     }
//     const resultados = municipios.filter(municipio =>
//         municipio.nombre.toLowerCase().includes(busqueda)
//     );
//     mostrarResultados(resultados);
// });

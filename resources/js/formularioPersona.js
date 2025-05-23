import axios from "axios";

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['Accept'] = 'application/json';

const numeroDocumento = document.getElementById('numero_documento');
const tipoDocumento = document.getElementById('tipo_documento');
const pais = document.getElementById('pais');


tipoDocumento.addEventListener('change', function (e) {
    const tipo = tipoDocumento.value;
    if (tipo === 'CC'|| tipo === 'RC' || tipo === 'TI' || tipo === 'CE') {
        numeroDocumento.setType('number');
        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de documento');
        pais.className = 'hidden';

    }  else if (tipo === 'PA' || tipo === 'PP' || tipo === 'PE' || tipo === 'PS' || tipo === 'PT' || tipo === 'AS'|| tipo ==="MS") {
        numeroDocumento.setAttribute('maxlength', '16');
        numeroDocumento.setAttribute('placeholder', 'Identificación temporal');
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
                    optionElement.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-200', 'dark:hover:bg-gray-700','capitalize');
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
                optionsList.classList.add('hidden');
            }, 200);
        });

        // Mostrar todas las opciones al enfocar el input (opcional)
        typeableInput.addEventListener('focus', () => {
            filterOptions(typeableInput.value); // Mostrar las opciones filtradas o todas si el input está vacío
        });


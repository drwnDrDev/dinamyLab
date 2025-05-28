// const examenes= document.getElementById('examenes');
const buscarPersona = document.getElementById('buscarPersona');
const tipoDocumento = document.getElementById('tipo_documento');
const numeroDocumento = document.getElementById('numero_documento');
const pais = document.getElementById('pais');



const persona = new Object();
const ordenMedcia = {
    paciente :{
        id: null,
        nombres: null,
        apellidos: null,
        tipo_documento: null,
        numero_documento: null,
        fecha_nacimiento: null,
        sexo: null,
        direccion: null,
        telefono: null,
        email: null,
    },
    acompaniante: {
        id: null,
        nombres: null,
        apellidos: null,
        tipo_documento: null,
        numero_documento: null,
        telefono: null
    },
    examenes: [],
};


const setPersona = (data) => {
    persona.id = data.id;
    persona.nombre = data.nombre;
    persona.apellido = data.apellido;
    persona.numero_documento = data.numero_documento;
    persona.fecha_nacimiento = data.fecha_nacimiento;
    persona.direccion = data.direccion;
    persona.telefono = data.telefono;
    persona.email = data.email;
    persona.sexo = data.sexo;
    persona.tipo_documento = data.tipo_documento;
}

const setPaciente = (data) => {
    paciente.id = data.id;
    paciente.nombres = data.nombre;
    paciente.apellidos = data.apellido;
    paciente.tipo_documento = data.tipo_documento;
    paciente.numero_documento = data.numero_documento;
    paciente.telefono = data.telefono;
}

document.getElementById('crearPeronsa').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    axios.post('/api/personas', formData, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        alert('Formulario enviado con éxito ✅');
        console.log(response.data);
    })
    .catch(error => {
        if (error.response?.status === 422) {
            console.warn('Errores de validación:', error.response.data.errors);
            const errors = error.response.data.errors;
            const errorMessages = Object.values(errors).flat();
            const errorList = document.createElement('ul');
            errorMessages.forEach(errorMessage => {
                const listItem = document.createElement('li');
                listItem.textContent = errorMessage;
                errorList.appendChild(listItem);
            });
            const errorContainer = document.createElement('div');
            errorContainer.classList.add('text-sm','text-red-600','space-y-1');
            errorContainer.appendChild(errorList);
            form.appendChild(errorContainer);
            setTimeout(() => {
                errorContainer.remove();
            }, 5000);
        } else {
            console.error('Error desconocido:', error);
        }
    });
});



// axios.get("/api/examenes")
//     .then(response => {
//         response.data.data.examenes.map(examen => {
//             const label = document.createElement('label');
//             label.classList.add('form-check-label');
//             label.setAttribute('for', examen.id);
//             label.textContent = examen.nombre;
//             examenes.appendChild(label);
//             const option = document.createElement('input');
//             option.classList.add('form-check-input');
//             option.type = 'checkbox';
//             option.name = 'examenes[]';
//             option.id = examen.id;
//             option.value = examen.id;
//             option.textContent = examen.nombre;
//             examenes.appendChild(option);
//         }
//         );
//     })
//     .catch(error => {
//         console.error("Error fetching examenes:", error);
//     });
tipoDocumento.addEventListener('change', function (e) {
    const tipo = tipoDocumento.value;
    if (tipo === 'CC'|| tipo === 'RC' || tipo === 'TI' || tipo === 'CE') {

        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de documento');
        pais.style.display = 'none';

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


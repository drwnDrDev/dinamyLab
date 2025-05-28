
const buscarPersona = document.getElementById('buscarPersona');
const tipoDocumento = document.getElementById('tipo_documento');
const numeroDocumento = document.getElementById('numero_documento');

const pais = document.getElementById('pais');

document.getElementById('perfil').addEventListener('change', function (e) {
    if(e.target.value === 1) {
        document.getElementById('fecha_nacimiento').setAttribute('required', 'required');
        document.getElementById('sexo').setAttribute('required', 'required');
        document.getElementById('municipio').setAttribute('required', 'required');
    }else{
        document.getElementById('fecha_nacimiento').removeAttribute('required');
        document.getElementById('sexo').removeAttribute('required');
        document.getElementById('municipio').removeAttribute('required');
    }
})

const usuario = {
    id: null
}



const tipoGuardado = document.getElementById('tipoGuardado');

const setTipoGuardado = () => {
    const tipo = usuario.id ? 'actualizar' : 'crear';
    tipoGuardado.textContent = tipo;
}

document.getElementById('crearPersona').addEventListener('submit', function (e) {
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
        usuario.id= response.data.data.persona.id;

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


numeroDocumento.addEventListener('blur', function (e) {
    e.preventDefault();
    const baseUrl = '/api/personas/buscar/';
    const numero_documento = document.getElementById('numero_documento').value;
    if (numero_documento.length>3) {
    const fullUrl = baseUrl + numero_documento;
    axios.get(fullUrl)
        .then(response => {
            if (response.data?.persona?.nombre) {

                const persona =response.data.persona;
                usuario.id = persona.id;

                document.getElementById('nombres').value = persona.nombre;
                document.getElementById('apellidos').value = persona.apellido;
                document.getElementById('numero_documento').value = persona.numero_documento;
                document.getElementById('fecha_nacimiento').value = persona.fecha_nacimiento;
                document.getElementById('direccion').value = persona.direccion;
                document.getElementById('telefono').value = persona.telefono;
                document.getElementById('correo').value = persona.email;
                document.getElementById('sexo').value = persona.sexo;
                document.getElementById('tipo_documento').value = persona.tipo_documento;
            } else {
                alert("No se encontró la persona");
            }
        })
        .catch(error => {
            console.error("Error fetching persona:", error);
        }).then(() => {setTipoGuardado()});
}
;

}
);

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
                optionsList.classList.add('flex');
            } else {
                optionsList.classList.remove('flex');

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
                optionsList.classList.remove('flex');
                optionsList.classList.add('hidden');
            }, 200);
        });

        // Mostrar todas las opciones al enfocar el input (opcional)
        typeableInput.addEventListener('focus', () => {
            filterOptions(typeableInput.value); // Mostrar las opciones filtradas o todas si el input está vacío
        });


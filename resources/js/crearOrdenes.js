
const guardarPersona = (evento) => {
    evento.preventDefault();
    const form = evento.target;
    const formData = new FormData(form);
    axios.post('/api/personas', formData, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json'
        }
    }).then(response => {
        const usuario = response.data.data;
        if(evento.target.perfil.value === 'Paciente'){
            document.getElementById('paciente_id').value = usuario.id;
        }else{
            document.getElementById('acompaniante_id').value = usuario.id;
        }
    }).catch(error => {
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

}

const buscarDocuento = (e) => {
    e.preventDefault();
    const baseUrl = '/api/personas/buscar/';
    const numero_documento = e.target.form['numero_documento'].value;
    if (numero_documento.length>3) {
    const fullUrl = baseUrl + numero_documento;
    axios.get(fullUrl)
        .then(response => {

            if (response.data && response.data.data) {
                const persona =response.data.data;
                console.log(persona);
                console.log(e.target);
                e.target.form['numero_documento'].value = persona.numero_documento;
                e.target.form['tipo_documento'].value = persona.tipo_documento;
                e.target.form['nombres'].value = persona.nombre;
                e.target.form['apellidos'].value = persona.apellido;
                if (persona.pais){
                    e.target.form['pais'].value = persona.pais || 'COL'; // Asignar país, por defecto 'COL'
                }
                if (e.target.form['perfil'].value === 'Paciente') {
                document.getElementById('paciente_id').value = persona.id;
                e.target.form['fecha_nacimiento'].value = persona.fecha_nacimiento;
                e.target.form['direccion'].value = persona.direccion;
                e.target.form['telefono'].value = persona.telefono;
                e.target.form['correo'].value = persona.correo;
                e.target.form['sexo'].forEach(sexo => {
                    if (sexo.value === persona.sexo) {
                        sexo.checked = true;
                    }
                });
                e.target.form['municipio'].value = persona.municipio;
                e.target.form['municipioBusqueda'].value = persona.ciudad || '';
                e.target.form['eps'].value = persona.eps || '';
            }else{
                document.getElementById('acompaniante_id').value = persona.id;
            }

            }
        })
        .catch(error => {
            console.error("Error fetching persona:", error);
        });
}
;
}


document.getElementById('crearPaciente').addEventListener('submit', function (e) {
    guardarPersona(e);
});
document.getElementById('crearacompaniante').addEventListener('submit', function (e) {
    guardarPersona(e);
});

document.getElementsByName('numero_documento').forEach(input => {

    input.addEventListener('blur', function (e) {

        buscarDocuento(e);
    });
});

document.getElementsByName('tipo_documento').forEach(input => {
    input.addEventListener('change', function (e)  {
    const tipo = input.value;
    if (tipo === 'CC'|| tipo === 'RC' || tipo === 'TI' || tipo === 'CE') {

        e.target.form['numero_documento'].setAttribute('maxlength', '10');
        e.target.form['numero_documento'].setAttribute('placeholder', 'Número de documento');
        pais.style.display = 'none';

    }  else if (tipo === 'PA' || tipo === 'PP' || tipo === 'PE' || tipo === 'PS' || tipo === 'PT' || tipo === 'AS'|| tipo ==="MS") {
        e.target.form['numero_documento'].setAttribute('maxlength', '16');
        e.target.form['numero_documento'].setAttribute('placeholder', 'Identificación temporal');
        e.target.form['numero_documento'].setAttribute('type', 'text');
        e.target.form['pais'].style.display = 'flex';
        e.target.form['pais'].addEventListener('focus', function (e) {
            axios.get('/api/paises')
                .then(response => {
                    if (response.data) {
                        response.data.data.paises.forEach(p => {
                            const option = document.createElement('option');
                            option.value = p.codigo_iso;
                            option.textContent = p.nombre;
                            option.className= ['text-gray-900', 'dark:text-gray-100'];
                            e.target.form['pais'].appendChild(option);
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




const VARIABLES = {
    PACIENTE: 'Paciente',
    ACOMPANIANTE: 'Acompañante',
    NUEVO_USUARIO: 'nuevoUsuario',
    ACTUALIZAR_USUARIO: 'actualizarUsuario',
    PACIENTE_ID: 'paciente_id',
    ACOMPANIANTE_ID: 'acompaniante_id',
    TIPO_DOCUMENTO: 'tipo_documento',
};
const dataKeys = {
    tiposDocumento: 'tipos_documento_data',
    paises: 'paises_data',
    municipios: 'municipios_data',
    eps: 'eps_data',
    lastUpdate: 'frontend_data_last_update'
};



const documentos = JSON.parse(localStorage.getItem(dataKeys.tiposDocumento)) || [];
const paises = JSON.parse(localStorage.getItem(dataKeys.paises)) || [];
const municipios = JSON.parse(localStorage.getItem(dataKeys.municipios)) || [];
const eps = JSON.parse(localStorage.getItem(dataKeys.eps)) || [];
const crearPaciente = document.getElementById('crearPaciente');
const crearAcompaniante = document.getElementById('crearacompaniante');
const paciente = document.getElementById('paciente_id');
const acompaniante = document.getElementById('acompaniante_id');
const exmamenes = await axios.get('/api/examenes', {
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
    }
}).then(response => {
    return response.data.data;
}).catch(error => {
    console.error('Error al obtener los exámenes:', error);
    return [];
});
const mostrarExamenes = (listaExamenes) => {
    const examenContainer = document.getElementById('examenesContainer');
    examenContainer.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos exámenes
    listaExamenes.forEach(examen => {
    const examenItem = document.createElement('div');
    examenItem.className = 'examen-item flex items-center gap-2 p-2 border border-borders rounded-sm shadow-sm';
    const input = document.createElement('input');
    input.type = 'number';
    input.id = examen.id;
    input.name = `examenes[${examen.id}]`;
    input.className = 'w-16 px-2 py-1 text-center rounded border border-borders bg-white focus:outline-none focus:ring-2 focus:ring-primary appearance-none';
    input.min = '0';
    input.value = '0';
    input.style.cssText = 'appearance: textfield; -moz-appearance: textfield;';
    const label = document.createElement('label');
    label.setAttribute('for', examen.id);
    label.className = 'text-lg font-semibold';
    label.textContent = examen.nombre;
    examenItem.appendChild(input);
    examenItem.appendChild(label);

    if (examenContainer) {
        examenContainer.appendChild(examenItem);
    } else {boddy.appendChild(examenItem);}
});
}


const todosLosExamenes = exmamenes.examenes || [];
let examesVisibles = todosLosExamenes;
const soloDiezYSeisMil = document.getElementById('16000');

mostrarExamenes(examesVisibles);
soloDiezYSeisMil.addEventListener('change', (e) => {
    // Filtrar los exámenes para mostrar solo aquellos con valor "16000.00"
    if (e.target.checked) {
        examesVisibles = todosLosExamenes.filter(examen => examen.valor === 16000);
    } else {
        examesVisibles = todosLosExamenes;
    }
   
    mostrarExamenes(examesVisibles);
});





const guardarPersona = (evento) => {

    evento.preventDefault();

    const form = evento.target;

    const formData = new FormData(form);
    const isPaciente = formData.get('perfil')=== VARIABLES.PACIENTE;

    let url = '/api/personas';

    if(form['tipoGuardado'].value === VARIABLES.ACTUALIZAR_USUARIO){
        url = isPaciente ? `/api/personas/${paciente.value}` : `/api/personas/${acompaniante.value}`;

        formData.append('_method', 'PUT');
    }


    axios.post(url, formData, {



        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json'
        }
    }).then(response => {

        const usuario = response.data.data;

        if(isPaciente){
            paciente.value = usuario.id;
        }else{
            acompaniante.value = usuario.id;
        }
        // Deshabilitar todos los campos del formulario después de guardar

        Array.from(form.elements).forEach(element => {
            if (element.type !== 'hidden') {
                element.setAttribute('disabled', 'disabled');
                element.classList.add('bg-stone-300', 'dark:bg-gray-700', 'cursor-not-allowed');
            }
        });
        form.classList.add('bg-green-100', 'pointer-events-none', 'transition', 'duration-50', 'ease-in-out');
        form.querySelector('button[type="submit"]').classList.add('bg-green-600', 'text-white', 'cursor-not-allowed');
        form.querySelector('button[type="submit"]').textContent = 'Guardado exitoso';
        form.querySelector('button[type="submit"]').disabled = true;

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
            error.response ? console.error('Error en la solicitud:', error.response.data) :
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


                e.target.form['tipoGuardado'].value = VARIABLES.ACTUALIZAR_USUARIO;
                e.target.form['tipoGuardado'].textContent = 'Actualizar usuario';

                e.target.form['numero_documento'].value = persona.numero_documento;
                e.target.form['tipo_documento'].value = persona.tipo_documento;
                e.target.form['nombres'].value = persona.nombre;
                e.target.form['apellidos'].value = persona.apellido;
                if (persona.pais){
                    const pais_origen  =paises.find(p => p.codigo_iso === persona.pais);
                    const option = document.createElement('option');
                    option.value = pais_origen.codigo_iso;
                    option.textContent = pais_origen.nombre;
                    option.className= ['text-gray-900', 'dark:text-gray-100'];
                    e.target.form['pais'].appendChild(option);
                    e.target.form['pais'].value = persona.pais || 'COL'; // Asignar país, por defecto 'COL'
                }
                if (e.target.form['perfil'].value === VARIABLES.PACIENTE) {
                paciente.value = persona.id;
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
                acompaniante.value = persona.id;
            }

            }else{
                e.target.form['tipoGuardado'].value = VARIABLES.NUEVO_USUARIO;
                e.target.form['tipoGuardado'].textContent = 'Usuario nuevo';
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
    const pais = e.target.form['pais'];
    const numeroDocumento = e.target.form['numero_documento'];
    if (tipo === 'CC'|| tipo === 'RC' || tipo === 'TI' || tipo === 'CE') {

        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de documento');
        pais.value = 'COL'; // Asignar país por defecto
        pais.setAttribute('disabled', 'disabled');

    }  else if (tipo === 'PA' || tipo === 'PP' || tipo === 'PE' || tipo === 'PS' || tipo === 'PT' || tipo === 'AS'|| tipo ==="MS") {
        numeroDocumento.setAttribute('maxlength', '16');
        numeroDocumento.setAttribute('placeholder', 'Identificación temporal');
        numeroDocumento.setAttribute('type', 'text');

        pais.removeAttribute('disabled');
        pais.addEventListener('focus', function (e) {
        paises.forEach(p => {
                            const option = document.createElement('option');
                            option.value = p.codigo_iso;
                            option.textContent = p.nombre;
                            option.className= ['text-gray-900', 'dark:text-gray-100'];
                            pais.appendChild(option);
                        });
        }
        , { once: true });
    }
    }   );
        })

        // Autocompletado para los campos con name="municipioBusqueda" usando la lista de municipios

        document.getElementsByName('municipioBusqueda').forEach((input, idx) => {
            // Crear el select oculto para almacenar el valor real del municipio
            let hiddenSelect = input.form.querySelector('select[name="municipio"]');
            if (!hiddenSelect) {
                hiddenSelect = document.createElement('select');
                hiddenSelect.name = 'municipio';
                hiddenSelect.style.display = 'none';
                input.form.appendChild(hiddenSelect);
            }
            // Limpiar y agregar todas las opciones al select oculto
            hiddenSelect.innerHTML = '';
            municipios.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option.id;
                opt.textContent = option.municipio + ' - ' + option.departamento;
                hiddenSelect.appendChild(opt);
            });

            // Crear el contenedor para las opciones de autocompletado
            let optionsList = input.form.querySelector('.municipio-autocomplete-list');
            if (!optionsList) {
                optionsList = document.createElement('div');
                optionsList.className = 'municipio-autocomplete-list absolute z-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded w-full max-h-48 overflow-auto hidden';
                input.parentNode.appendChild(optionsList);
            }

            // Filtrar y mostrar opciones
            function filterOptions(query) {
                const filtered = municipios.filter(option =>
                    (option.municipio + ' - ' + option.departamento).toLowerCase().includes(query.toLowerCase())
                );
                displayOptions(filtered);
            }

            function displayOptions(options) {
                optionsList.innerHTML = '';
                if (options.length > 0) {
                    options.forEach(option => {
                        const div = document.createElement('div');
                        div.className = 'p-2 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 capitalize';
                        div.textContent = option.municipio + ' - ' + option.departamento;
                        div.addEventListener('mousedown', () => {
                            input.value = div.textContent;
                            hiddenSelect.value = option.id;
                            optionsList.classList.add('hidden');
                        });
                        optionsList.appendChild(div);
                    });
                    optionsList.classList.remove('hidden');
                } else {
                    optionsList.classList.add('hidden');
                }
            }

            input.setAttribute('autocomplete', 'off');
            input.addEventListener('input', () => {
                filterOptions(input.value);
            });
            input.addEventListener('focus', () => {
                filterOptions(input.value);
            });
            input.addEventListener('blur', () => {
                setTimeout(() => {
                    optionsList.classList.add('hidden');
                }, 150);
            });
        });

// Agregar opciones al datalist de los campos EPS en el formulario
document.getElementsByName('eps').forEach(input => {
    const datalistId = input.getAttribute('list');
    if (datalistId) {
        const datalist = document.getElementById(datalistId);
        if (datalist) {
            // Limpiar opciones previas para evitar duplicados
            datalist.innerHTML = '';
            eps.forEach(epsOption => {
                const option = document.createElement('option');
                option.value = epsOption.nombre;
                datalist.appendChild(option);
            });
        }
    }
});

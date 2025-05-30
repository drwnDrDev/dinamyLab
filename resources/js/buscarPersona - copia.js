const buscarPersona = document.getElementById('buscarPersona');
const tipoDocumento = document.getElementById('tipo_documento');
const numeroDocumento = document.getElementById('numero_documento');
const pais = document.getElementById('pais');
const tipoGuardado = document.getElementById('tipoGuardado');
const usuario = { id: null };

function asignarPaciente(paciente) {
    document.getElementById('paciente_id').value = paciente.id;
}

function setTipoGuardado() {
    tipoGuardado.textContent = usuario.id ? 'actualizar' : 'crear';
}

document.getElementById('crearPaciente').addEventListener('submit', async function (e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await axios.post('/api/personas', formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json'
            }
        });
        usuario.id = response.data.data.persona.id;
        asignarPaciente(usuario);
        setTipoGuardado();
    } catch (error) {
        if (error.response?.status === 422) {
            mostrarErrores(form, error.response.data.errors);
        } else {
            console.error('Error desconocido:', error);
        }
    }
});

function mostrarErrores(form, errors) {
    const errorMessages = Object.values(errors).flat();
    const errorList = document.createElement('ul');
    errorMessages.forEach(msg => {
        const li = document.createElement('li');
        li.textContent = msg;
        errorList.appendChild(li);
    });
    const errorContainer = document.createElement('div');
    errorContainer.className = 'text-sm text-red-600 space-y-1';
    errorContainer.appendChild(errorList);
    form.appendChild(errorContainer);
    setTimeout(() => errorContainer.remove(), 5000);
}

numeroDocumento.addEventListener('blur', async function () {
    const numero_documento = numeroDocumento.value;
    if (numero_documento.length <= 3) return;

    try {
        const { data } = await axios.get(`/api/personas/buscar/${numero_documento}`);
        if (data?.persona) {
            const persona = data.persona;
            usuario.id = persona.id;
            asignarPaciente(persona);
            setCamposPersona(persona);
        }
        setTipoGuardado();
    } catch (error) {
        console.error("Error fetching persona:", error);
        setTipoGuardado();
    }
});

function setCamposPersona(persona) {
    document.getElementById('nombres').value = persona.nombre;
    document.getElementById('apellidos').value = persona.apellido;
    document.getElementById('numero_documento').value = persona.numero_documento;
    document.getElementById('fecha_nacimiento').value = persona.fecha_nacimiento;
    document.getElementById('direccion').value = persona.direccion;
    document.getElementById('telefono').value = persona.telefono;
    document.getElementById('correo').value = persona.correo;
    document.getElementsByName('sexo').forEach(sexo => {
        sexo.checked = sexo.value === persona.sexo;
    });
    document.getElementById('tipo_documento').value = persona.tipo_documento;
    document.getElementById('municipio').value = persona.municipio;
    document.getElementById('municipioBusqueda').value = persona.ciudad || '';
    document.getElementById('pais').value = persona.pais || 'COL';
    document.getElementById('eps').value = persona.eps || '';
}

tipoDocumento.addEventListener('change', function () {
    const tipo = tipoDocumento.value;
    if (['CC', 'RC', 'TI', 'CE'].includes(tipo)) {
        numeroDocumento.maxLength = 10;
        numeroDocumento.placeholder = 'Número de documento';
        pais.style.display = 'none';
    } else if (['PA', 'PP', 'PE', 'PS', 'PT', 'AS', 'MS'].includes(tipo)) {
        numeroDocumento.maxLength = 16;
        numeroDocumento.placeholder = 'Identificación temporal';
        numeroDocumento.type = 'text';
        pais.style.display = 'flex';
        pais.addEventListener('focus', cargarPaises, { once: true });
    }
});

async function cargarPaises() {
    try {
        const { data } = await axios.get('/api/paises');
        if (data?.data?.paises) {
            data.data.paises.forEach(p => {
                const option = document.createElement('option');
                option.value = p.codigo_iso;
                option.textContent = p.nombre;
                option.className = 'text-gray-900 dark:text-gray-100';
                pais.appendChild(option);
            });
        } else {
            alert("No se encontró el país");
        }
    } catch (error) {
        console.error('Error fetching countries:', error);
    }
}

// Autocompletado de municipio
const typeableInput = document.getElementById('municipioBusqueda');
const optionsList = document.getElementById('opciones');
const hiddenSelect = document.getElementById('municipio');
const allOptions = Array.from(hiddenSelect.options).map(option => ({
    value: option.value,
    text: option.text
}));

function filterOptions(query) {
    const filtered = allOptions.filter(option =>
        option.text.toLowerCase().includes(query.toLowerCase())
    );
    displayOptions(filtered);
}

function displayOptions(options) {
    optionsList.innerHTML = '';
    if (options.length) {
        options.forEach(option => {
            const el = document.createElement('div');
            el.className = 'p-2 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 capitalize';
            el.textContent = option.text;
            el.onclick = () => {
                typeableInput.value = option.text;
                hiddenSelect.value = option.value;
                optionsList.classList.remove('block', 'flex');
                optionsList.classList.add('hidden');
            };
            optionsList.appendChild(el);
        });
        optionsList.classList.remove('hidden');
        optionsList.classList.add('flex');
    } else {
        optionsList.classList.remove('flex');
        optionsList.classList.add('hidden');
    }
}

typeableInput.addEventListener('input', () => filterOptions(typeableInput.value));
typeableInput.addEventListener('blur', () => setTimeout(() => {
    optionsList.classList.remove('flex');
    optionsList.classList.add('hidden');
}, 200));
typeableInput.addEventListener('focus', () => filterOptions(typeableInput.value));




// document.getElementById('perfil').addEventListener('change', function (e) {
//     if(e.target.value === 1) {
//         document.getElementById('fecha_nacimiento').setAttribute('required', 'required');
//         document.getElementById('sexo').setAttribute('required', 'required');
//         document.getElementById('municipio').setAttribute('required', 'required');
//     }else{
//         document.getElementById('fecha_nacimiento').removeAttribute('required');
//         document.getElementById('sexo').removeAttribute('required');
//         document.getElementById('municipio').removeAttribute('required');
//     }
// })


import axios from "axios";


axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.headers.common["X-CSRF-TOKEN"] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common["Accept"] = "application/json";

const examenes= document.getElementById('examenes');
const buscarPersona = document.getElementById('buscarPersona');

const persona = new Object();

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

const titulo = document.querySelector('h1');




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


buscarPersona.addEventListener('click', function (e) {
    e.preventDefault();
    const baseUrl = '/api/personas/buscar/';
    const numero_documento = document.getElementById('numero_documento').value;
    const fullUrl = baseUrl + numero_documento;

    axios.get(fullUrl)
        .then(response => {

            if (response.data) {
                setPersona(response.data.persona);
                console.log(persona);

            } else {
                alert("No se encontró la persona");
            }
        })
        .catch(error => {
            console.error("Error fetching persona:", error);
        });
}
);


axios.get("/api/examenes")
    .then(response => {
        response.data.data.examenes.map(examen => {
            const label = document.createElement('label');
            label.classList.add('form-check-label');
            label.setAttribute('for', examen.id);
            label.textContent = examen.nombre;
            examenes.appendChild(label);
            const option = document.createElement('input');
            option.classList.add('form-check-input');
            option.type = 'checkbox';
            option.name = 'examenes[]';
            option.id = examen.id;
            option.value = examen.id;
            option.textContent = examen.nombre;
            examenes.appendChild(option);
        }
        );
    })
    .catch(error => {
        console.error("Error fetching examenes:", error);
    });

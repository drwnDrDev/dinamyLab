import axios from "axios";

axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.headers.common["X-CSRF-TOKEN"] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common["Accept"] = "application/json";

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
        } else {
            console.error('Error desconocido:', error);
        }
    });
});



axios.get("/api/personas")
    .then(response => {
        console.log(response.data);
    })
    .catch(error => {
        console.error("Error fetching personas:", error);
    });



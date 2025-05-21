import axios from "axios";


const form = document.getElementById('form');
const buscarPersona = document.getElementById('buscar');


function showPacientes(pacientes) {
    const pacientesContainer = document.getElementById('pacientes');
    pacientesContainer.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos elementos

    pacientes.forEach(paciente => {
        const pacienteDiv = document.createElement('a');
        pacienteDiv.classList.add('grid', 'grid-cols-8' ,'gap-2' ,'hover:bg-slate-600' ,'hover:text-slate-50');
        pacienteDiv.textContent = `${paciente.nombre} ${paciente.apellido}`;
        pacientesContainer.appendChild(pacienteDiv);
    });
}
buscarPersona.addEventListener('click', function (e) {
    e.preventDefault();
    const baseUrl = '/api/personas/buscar/';
    const numero_documento = document.getElementById('llave_busqueda').value;
    const fullUrl = baseUrl + numero_documento;

    axios.get(fullUrl)
        .then(response => {
            if (response.data) {
                console.log(response.data.persona);
            } else {
                alert("No se encontró la persona");
            }
        })
        .catch(error => {
            console.error("Error fetching persona:", error);
        });
});

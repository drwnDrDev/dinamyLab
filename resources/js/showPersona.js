
const form = document.getElementById('direccion-form');
const personaId = form?.dataset.personaId;
const editarButton = document.getElementById('editar-button');
const editable = {
    label:editarButton.innerText,
    valor:false
}
const handleEditar = () => {
    editable.valor = !editable.valor;
    const label = editable.valor ? 'Guardar' : 'Editar';
    editarButton.innerText = label;
}

const iniciarEdicion = async () => {

 editarButton.addEventListener('click', handleEditar);

const direccionForm = document.getElementById('direccion-form');
const municipioInput = direccionForm?.querySelector('input[name="municipio"]');
const direccionInput = direccionForm?.querySelector('input[name="direccion"]');

const telefonos = document.getElementById('telefonos');

if(editable.valor) {
    telefonos.addEventListener('click', (event) => {
        const telefonoItem = event.target.closest('.telefono-item');
        if(telefonoItem) {
            const telefonoId = telefonoItem.dataset.telefonoId;
            // Aquí puedes agregar la lógica para manejar la edición del teléfono
        }
    });
}
}



document.addEventListener('DOMContentLoaded', iniciarEdicion);


import { fetchServiciosHabilitados, fetchViaIngreso, fetchDiagnosticos, fetchFinalidades, fetchCausasExternas, fetchTiposAtencion } from "./api";

const setupData = {
    serviciosHabilitados: [],
    viasIngreso: [],
    diagnosticos: [],
    finalidades: [],
    causasExternas: [],
    tiposAtencion: [],
    visibles: [],
    activos: [],
    todos: [],
    buscador: '',
    base: window.location.origin
};

const botones = {
    serviciosHabilitados: document.getElementById('btn-servicios-habilitados'),
    viasIngreso: document.getElementById('btn-vias-ingreso'),
    diagnosticos: document.getElementById('btn-diagnosticos-frecuentes'),
    finalidades: document.getElementById('btn-finalidades'),
    causasExternas: document.getElementById('btn-causas-externas'),
    tiposAtencion: document.getElementById('btn-tipos-atencion')
};

const contenido = document.getElementById('contenido');

const estadoBotones = {
    serviciosHabilitados: false,
    viasIngreso: false,
    diagnosticos: false,
    finalidades: false,
    causasExternas: false,
    tiposAtencion: false
};

const toggleButtonState = (key) => {

    estadoBotones[key] = !estadoBotones[key];
    if (estadoBotones[key]) {
        botones[key].classList.add('bg-gray-500');
        obtenerListado(key);
        renderIndice();
    } else {
        botones[key].classList.remove('bg-gray-500');
    }
};

const obtenerListado = (key) => {
    const data = setupData[key];
    setupData.activos = data.filter(item => item.activo);
    setupData.visibles = data.filter(item => item.activo);
    setupData.todos = data;
    if (setupData.buscador) {
        const buscado = setupData.buscador.toLowerCase();
        if(key === 'serviciosHabilitados'){
            setupData.visibles = data.filter(item => (item.nombre && item.nombre.toLowerCase().includes(buscado)) || (item.grupo && item.grupo.toLowerCase().includes(buscado)) || (item.codigo===parseInt(buscado)) );
            return;
        }
        setupData.visibles = data.filter(item => (item.nombre && item.nombre.toLowerCase().includes(buscado)) || (item.descripcion && item.descripcion.toLowerCase().includes(buscado)) || (item.codigo && item.codigo.toLowerCase().includes(buscado)));
    }
};

const toggleActivarItem = (item) => {
    const accion = item.activo ? 'desactivar' : 'activar';
    if (confirm(`¿Estás seguro de que deseas ${accion} el ítem "${item.nombre || item.descripcion}"?`)) {
        fetch(`${setupData.base}/api/setup/${item.codigo}/${accion}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            alert(data.message || `Ítem ${accion}do exitosamente.`);
            item.activo = !item.activo;
            obtenerListado(Object.keys(botones).find(key => botones[key].classList.contains('bg-gray-500')));
            renderIndice();
        })
        .catch(error => {
            console.error('Error al procesar la solicitud:', error);
            alert('Ocurrió un error al procesar la solicitud. Por favor, intenta nuevamente.');
        });
    }
};

const renderRegistro = (item) => {
    const div = document.createElement('div');
    div.classList.add('border', 'p-2', 'mb-2','bg-pink-50', 'dark:bg-gray-800', 'rounded', 'shadow',);

    div.addEventListener('click', (item) =>{
            cosole.log(item);

        });
    div.innerHTML = `
        <h3 class="font-bold col-span-4">${item.nombre || item.descripcion || 'Sin nombre'}</h3>
        <p> ${item.codigo || 'Sin código'}</p>`;
    const boton = document.createElement('button');
    boton.classList.add('mt-2', 'px-4', 'py-2', 'rounded', item.activo ? 'bg-red-500' : 'bg-green-500', 'text-white', 'font-bold');
    boton.textContent = item.activo ? 'Desactivar' : 'Activar';
    boton.addEventListener('click', (e) => {
        e.stopPropagation(); // Evitar que el clic se propague al div padre
        alert(item.nombre);
        toggleActivarItem(item);
    });
    div.appendChild(boton);
//

    return div;
}

const renderIndice = () => {
    const visibles = setupData.visibles;
    const activos = setupData.activos;
    const todos = setupData.todos;
    contenido.innerHTML = `
        <div class="mb-4">
            <button id="btn-activos" class="bg-cyan-500 text-white font-bold py-2 px-4 rounded mr-2">Activos (${activos.length})</button>
            <button id="btn-inactivos" class="bg-yellow-500 text-white font-bold py-2 px-4 rounded mr-2">Inactivos (${todos.length - activos.length})</button>
            <button id="btn-todos" class="bg-purple-500 text-white font-bold py-2 px-4 rounded mr-2">Todos (${todos.length})</button>
        </div>
        <div id="listado" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            ${visibles.map(item => renderRegistro(item).outerHTML).join('\n')}
        </div>
    `;

}


export const initializeSetupData = async () => {
    try {
        const [serviciosHabilitados, viasIngreso, diagnosticos, finalidades, causasExternas, tiposAtencion] = await Promise.all([
            fetchServiciosHabilitados(),
            fetchViaIngreso(),
            fetchDiagnosticos(),
            fetchFinalidades(),
            fetchCausasExternas(),
            fetchTiposAtencion()
        ]);

        setupData.serviciosHabilitados = serviciosHabilitados;
        setupData.viasIngreso = viasIngreso;
        setupData.diagnosticos = diagnosticos;
        setupData.finalidades = finalidades;
        setupData.causasExternas = causasExternas;
        setupData.tiposAtencion = tiposAtencion;

    } catch (error) {
        console.error('Error al inicializar los datos de configuración:', error);
    }
    Object.keys(botones).forEach(key => {
        botones[key].addEventListener('click', () => {
            toggleButtonState(key)
            document.getElementById('buscador').addEventListener('input',
                 (event) => {
                setupData.buscador = event.target.value;
                obtenerListado(key);
                renderIndice();
            });

        });
    });

};


document.addEventListener('DOMContentLoaded', () => {
    initializeSetupData();
});

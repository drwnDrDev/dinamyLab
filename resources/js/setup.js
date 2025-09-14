
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
        setupData.visibles = data.filter(item => (item.nombre && item.nombre.toLowerCase().includes(buscado)) || (item.descripcion && item.descripcion.toLowerCase().includes(buscado)) || (item.codigo && item.codigo.toLowerCase().includes(buscado)));
    }
};

const renderRegistro = (item) => {
    const div = document.createElement('div');
    div.classList.add('border', 'p-2', 'mb-2');
    div.innerHTML = `
        <h3 class="font-bold col-span-4">${item.nombre || item.descripcion || 'Sin nombre'}</h3>
        <p> ${item.codigo || 'Sin código'}</p>
        <p>${item.activo ? '<a class="text-green-500" href="#">activo</a>' : '<a class="text-red-500" href="#">inactivo</a>'}</p>
    `;
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
            ${visibles.map(item => renderRegistro(item).outerHTML).join('')}
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
            document.getElementById('buscador').addEventListener('input', (event) => {
                setupData.buscador = event.target.value;
                console.log('Buscador:', setupData.buscador);
                obtenerListado(key);
                renderIndice();
            });

        });
    });

};


document.addEventListener('DOMContentLoaded', () => {
    initializeSetupData();
});

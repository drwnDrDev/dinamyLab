import React, { useState, useEffect } from 'react';
import {
    fetchServiciosHabilitados,
    fetchViaIngreso,
    fetchDiagnosticos,
    fetchFinalidades,
    fetchCausasExternas,
    fetchTiposAtencion
} from "../../api";

const Setup = () => {
    // Estados
    const [setupData, setSetupData] = useState({
        visibles: [],
        todos: [],
        buscador: ''
    });

    const [estadoBotones, setEstadoBotones] = useState({
        serviciosHabilitados: {
            activo: false,
            nombre: 'Servicios Habilitados',
            clases: 'bg-blue-500',
            modificar: 'api/setup/servicios-habilitados'
        },
        viasIngreso: {
            activo:false,
            nombre: 'Vías de Ingreso',
            clases: 'bg-green-500'

        },
        diagnosticos: {
            activo:false,
            nombre: 'Diagnósticos',
            clases: 'bg-red-500'

        },
        finalidades: {
            activo:false,
            nombre: 'Finalidades',
            clases: 'bg-yellow-500'
        },
        causasExternas: {
            activo:false,
            nombre: 'Causas Externas',
            clases: 'bg-purple-500'

        },
        tiposAtencion: {
            activo: false,
            nombre: 'Tipos de Atención',
            clases: 'bg-yellow-500'

        }
    });

    // Efecto para cargar datos iniciales
    useEffect(() => {
        initializeSetupData();
    }, []);

    const initializeSetupData = async () => {
        try {
            const [
                serviciosHabilitados,
                viasIngreso,
                diagnosticos,
                finalidades,
                causasExternas,
                tiposAtencion
            ] = await Promise.all([
                fetchServiciosHabilitados(),
                fetchViaIngreso(),
                fetchDiagnosticos(),
                fetchFinalidades(),
                fetchCausasExternas(),
                fetchTiposAtencion()
            ]);

            setSetupData(prev => ({
                ...prev,
                serviciosHabilitados,
                viasIngreso,
                diagnosticos,
                finalidades,
                causasExternas,
                tiposAtencion
            }));
        } catch (error) {
            console.error('Error al inicializar los datos:', error);
        }
    };

    const toggleButtonState = (key) => {
        // Reset all buttons to inactive first
        const resetButtons = {};
        Object.keys(estadoBotones).forEach(k => {
            resetButtons[k] = {
                ...estadoBotones[k],
                activo: k === key ? !estadoBotones[k].activo : false
            };
        });

        setEstadoBotones(resetButtons);

        if (!estadoBotones[key].activo) {
            obtenerListado(key);
        }
    };

    const obtenerListado = (key) => {
        const data = setupData[key];
        setSetupData(prev => ({
            ...prev,
            activos: data.filter(item => item.activo),
            visibles: data.filter(item => item.activo),
            todos: data
        }));

        if (setupData.buscador) {
            const buscado = setupData.buscador.toLowerCase();
            const filtrados = key === 'serviciosHabilitados'
                ? data.filter(item =>
                    (item.nombre && item.nombre.toLowerCase().includes(buscado)) ||
                    (item.grupo && item.grupo.toLowerCase().includes(buscado)) ||
                    (item.codigo === parseInt(buscado)))
                : data.filter(item =>
                    (item.nombre && item.nombre.toLowerCase().includes(buscado)) ||
                    (item.descripcion && item.descripcion.toLowerCase().includes(buscado)) ||
                    (item.codigo && item.codigo.toLowerCase().includes(buscado)));

            setSetupData(prev => ({
                ...prev,
                visibles: filtrados
            }));
        }
    };

    const toggleActivarItem = async (item) => {
        const accion = item.activo ? 'desactivar' : 'activar';

        if (window.confirm(`¿Estás seguro de que deseas ${accion} el ítem "${item.nombre || item.descripcion}"?`)) {
            try {
                const response = await fetch(`${window.location.origin}/api/setup/${item.codigo}/${accion}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Error en la respuesta del servidor');

                const data = await response.json();
                alert(data.message || `Ítem ${accion}do exitosamente.`);

                // Actualizar el estado del ítem
                const activeKey = Object.keys(estadoBotones).find(key => estadoBotones[key]);
                if (activeKey) {
                    obtenerListado(activeKey);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar la solicitud.');
            }
        }
    };

    return (
        <div className="p-4">
            {/* Botones de categorías */}
            <div className="flex flex-wrap gap-2 mb-4">
                {Object.keys(estadoBotones).map(key => (
                    <button
                        key={key}
                        onClick={() => toggleButtonState(key)}
                        className={`px-4 py-2 rounded ${
                            estadoBotones[key].activo ? estadoBotones[key].clases: 'bg-gray-200'
                        } text-white`}
                    >
                        {estadoBotones[key].nombre}
                    </button>
                ))}
            </div>

            {/* Buscador */}
            <input
                type="text"
                placeholder="Buscar..."
                className="w-full p-2 mb-4 border rounded"
                value={setupData.buscador}
                onChange={(e) => {
                    setSetupData(prev => ({ ...prev, buscador: e.target.value }));
                    const activeKey = Object.keys(estadoBotones).find(key => estadoBotones[key]);
                    if (activeKey) obtenerListado(activeKey);
                }}
            />

            {/* Estadísticas */}
            <div className="mb-4">
                <button className="bg-cyan-500 text-white font-bold py-2 px-4 rounded mr-2">
                    Activos ({setupData.activos.length})
                </button>
                <button className="bg-yellow-500 text-white font-bold py-2 px-4 rounded mr-2">
                    Inactivos ({setupData.todos.length - setupData.activos.length})
                </button>
                <button className="bg-purple-500 text-white font-bold py-2 px-4 rounded mr-2">
                    Todos ({setupData.todos.length})
                </button>
            </div>

            {/* Listado de items */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {setupData.visibles.map((item, index) => (
                    <div
                        key={index}
                        className="border p-2 mb-2 bg-pink-50 dark:bg-gray-800 rounded shadow"
                        onClick={() => console.log(item)}
                    >
                        <h3 className="font-bold col-span-4">
                            {item.nombre || item.descripcion || 'Sin nombre'}
                        </h3>
                        <p>{item.codigo || 'Sin código'}</p>
                        <button
                            className={`mt-2 px-4 py-2 rounded ${
                                item.activo ? 'bg-red-500' : 'bg-green-500'
                            } text-white font-bold`}
                            onClick={(e) => {
                                e.stopPropagation();
                                toggleActivarItem(item);
                            }}
                        >
                            {item.activo ? 'Desactivar' : 'Activar'}
                        </button>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default Setup;

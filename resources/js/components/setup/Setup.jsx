import React, { useState, useEffect } from 'react';
import {
    fetchServiciosHabilitados,
    fetchViaIngreso,
    fetchDiagnosticos,
    fetchFinalidades,
    fetchCausasExternas,
    fetchTiposAtencion,
    fetchTiposAfiliacion
} from "../../api";

const Setup = () => {
    // Estado principal
    const [categorias, setCategorias] = useState({
        serviciosHabilitados: { nombre: 'Servicios Habilitados', clases: 'bg-blue-200 text-blue-900', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/servicios-habilitados', localStorageKey: 'servicios_habilitados_data' },
        viasIngreso: { nombre: 'Vías de Ingreso', clases: 'bg-green-200 text-green-900', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/via-ingreso', localStorageKey: 'vias_ingreso_data' },
        diagnosticos: { nombre: 'Diagnósticos', clases: 'bg-stone-200 text-stone-900', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/cie10', localStorageKey: 'diagnosticos_data' },
        finalidades: { nombre: 'Finalidades', clases: 'bg-yellow-200 text-yellow-900', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/finalidades', localStorageKey: 'finalidad_consulta_data' },
        causasExternas: { nombre: 'Causas Externas', clases: 'bg-purple-200 text-purple-900', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/causa-atencion', localStorageKey: 'causas_externas_data' },
        tiposAtencion: { nombre: 'Modalidades de Atención', clases: 'bg-teal-200 text-teal-900', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/modalidades-atencion', localStorageKey: 'tipos_atencion_data' },
        tiposAfiliacion: { nombre: 'Tipos de Afiliación', clases: 'bg-pink-200 text-pink-900', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/tipos-afiliacion', localStorageKey: 'tipos_afiliacion_data' },
    });

    const [buscador, setBuscador] = useState('');
    const [filtro, setFiltro] = useState('todos'); // 'activos', 'inactivos', 'todos'

    // Map de funciones para inicializar
    const fetchers = {
        serviciosHabilitados: fetchServiciosHabilitados,
        viasIngreso: fetchViaIngreso,
        diagnosticos: fetchDiagnosticos,
        finalidades: fetchFinalidades,
        causasExternas: fetchCausasExternas,
        tiposAtencion: fetchTiposAtencion,
        tiposAfiliacion: fetchTiposAfiliacion
    };

    // useEffect para la carga inicial de datos (solo una vez)
    useEffect(() => {
        const initialize = async () => {
            try {
                const fetcherFns = Object.values(fetchers);
                const keys = Object.keys(fetchers);
                const results = await Promise.allSettled(fetcherFns.map(fn => fn()));

                setCategorias(prev => {
                    const updated = { ...prev };
                    keys.forEach((key, i) => {
                        if (results[i].status === 'fulfilled') {
                            const fetchedData = results[i].value;
                            updated[key].datos = fetchedData;
                            updated[key].todos = fetchedData;
                            updated[key].activos = fetchedData.filter(d => d.activo);
                            updated[key].visibles = fetchedData;
                        } else {
                            updated[key].datos = [];
                            updated[key].todos = [];
                            updated[key].activos = [];
                            updated[key].visibles = [];
                            console.error(`Error al cargar ${updated[key].nombre}`);
                        }
                    });
                    return updated;
                });
            } catch (error) {
                console.error('Error al inicializar los datos:', error);
            }
        }
        initialize();
    }, []);
    // Nuevo useEffect para filtrar cuando cambien filtro, buscador o la categoría activa
    useEffect(() => {
        const activeKey = Object.keys(categorias).find(key => categorias[key].activo);
        if (activeKey) {
            obtenerListado(activeKey);
        }

    }, [filtro, buscador]); // Usar activeKey como dependencia

    // Filtrado genérico
    const filtrar = (item, texto, esServicio) => {
        const buscado = texto.toLowerCase();
        return (
            (item.nombre && item.nombre.toLowerCase().includes(buscado)) ||
            (item.descripcion && item.descripcion.toLowerCase().includes(buscado)) ||
            (esServicio
                ? (item.grupo && item.grupo.toLowerCase().includes(buscado)) || item.codigo == parseInt(buscado)
                : item.codigo && item.codigo.toLowerCase().includes(buscado))
        );
    };

    // Refactorización de obtenerListado para que sea una función pura
    const obtenerListado = (key) => {
        setCategorias(prev => {
            const data = prev[key].datos;
            let visibles;

            if (filtro === 'activos') {
                visibles = data.filter(d => d.activo);
            } else if (filtro === 'inactivos') {
                visibles = data.filter(d => !d.activo);
            } else {
                visibles = data;
            }

            if (buscador) {
                visibles = visibles.filter(item => filtrar(item, buscador, key === 'serviciosHabilitados'));
            }

            return {
                ...prev,
                [key]: {
                    ...prev[key],
                    visibles,
                },
            };
        });
    };

    // Toggle botón
    const toggleButtonState = (key) => {
        setCategorias(prev => {
            const updated = {};
            Object.keys(prev).forEach(k => {
                updated[k] = { ...prev[k], activo: k === key ? !prev[k].activo : false };
            });
            return updated;
        });
    };

    // Activar/Desactivar ítem
    const toggleActivarItem = async (item, key) => {
        const categoria = categorias[key];
        const url = `${window.location.origin}/${categoria.activeChange}/${item.codigo}/activar`;

        try {
            const response = await fetch(url, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' }
            });

            if (!response.ok) throw new Error('Error en la respuesta del servidor');


            // actualizar localmente sin esperar refresh
            setCategorias(prev => {
                const updated = { ...prev };
                updated[key] = {
                    ...prev[key],
                    datos: prev[key].datos.map(d =>
                        d.codigo === item.codigo ? { ...d, activo: !d.activo } : d
                    ),
                };
                // recalcular todos y activos
                const nuevosDatos = updated[key].datos;
                updated[key].todos = nuevosDatos;
                updated[key].activos = nuevosDatos.filter(d => d.activo);
                localStorage.setItem(updated[key].localStorageKey, JSON.stringify(updated[key].activos));
                obtenerListado(key); // actualizar visibles
                return updated;
            });
        } catch (error) {
            console.error('Error:', error);
        }
    };


    // Identificar la categoría activa
    const activeKey = Object.keys(categorias).find(key => categorias[key].activo);
    const activa = activeKey ? categorias[activeKey] : null;

    return (
        <div className="p-4">

            {/* Botones de categorías */}
            <div className="flex flex-wrap gap-2 mb-4">
                {Object.keys(categorias).map(key => (
                    <button
                        key={key}
                        onClick={() => toggleButtonState(key)}
                        className={`px-4 py-2 rounded ${categorias[key].activo ? categorias[key].clases : 'bg-gray-200 text-white'}`}
                    >
                        {categorias[key].nombre}
                    </button>
                ))}
            </div>

            {/* Buscador */}
            <input
                type="text"
                placeholder="Buscar..."
                className="w-full p-2 mb-4 border rounded"
                value={buscador}
                onChange={(e) => setBuscador(e.target.value)}
            />

            {/* Estadísticas */}
            {activa && (
                <div className="mb-4">
                    <button
                        className="bg-cyan-500 text-white font-bold py-2 px-4 rounded mr-2"
                        onClick={() => setFiltro('activos')}
                    >
                        Activos ({activa.activos.length})
                    </button>
                    <button
                        className="bg-yellow-500 text-white font-bold py-2 px-4 rounded mr-2"
                        onClick={() => setFiltro('inactivos')}
                    >
                        Inactivos ({activa.todos.length - activa.activos.length})
                    </button>
                    <button
                        className="bg-purple-500 text-white font-bold py-2 px-4 rounded mr-2"
                        onClick={() => setFiltro('todos')}
                    >
                        Todos ({activa.todos.length})
                    </button>
                </div>
            )}

            {/* Listado */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {activa?.visibles.map((item) => (
                    <div
                        key={item.codigo}
                        className="border p-2 mb-2 bg-pink-50 dark:bg-gray-800 rounded shadow"

                    >
                        <h3 className="font-bold col-span-4">{item.nombre || 'Sin nombre'}</h3>
                        <p>{item.codigo || 'Sin código'}</p>
                        <button
                            className={`mt-2 px-4 py-2 rounded ${item.activo ? 'bg-red-500' : 'bg-green-500'} text-white font-bold`}
                            onClick={(e) => {
                                e.stopPropagation();
                                if (activeKey) {
                                    toggleActivarItem(item, activeKey);
                                }
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

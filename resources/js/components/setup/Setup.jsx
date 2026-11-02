import React, { useEffect, useState } from 'react';
import useAxiosAuth from '../hooks/useAxiosAuth';

const initialCategorias = {
    serviciosHabilitados: { nombre: 'Servicios Habilitados', clases: 'bg-blue-200 text-blue-900', datos: [], visibles: [], activo: false, loaded: false, activeChange: 'api/servicios-habilitados', localStorageKey: 'servicios_habilitados_data', endpoint: '/api/servicios-habilitados', dataKey: 'data' },
    viasIngreso: { nombre: 'Vías de Ingreso', clases: 'bg-green-200 text-green-900', datos: [], visibles: [], activo: false, loaded: false, activeChange: 'api/via-ingreso', localStorageKey: 'vias_ingreso_data', endpoint: '/api/via-ingreso', dataKey: 'data' },
    diagnosticos: { nombre: 'Diagnósticos', clases: 'bg-stone-200 text-stone-900', datos: [], visibles: [], activo: false, loaded: false, activeChange: 'api/cie10', localStorageKey: 'default_cies_data', endpoint: '/api/cie10/gestion', dataKey: 'data' },
    finalidades: { nombre: 'Finalidades', clases: 'bg-yellow-200 text-yellow-900', datos: [], visibles: [], activo: false, loaded: false, activeChange: 'api/finalidades', localStorageKey: 'finalidad_consulta_data', endpoint: '/api/finalidades', dataKey: 'data.finalidades' },
    causasExternas: { nombre: 'Causas Externas', clases: 'bg-purple-200 text-purple-900', datos: [], visibles: [], activo: false, loaded: false, activeChange: 'api/causa-atencion', localStorageKey: 'causas_externas_data', endpoint: '/api/causa-atencion', dataKey: 'data.causas_externas' },
    tiposAtencion: { nombre: 'Modalidades de Atención', clases: 'bg-teal-200 text-teal-900', datos: [], visibles: [], activo: false, loaded: false, activeChange: 'api/modalidades-atencion', localStorageKey: 'tipos_atencion_data', endpoint: '/api/modalidades-atencion', dataKey: 'data.modalidades_atencion' },
    tiposAfiliacion: { nombre: 'Tipos de Afiliación', clases: 'bg-pink-200 text-pink-900', datos: [], visibles: [], activo: false, loaded: false, activeChange: 'api/tipos-afiliacion', localStorageKey: 'tipos_afiliacion_data', endpoint: '/api/tipos-afiliacion', dataKey: 'data' },
};

const getNestedData = (obj, path) => {
    const data = path.split('.').reduce((value, part) => value?.[part], obj);
    return Array.isArray(data) ? data : [];
};

const Setup = () => {
    const { axiosInstance, csrfLoaded, error: csrfError } = useAxiosAuth();
    const [categorias, setCategorias] = useState(initialCategorias);
    const [buscador, setBuscador] = useState('');
    const [filtro, setFiltro] = useState('todos');
    const [pagina, setPagina] = useState(1);
    const [paginacion, setPaginacion] = useState(null);
    const [cargando, setCargando] = useState(false);
    const [error, setError] = useState('');
    const [refresh, setRefresh] = useState(0);

    const activeKey = Object.keys(categorias).find(key => categorias[key].activo);
    const activa = activeKey ? categorias[activeKey] : null;
    const esDiagnostico = activeKey === 'diagnosticos';

    useEffect(() => {
        if (!csrfLoaded || !activeKey || !activa) return undefined;

        let cancelled = false;
        const filtrarDatos = (datos) => datos.filter(item => {
            const activo = Boolean(item.activo);
            const texto = [
                item.nombre,
                item.descripcion,
                item.grupo,
                item.codigo,
            ].filter(Boolean).join(' ').toLocaleLowerCase();
            return (filtro === 'todos' || (filtro === 'activos' ? activo : !activo))
                && texto.includes(buscador.trim().toLocaleLowerCase());
        });

        if (activeKey !== 'diagnosticos' && activa.loaded) {
            setCargando(false);
            setCategorias(prev => ({
                ...prev,
                [activeKey]: { ...prev[activeKey], visibles: filtrarDatos(prev[activeKey].datos) },
            }));
            return undefined;
        }

        const cargar = async () => {
            setCargando(true);
            setError('');
            try {
                if (activeKey === 'diagnosticos') {
                    const response = await axiosInstance.get(activa.endpoint, {
                        params: { search: buscador, estado: filtro, page: pagina },
                    });
                    if (cancelled) return;
                    const rows = getNestedData(response.data, activa.dataKey);
                    setCategorias(prev => ({
                        ...prev,
                        [activeKey]: { ...prev[activeKey], datos: rows, visibles: rows, loaded: true },
                    }));
                    setPaginacion(response.data.meta);
                } else {
                    const response = await axiosInstance.get(activa.endpoint);
                    if (cancelled) return;
                    const rows = getNestedData(response.data, activa.dataKey);
                    const visibles = filtrarDatos(rows);
                    setCategorias(prev => ({
                        ...prev,
                        [activeKey]: { ...prev[activeKey], datos: rows, visibles, loaded: true },
                    }));
                    setPaginacion(null);
                }
            } catch (requestError) {
                if (!cancelled) {
                    setError(requestError.response?.data?.message || `No fue posible cargar ${activa.nombre}.`);
                    console.error(`Error al cargar ${activa.nombre}:`, requestError);
                }
            } finally {
                if (!cancelled) setCargando(false);
            }
        };

        const timer = setTimeout(cargar, activeKey === 'diagnosticos' ? 250 : 0);
        return () => {
            cancelled = true;
            clearTimeout(timer);
        };
    }, [activeKey, csrfLoaded, buscador, filtro, pagina, refresh]);

    const seleccionarCategoria = (key) => {
        setCategorias(prev => {
            const seleccion = !prev[key].activo;
            return Object.fromEntries(Object.entries(prev).map(([nombre, categoria]) => [
                nombre,
                { ...categoria, activo: seleccion && nombre === key },
            ]));
        });
        setBuscador('');
        setFiltro('todos');
        setPagina(1);
        setPaginacion(null);
        setError('');
    };

    const sincronizarLocalStorage = (categoria, actualizado) => {
        try {
            const guardados = JSON.parse(localStorage.getItem(categoria.localStorageKey) || '[]');
            const siguientes = guardados.filter(item => item.codigo !== actualizado.codigo);
            if (actualizado.activo) siguientes.push(actualizado);
            localStorage.setItem(categoria.localStorageKey, JSON.stringify(siguientes));
        } catch (storageError) {
            console.error(`No fue posible actualizar ${categoria.localStorageKey}:`, storageError);
        }
    };

    const toggleActivarItem = async (item) => {
        if (!activeKey || !activa) return;
        setError('');
        try {
            const response = await axiosInstance.patch(
                `${window.location.origin}/${activa.activeChange}/${encodeURIComponent(item.codigo)}/activar`
            );
            const actualizado = response.data?.data;
            if (!actualizado || actualizado.codigo === undefined) {
                throw new Error('El servidor no devolvió el estado actualizado del catálogo.');
            }

            sincronizarLocalStorage(activa, actualizado);
            setCategorias(prev => ({
                ...prev,
                [activeKey]: {
                    ...prev[activeKey],
                    datos: prev[activeKey].datos.map(dato =>
                        dato.codigo === actualizado.codigo ? actualizado : dato
                    ),
                    visibles: prev[activeKey].visibles.map(dato =>
                        dato.codigo === actualizado.codigo ? actualizado : dato
                    ),
                },
            }));
            if (activeKey === 'diagnosticos') setRefresh(value => value + 1);
        } catch (requestError) {
            setError(requestError.response?.data?.message || requestError.message || 'No fue posible actualizar el estado.');
            console.error('Error al actualizar el estado del catálogo:', requestError);
        }
    };

    const itemsActivos = esDiagnostico
        ? (paginacion?.activos ?? 0)
        : (activa?.datos.filter(item => item.activo).length ?? 0);
    const totalItems = esDiagnostico ? (paginacion?.todos ?? 0) : (activa?.datos.length ?? 0);
    const itemsInactivos = esDiagnostico
        ? (paginacion?.inactivos ?? 0)
        : totalItems - itemsActivos;

    return (
        <div className="p-4 text-text">
            <div className="mb-4 flex flex-wrap gap-2">
                {Object.entries(categorias).map(([key, categoria]) => (
                    <button
                        key={key}
                        type="button"
                        onClick={() => seleccionarCategoria(key)}
                        aria-pressed={categoria.activo}
                        className={`rounded px-4 py-2 font-medium ${categoria.activo ? categoria.clases : 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-100'}`}
                    >
                        {categoria.nombre}
                    </button>
                ))}
            </div>

            {activa && (
                <>
                    <label className="mb-4 block">
                        <span className="sr-only">Buscar en {activa.nombre}</span>
                        <input
                            type="search"
                            placeholder={`Buscar en ${activa.nombre}...`}
                            className="w-full rounded border border-gray-300 bg-white p-2 text-gray-900 placeholder:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder:text-gray-300"
                            value={buscador}
                            onChange={event => {
                                setBuscador(event.target.value);
                                setPagina(1);
                            }}
                        />
                    </label>

                    <div className="mb-4 flex flex-wrap gap-2">
                        <button type="button" className="rounded bg-cyan-600 px-4 py-2 font-bold text-white" onClick={() => {
                            setFiltro('activos');
                            setPagina(1);
                        }}>
                            Activos ({itemsActivos})
                        </button>
                        <button type="button" className="rounded bg-yellow-600 px-4 py-2 font-bold text-white" onClick={() => {
                            setFiltro('inactivos');
                            setPagina(1);
                        }}>
                            Inactivos ({itemsInactivos})
                        </button>
                        <button type="button" className="rounded bg-purple-700 px-4 py-2 font-bold text-white" onClick={() => {
                            setFiltro('todos');
                            setPagina(1);
                        }}>
                            Todos ({totalItems})
                        </button>
                    </div>

                    {csrfError && <p role="alert" className="mb-3 text-red-700 dark:text-red-300">{csrfError}</p>}
                    {error && <p role="alert" className="mb-3 text-red-700 dark:text-red-300">{error}</p>}
                    {cargando && <p role="status" className="mb-3">Cargando catálogo...</p>}

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {activa.visibles.map(item => (
                            <div key={item.codigo} className="rounded border border-gray-300 bg-white p-3 shadow dark:border-gray-600 dark:bg-gray-800">
                                <h3 className="font-bold">{item.nombre || item.descripcion || 'Sin nombre'}</h3>
                                {item.codigo && <p>{item.codigo}</p>}
                                <button
                                    type="button"
                                    className={`mt-2 rounded px-4 py-2 font-bold text-white ${item.activo ? 'bg-red-700' : 'bg-green-700'}`}
                                    onClick={() => toggleActivarItem(item)}
                                >
                                    {item.activo ? 'Desactivar' : 'Activar'}
                                </button>
                            </div>
                        ))}
                    </div>

                    {!cargando && activa.visibles.length === 0 && !error && (
                        <p className="py-4 text-center">No hay elementos para mostrar.</p>
                    )}

                    {esDiagnostico && paginacion && paginacion.last_page > 1 && (
                        <nav aria-label="Paginación de diagnósticos" className="mt-4 flex items-center justify-center gap-3">
                            <button
                                type="button"
                                disabled={pagina <= 1 || cargando}
                                onClick={() => setPagina(value => value - 1)}
                                className="rounded border border-gray-400 px-3 py-2 disabled:opacity-50"
                            >
                                Anterior
                            </button>
                            <span>Página {paginacion.current_page} de {paginacion.last_page}</span>
                            <button
                                type="button"
                                disabled={pagina >= paginacion.last_page || cargando}
                                onClick={() => setPagina(value => value + 1)}
                                className="rounded border border-gray-400 px-3 py-2 disabled:opacity-50"
                            >
                                Siguiente
                            </button>
                        </nav>
                    )}
                </>
            )}
        </div>
    );
};

export default Setup;

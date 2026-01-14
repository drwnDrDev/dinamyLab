import React, { useState, useEffect } from 'react';
import axios from 'axios';

/**
 * Componente para personal de recepción
 * Lista pre-registros pendientes y permite confirmarlos o cancelarlos
 * REQUIERE autenticación
 */
const RecepcionPreRegistros = ({ onSeleccionarParaConfirmar }) => {
    const [loading, setLoading] = useState(false);
    const [registros, setRegistros] = useState([]);
    const [filtros, setFiltros] = useState({
        busqueda: '',
        fecha: '',
        estado: 'pendiente'
    });
    const [error, setError] = useState(null);

    useEffect(() => {
        cargarRegistros();
    }, [filtros.estado]);

    const cargarRegistros = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await axios.get('/api/recepcion/pre-registros/pendientes', {
                params: {
                    estado: filtros.estado,
                    fecha: filtros.fecha || undefined
                }
            });

            setRegistros(response.data.data || []);
        } catch (err) {
            console.error('Error al cargar:', err);
            setError(err.response?.data?.message || 'Error al cargar registros');
        } finally {
            setLoading(false);
        }
    };

    const handleBuscar = async () => {
        if (!filtros.busqueda.trim()) {
            cargarRegistros();
            return;
        }

        setLoading(true);
        setError(null);

        try {
            const response = await axios.get('/api/recepcion/pre-registros/buscar', {
                params: { q: filtros.busqueda }
            });
            setRegistros(response.data.data || []);
        } catch (err) {
            setError('Error en búsqueda');
        } finally {
            setLoading(false);
        }
    };

    const handleCancelar = async (id) => {
        if (!confirm('¿Cancelar este pre-registro?')) return;

        try {
            await axios.put(`/api/recepcion/pre-registros/${id}/cancelar`);
            cargarRegistros();
        } catch (err) {
            alert('Error al cancelar');
        }
    };

    const handleConfirmar = (registro) => {
        if (onSeleccionarParaConfirmar) {
            onSeleccionarParaConfirmar(registro);
        }
    };

    const registrosFiltrados = registros.filter(r => {
        if (!filtros.busqueda) return true;

        const busqueda = filtros.busqueda.toLowerCase();
        return (
            r.nombres_completos?.toLowerCase().includes(busqueda) ||
            r.numero_documento?.includes(busqueda) ||
            r.codigo_confirmacion?.toLowerCase().includes(busqueda)
        );
    });

    const getEstadoBadge = (estado) => {
        const badges = {
            pendiente: 'bg-yellow-100 text-yellow-800 border-yellow-300',
            confirmado: 'bg-blue-100 text-blue-800 border-blue-300',
            cancelado: 'bg-red-100 text-red-800 border-red-300',
            atendido: 'bg-green-100 text-green-800 border-green-300'
        };
        return badges[estado] || 'bg-gray-100 text-gray-800 border-gray-300';
    };

    return (
        <div className="max-w-7xl mx-auto p-6">
            {/* Header */}
            <div className="mb-6">
                <h1 className="text-3xl font-bold text-gray-900">
                    📋 Recepción - Pre-registros
                </h1>
                <p className="text-gray-600 mt-1">
                    Gestiona los pre-registros pendientes de confirmación
                </p>
            </div>

            {/* Filtros y búsqueda */}
            <div className="bg-white rounded-lg shadow-md p-4 mb-6">
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {/* Búsqueda */}
                    <div className="md:col-span-2">
                        <input
                            type="text"
                            placeholder="Buscar por nombre, documento o código..."
                            value={filtros.busqueda}
                            onChange={(e) => setFiltros({...filtros, busqueda: e.target.value})}
                            onKeyPress={(e) => e.key === 'Enter' && handleBuscar()}
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    {/* Estado */}
                    <div>
                        <select
                            value={filtros.estado}
                            onChange={(e) => setFiltros({...filtros, estado: e.target.value})}
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="pendiente">Pendientes</option>
                            <option value="confirmado">Confirmados</option>
                            <option value="cancelado">Cancelados</option>
                            <option value="atendido">Atendidos</option>
                            <option value="">Todos</option>
                        </select>
                    </div>

                    {/* Fecha */}
                    <div>
                        <input
                            type="date"
                            value={filtros.fecha}
                            onChange={(e) => setFiltros({...filtros, fecha: e.target.value})}
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>

                <div className="mt-3 flex gap-2">
                    <button
                        onClick={handleBuscar}
                        className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                    >
                        🔍 Buscar
                    </button>
                    <button
                        onClick={() => {
                            setFiltros({busqueda: '', fecha: '', estado: 'pendiente'});
                            cargarRegistros();
                        }}
                        className="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                    >
                        Limpiar
                    </button>
                    <button
                        onClick={cargarRegistros}
                        className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                    >
                        ↻ Recargar
                    </button>
                </div>
            </div>

            {/* Contador */}
            <div className="mb-4 text-sm text-gray-600">
                {registrosFiltrados.length} registro(s) encontrado(s)
            </div>

            {/* Error */}
            {error && (
                <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                    {error}
                </div>
            )}

            {/* Loading */}
            {loading && (
                <div className="text-center py-8">
                    <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-blue-600"></div>
                    <p className="mt-2 text-gray-600">Cargando...</p>
                </div>
            )}

            {/* Lista de registros */}
            {!loading && registrosFiltrados.length === 0 && (
                <div className="text-center py-12 bg-gray-50 rounded-lg">
                    <p className="text-gray-500 text-lg">No hay registros para mostrar</p>
                </div>
            )}

            {!loading && registrosFiltrados.length > 0 && (
                <div className="space-y-4">
                    {registrosFiltrados.map((registro) => (
                        <div
                            key={registro.id}
                            className="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow"
                        >
                            <div className="p-5">
                                {/* Fila superior: nombre y estado */}
                                <div className="flex items-start justify-between mb-4">
                                    <div className="flex-1">
                                        <h3 className="text-lg font-bold text-gray-900">
                                            {registro.nombres_completos}
                                        </h3>
                                        <p className="text-sm text-gray-500 mt-1">
                                            Código: <span className="font-mono font-semibold text-blue-600">
                                                {registro.codigo_confirmacion}
                                            </span>
                                        </p>
                                    </div>
                                    <span className={`px-3 py-1 border rounded-full text-sm font-semibold ${getEstadoBadge(registro.estado)}`}>
                                        {registro.estado}
                                    </span>
                                </div>

                                {/* Información */}
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 text-sm">
                                    {registro.numero_documento && (
                                        <div>
                                            <span className="text-gray-600">Documento:</span>
                                            <span className="ml-2 font-semibold">{registro.numero_documento}</span>
                                        </div>
                                    )}
                                    {registro.telefono_contacto && (
                                        <div>
                                            <span className="text-gray-600">Teléfono:</span>
                                            <span className="ml-2 font-semibold">{registro.telefono_contacto}</span>
                                        </div>
                                    )}
                                    {registro.email && (
                                        <div>
                                            <span className="text-gray-600">Email:</span>
                                            <span className="ml-2 font-semibold">{registro.email}</span>
                                        </div>
                                    )}
                                    {registro.fecha_deseada && (
                                        <div>
                                            <span className="text-gray-600">Fecha:</span>
                                            <span className="ml-2 font-semibold">
                                                {new Date(registro.fecha_deseada).toLocaleDateString('es-ES')}
                                                {registro.hora_deseada && ` ${registro.hora_deseada}`}
                                            </span>
                                        </div>
                                    )}
                                    <div>
                                        <span className="text-gray-600">Registrado:</span>
                                        <span className="ml-2 font-semibold">
                                            {new Date(registro.created_at).toLocaleString('es-ES')}
                                        </span>
                                    </div>
                                </div>

                                {registro.motivo && (
                                    <div className="mb-4 p-3 bg-gray-50 rounded text-sm">
                                        <span className="text-gray-600">Motivo:</span>
                                        <p className="mt-1">{registro.motivo}</p>
                                    </div>
                                )}

                                {/* Datos parseados (si existen) */}
                                {registro.datos_parseados && (
                                    <div className="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm">
                                        <p className="text-blue-800 font-semibold mb-1">Datos parseados automáticamente:</p>
                                        <div className="grid grid-cols-2 gap-2 text-xs">
                                            {registro.datos_parseados.primer_nombre && (
                                                <div>Primer nombre: <strong>{registro.datos_parseados.primer_nombre}</strong></div>
                                            )}
                                            {registro.datos_parseados.segundo_nombre && (
                                                <div>Segundo nombre: <strong>{registro.datos_parseados.segundo_nombre}</strong></div>
                                            )}
                                            {registro.datos_parseados.primer_apellido && (
                                                <div>Primer apellido: <strong>{registro.datos_parseados.primer_apellido}</strong></div>
                                            )}
                                            {registro.datos_parseados.segundo_apellido && (
                                                <div>Segundo apellido: <strong>{registro.datos_parseados.segundo_apellido}</strong></div>
                                            )}
                                        </div>
                                    </div>
                                )}

                                {/* Acciones */}
                                {registro.estado === 'pendiente' && (
                                    <div className="flex gap-2">
                                        <button
                                            onClick={() => handleConfirmar(registro)}
                                            className="flex-1 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors"
                                        >
                                            ✅ Confirmar y completar registro
                                        </button>
                                        <button
                                            onClick={() => handleCancelar(registro.id)}
                                            className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                                        >
                                            ❌ Cancelar
                                        </button>
                                    </div>
                                )}

                                {registro.estado === 'confirmado' && registro.persona && (
                                    <div className="p-3 bg-green-50 border border-green-200 rounded text-sm text-green-800">
                                        ✅ Confirmado - Persona registrada: ID {registro.persona.id}
                                    </div>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
};

export default RecepcionPreRegistros;

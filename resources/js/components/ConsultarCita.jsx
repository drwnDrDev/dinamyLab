import React, { useState } from 'react';
import axios from 'axios';

/**
 * Componente para que usuarios consulten el estado de su pre-registro
 * Búsqueda por código de confirmación o por documento
 * NO requiere autenticación
 */
const ConsultarCita = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [busqueda, setBusqueda] = useState('');
    const [resultados, setResultados] = useState([]);

    const handleBuscar = async (e) => {
        e.preventDefault();

        if (!busqueda.trim()) {
            setError('Ingresa un código o número de documento');
            return;
        }

        setLoading(true);
        setError(null);
        setResultados([]);

        try {
            const response = await axios.get(`/api/citas/consultar/${busqueda.trim()}`);
            setResultados(response.data.data || []);

            if (response.data.data.length === 0) {
                setError('No se encontraron registros con ese código o documento');
            }
        } catch (err) {
            console.error('Error:', err);
            setError(err.response?.data?.message || 'Error al consultar');
        } finally {
            setLoading(false);
        }
    };

    const getEstadoBadge = (estado) => {
        const badges = {
            pendiente: 'bg-yellow-100 text-yellow-800',
            confirmado: 'bg-blue-100 text-blue-800',
            cancelado: 'bg-red-100 text-red-800',
            atendido: 'bg-green-100 text-green-800'
        };
        return badges[estado] || 'bg-gray-100 text-gray-800';
    };

    const getEstadoTexto = (estado) => {
        const textos = {
            pendiente: '⏳ Pendiente de confirmación',
            confirmado: '✅ Confirmado - Puedes acudir',
            cancelado: '❌ Cancelado',
            atendido: '✔️ Atendido'
        };
        return textos[estado] || estado;
    };

    return (
        <div className="max-w-3xl mx-auto p-8">
            <div className="bg-white rounded-lg shadow-lg border border-gray-200">
                {/* Header */}
                <div className="p-6 border-b border-gray-200 bg-teal-50">
                    <h2 className="text-2xl font-bold text-gray-800">
                        Consultar Estado de Cita
                    </h2>
                    <p className="text-sm text-gray-600 mt-2">
                        Ingresa tu código de confirmación o número de documento
                    </p>
                </div>

                {/* Formulario de búsqueda */}
                <div className="p-6">
                    <form onSubmit={handleBuscar} className="space-y-4">
                        <div>
                            <input
                                type="text"
                                value={busqueda}
                                onChange={(e) => setBusqueda(e.target.value)}
                                placeholder="Código o Documento"
                                className="w-full px-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono"
                            />
                            <p className="mt-1 text-xs text-gray-500">
                                Ej: ABC12345 o 1012555321
                            </p>
                        </div>

                        <button
                            type="submit"
                            disabled={loading}
                            className="w-full py-3 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {loading ? 'Buscando...' : '🔍 Buscar'}
                        </button>
                    </form>

                    {/* Errores */}
                    {error && (
                        <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                            {error}
                        </div>
                    )}

                    {/* Resultados */}
                    {resultados.length > 0 && (
                        <div className="mt-6 space-y-4">
                            {resultados.map((registro) => (
                                <div
                                    key={registro.id}
                                    className="border border-gray-200 rounded-lg p-6 bg-gray-50"
                                >
                                    {/* Estado */}
                                    <div className="flex items-center justify-between mb-4">
                                        <span className={`px-3 py-1 rounded-full text-sm font-semibold ${getEstadoBadge(registro.estado)}`}>
                                            {getEstadoTexto(registro.estado)}
                                        </span>
                                        <span className="text-sm text-gray-500">
                                            {new Date(registro.created_at).toLocaleDateString('es-ES')}
                                        </span>
                                    </div>

                                    {/* Información */}
                                    <div className="space-y-3">
                                        <div>
                                            <p className="text-sm text-gray-600">Nombre</p>
                                            <p className="text-lg font-semibold text-gray-800">
                                                {registro.nombres_completos}
                                            </p>
                                        </div>

                                        {registro.numero_documento && (
                                            <div>
                                                <p className="text-sm text-gray-600">Documento</p>
                                                <p className="font-mono text-gray-800">
                                                    {registro.numero_documento}
                                                </p>
                                            </div>
                                        )}

                                        <div>
                                            <p className="text-sm text-gray-600">Código de confirmación</p>
                                            <p className="text-xl font-mono font-bold text-teal-600">
                                                {registro.codigo_confirmacion}
                                            </p>
                                        </div>

                                        {registro.fecha_deseada && (
                                            <div>
                                                <p className="text-sm text-gray-600">Fecha deseada</p>
                                                <p className="text-gray-800">
                                                    {new Date(registro.fecha_deseada).toLocaleDateString('es-ES', {
                                                        weekday: 'long',
                                                        year: 'numeric',
                                                        month: 'long',
                                                        day: 'numeric'
                                                    })}
                                                    {registro.hora_deseada && ` a las ${registro.hora_deseada}`}
                                                </p>
                                            </div>
                                        )}

                                        {registro.motivo && (
                                            <div>
                                                <p className="text-sm text-gray-600">Motivo</p>
                                                <p className="text-gray-800">{registro.motivo}</p>
                                            </div>
                                        )}

                                        {registro.telefono_contacto && (
                                            <div>
                                                <p className="text-sm text-gray-600">Teléfono</p>
                                                <p className="text-gray-800">{registro.telefono_contacto}</p>
                                            </div>
                                        )}
                                    </div>

                                    {/* Instrucciones según estado */}
                                    {registro.estado === 'pendiente' && (
                                        <div className="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
                                            <p className="font-semibold mb-1">Instrucciones:</p>
                                            <p>Tu registro está pendiente de confirmación. Acude a recepción con tu código o documento para completar el registro.</p>
                                        </div>
                                    )}

                                    {registro.estado === 'confirmado' && (
                                        <div className="mt-4 p-4 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
                                            <p className="font-semibold mb-1">¡Registro confirmado!</p>
                                            <p>Tu cita ha sido confirmada. Por favor presenta tu código o documento al llegar.</p>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ConsultarCita;

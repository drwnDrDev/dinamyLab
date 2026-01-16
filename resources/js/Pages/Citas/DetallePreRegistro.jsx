import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function DetallePreRegistro({ preRegistro }) {
    const { data, setData, put, delete: destroy, processing, errors } = useForm({
        estado: preRegistro.estado,
    });

    const [mostrarFormulario, setMostrarFormulario] = useState(false);

    const handleCambiarEstado = (e) => {
        e.preventDefault();
        put(route('citas.updateEstado', preRegistro.id), {
            onSuccess: () => {
                setMostrarFormulario(false);
            },
        });
    };

    const handleCancelar = () => {
        if (confirm('¿Estás seguro de que deseas cancelar este pre-registro?')) {
            destroy(route('citas.cancelar', preRegistro.id));
        }
    };

    const getEstadoBadge = (estado) => {
        const badges = {
            pendiente: { bg: 'bg-yellow-100', text: 'text-yellow-800', label: 'Pendiente' },
            confirmada: { bg: 'bg-green-100', text: 'text-green-800', label: 'Confirmada' },
            procesada: { bg: 'bg-blue-100', text: 'text-blue-800', label: 'Procesada' },
            cancelada: { bg: 'bg-red-100', text: 'text-red-800', label: 'Cancelada' },
        };
        return badges[estado] || badges.pendiente;
    };

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('es-CO', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    const formatDateTime = (datetime) => {
        return new Date(datetime).toLocaleString('es-CO', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const badge = getEstadoBadge(preRegistro.estado);

    return (
        <div className="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
            <div className="max-w-4xl mx-auto">
                {/* Encabezado */}
                <div className="mb-8 flex justify-between items-center">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">
                            Detalles Pre-Registro
                        </h1>
                        <p className="text-gray-600 mt-2">
                            Código: <span className="font-mono font-semibold">{preRegistro.codigo_confirmacion}</span>
                        </p>
                    </div>
                    <a
                        href={route('citas.index')}
                        className="text-indigo-600 hover:text-indigo-900 font-medium"
                    >
                        ← Volver al listado
                    </a>
                </div>

                {/* Cards principales */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    {/* Card de estado */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">
                            Estado Actual
                        </h2>
                        <div>
                            <span
                                className={`inline-block px-4 py-2 rounded-lg text-lg font-semibold ${badge.bg} ${badge.text}`}
                            >
                                {badge.label}
                            </span>
                        </div>
                    </div>

                    {/* Card de fechas */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">
                            Fechas
                        </h2>
                        <div className="space-y-2 text-sm">
                            <div>
                                <p className="text-gray-500">Creado</p>
                                <p className="text-gray-900 font-medium">
                                    {formatDateTime(preRegistro.created_at)}
                                </p>
                            </div>
                            {preRegistro.fecha_confirmacion && (
                                <div>
                                    <p className="text-gray-500">Confirmado</p>
                                    <p className="text-gray-900 font-medium">
                                        {formatDateTime(preRegistro.fecha_confirmacion)}
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Card de contacto rápido */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">
                            Contacto Rápido
                        </h2>
                        <div className="space-y-2">
                            <a
                                href={`mailto:${preRegistro.email}`}
                                className="block text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                            >
                                📧 {preRegistro.email}
                            </a>
                            <a
                                href={`tel:${preRegistro.telefono_contacto}`}
                                className="block text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                            >
                                📱 {preRegistro.telefono_contacto}
                            </a>
                        </div>
                    </div>
                </div>

                {/* Información Personal */}
                <div className="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 className="text-lg font-semibold text-gray-900 mb-4">
                        Información Personal
                    </h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p className="text-sm text-gray-500">Nombres Completos</p>
                            <p className="text-gray-900 font-medium">
                                {preRegistro.nombres_completos}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Tipo de Documento</p>
                            <p className="text-gray-900 font-medium">
                                {preRegistro.tipo_documento}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Número de Documento</p>
                            <p className="text-gray-900 font-medium">
                                {preRegistro.numero_documento}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Email</p>
                            <p className="text-gray-900 font-medium">
                                {preRegistro.email}
                            </p>
                        </div>
                        <div className="md:col-span-2">
                            <p className="text-sm text-gray-500">Teléfono de Contacto</p>
                            <p className="text-gray-900 font-medium">
                                {preRegistro.telefono_contacto}
                            </p>
                        </div>
                    </div>
                </div>

                {/* Información de la Cita */}
                <div className="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 className="text-lg font-semibold text-gray-900 mb-4">
                        Información de la Cita
                    </h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p className="text-sm text-gray-500">Fecha Deseada</p>
                            <p className="text-gray-900 font-medium">
                                {formatDate(preRegistro.fecha_deseada)}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Hora Deseada</p>
                            <p className="text-gray-900 font-medium">
                                {new Date(`2000-01-01 ${preRegistro.hora_deseada}`).toLocaleTimeString('es-CO', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                })}
                            </p>
                        </div>
                        {preRegistro.motivo && (
                            <div className="md:col-span-2">
                                <p className="text-sm text-gray-500">Motivo</p>
                                <p className="text-gray-900 font-medium">
                                    {preRegistro.motivo}
                                </p>
                            </div>
                        )}
                        {preRegistro.observaciones && (
                            <div className="md:col-span-2">
                                <p className="text-sm text-gray-500">Observaciones</p>
                                <p className="text-gray-900 font-medium">
                                    {preRegistro.observaciones}
                                </p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Información Relacionada */}
                {(preRegistro.persona_id || preRegistro.orden_id) && (
                    <div className="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 className="text-lg font-semibold text-gray-900 mb-4">
                            Información Relacionada
                        </h2>
                        <div className="space-y-3">
                            {preRegistro.persona_id && (
                                <div>
                                    <p className="text-sm text-gray-500">Persona Asociada</p>
                                    <a
                                        href={route('personas.show', preRegistro.persona.id)}
                                        className="text-indigo-600 hover:text-indigo-900 font-medium"
                                    >
                                        {preRegistro.persona.nombres} {preRegistro.persona.apellidos}
                                    </a>
                                </div>
                            )}
                            {preRegistro.orden_id && (
                                <div>
                                    <p className="text-sm text-gray-500">Orden Médica Asociada</p>
                                    <a
                                        href={route('ordenes.show', preRegistro.orden.id)}
                                        className="text-indigo-600 hover:text-indigo-900 font-medium"
                                    >
                                        Orden #{preRegistro.orden.id}
                                    </a>
                                </div>
                            )}
                        </div>
                    </div>
                )}

                {/* Acciones */}
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h2 className="text-lg font-semibold text-gray-900 mb-4">
                        Acciones
                    </h2>

                    {!mostrarFormulario ? (
                        <div className="space-y-3">
                            <button
                                onClick={() => setMostrarFormulario(true)}
                                className="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200"
                            >
                                Cambiar Estado
                            </button>
                            {preRegistro.estado !== 'cancelada' && (
                                <button
                                    onClick={handleCancelar}
                                    className="w-full bg-red-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200"
                                >
                                    Cancelar Pre-registro
                                </button>
                            )}
                        </div>
                    ) : (
                        <form onSubmit={handleCambiarEstado} className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Nuevo Estado
                                </label>
                                <select
                                    value={data.estado}
                                    onChange={(e) => setData('estado', e.target.value)}
                                    className={`w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
                                        errors.estado ? 'border-red-500' : 'border-gray-300'
                                    }`}
                                >
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="procesada">Procesada</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                                {errors.estado && (
                                    <p className="mt-1 text-sm text-red-600">{errors.estado}</p>
                                )}
                            </div>

                            <div className="flex gap-3">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="flex-1 bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 disabled:opacity-50 transition duration-200"
                                >
                                    {processing ? 'Actualizando...' : 'Actualizar'}
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setMostrarFormulario(false)}
                                    className="flex-1 bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg hover:bg-gray-500 transition duration-200"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    )}
                </div>
            </div>
        </div>
    );
}

import React, { useState } from 'react';
import { useForm, usePage } from '@inertiajs/react';

export default function ListadoPreRegistros({ preRegistros, filtros = {} }) {
    const { data, setData, get, processing } = useForm({
        estado: filtros.estado || '',
        fecha_desde: filtros.fecha_desde || '',
        fecha_hasta: filtros.fecha_hasta || '',
    });

    const handleFiltrar = (e) => {
        e.preventDefault();
        get(route('citas.filtrar'));
    };

    const handleLimpiar = () => {
        setData({
            estado: '',
            fecha_desde: '',
            fecha_hasta: '',
        });
        get(route('citas.index'));
    };

    const getEstadoBadge = (estado) => {
        const badges = {
            pendiente: { bg: 'bg-yellow-100', text: 'text-yellow-800', label: 'Pendiente' },
            confirmada: { bg: 'bg-green-100', text: 'text-green-800', label: 'Confirmada' },
            procesada: { bg: 'bg-blue-100', text: 'text-blue-800', label: 'Procesada' },
            cancelada: { bg: 'bg-red-100', text: 'text-red-800', label: 'Cancelada' },
        };
        const badge = badges[estado] || badges.pendiente;
        return badge;
    };

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('es-CO', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    return (
        <div className="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
            <div className="max-w-7xl mx-auto">
                {/* Encabezado */}
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">
                        Pre-Registros de Citas
                    </h1>
                    <p className="text-gray-600 mt-2">
                        Gestiona y visualiza todos los registros de citas
                    </p>
                </div>

                {/* Filtros */}
                <div className="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 className="text-lg font-semibold text-gray-900 mb-4">
                        Filtros
                    </h2>
                    <form onSubmit={handleFiltrar} className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {/* Estado */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Estado
                                </label>
                                <select
                                    value={data.estado}
                                    onChange={(e) => setData('estado', e.target.value)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="procesada">Procesada</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>

                            {/* Fecha Desde */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Desde
                                </label>
                                <input
                                    type="date"
                                    value={data.fecha_desde}
                                    onChange={(e) => setData('fecha_desde', e.target.value)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            {/* Fecha Hasta */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Hasta
                                </label>
                                <input
                                    type="date"
                                    value={data.fecha_hasta}
                                    onChange={(e) => setData('fecha_hasta', e.target.value)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                        </div>

                        {/* Botones de acción */}
                        <div className="flex gap-4 pt-4">
                            <button
                                type="submit"
                                disabled={processing}
                                className="bg-indigo-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition duration-200"
                            >
                                {processing ? 'Buscando...' : 'Buscar'}
                            </button>
                            <button
                                type="button"
                                onClick={handleLimpiar}
                                className="bg-gray-400 text-white font-semibold py-2 px-6 rounded-lg hover:bg-gray-500 transition duration-200"
                            >
                                Limpiar Filtros
                            </button>
                        </div>
                    </form>
                </div>

                {/* Tabla de pre-registros */}
                <div className="bg-white rounded-lg shadow-md overflow-hidden">
                    {preRegistros.data && preRegistros.data.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-100">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                                            Nombre
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                                            Documento
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                                            Teléfono
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                                            Fecha Deseada
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200">
                                    {preRegistros.data.map((registro) => {
                                        const badge = getEstadoBadge(registro.estado);
                                        return (
                                            <tr key={registro.id} className="hover:bg-gray-50">
                                                <td className="px-6 py-4 text-sm text-gray-900 font-medium">
                                                    {registro.nombres_completos}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-700">
                                                    {registro.numero_documento}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-700">
                                                    {registro.email}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-700">
                                                    {registro.telefono_contacto}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-700">
                                                    {formatDate(registro.fecha_deseada)}
                                                </td>
                                                <td className="px-6 py-4 text-sm">
                                                    <span
                                                        className={`inline-block px-3 py-1 rounded-full text-xs font-medium ${badge.bg} ${badge.text}`}
                                                    >
                                                        {badge.label}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-700">
                                                    <div className="flex gap-2">
                                                        <a
                                                            href={route('citas.show', registro.id)}
                                                            className="text-indigo-600 hover:text-indigo-900 font-medium"
                                                        >
                                                            Ver
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <div className="text-center py-12">
                            <svg
                                className="mx-auto h-12 w-12 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <p className="mt-4 text-gray-600">
                                No hay pre-registros disponibles
                            </p>
                        </div>
                    )}
                </div>

                {/* Paginación */}
                {preRegistros.links && preRegistros.links.length > 3 && (
                    <div className="mt-6 flex justify-center gap-2">
                        {preRegistros.links.map((link, index) => (
                            <a
                                key={index}
                                href={link.url}
                                className={`px-4 py-2 rounded-lg text-sm font-medium transition duration-200 ${
                                    link.active
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}

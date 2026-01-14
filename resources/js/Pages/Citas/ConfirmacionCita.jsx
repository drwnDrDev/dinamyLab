import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function ConfirmacionCita({ preRegistro }) {
    const { post, processing } = useForm();

    const handleConfirmar = () => {
        post(route('citas.confirmar', { codigo: preRegistro.codigo_confirmacion }));
    };

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('es-CO', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    const formatTime = (time) => {
        return new Date(`2000-01-01 ${time}`).toLocaleTimeString('es-CO', {
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
            <div className="max-w-2xl mx-auto">
                {/* Card de confirmación */}
                <div className="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <div className="text-center mb-8">
                        <div className="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                            <svg
                                className="w-8 h-8 text-blue-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <h1 className="text-3xl font-bold text-gray-900 mb-2">
                            Confirmar Cita
                        </h1>
                        <p className="text-gray-600">
                            Use el código de confirmación para validar su cita
                        </p>
                    </div>

                    {/* Código de Confirmación */}
                    <div className="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-8 text-center">
                        <p className="text-gray-600 text-sm mb-2">
                            Su código de confirmación es:
                        </p>
                        <p className="text-4xl font-bold text-blue-600 font-mono tracking-wider">
                            {preRegistro.codigo_confirmacion}
                        </p>
                        <p className="text-gray-500 text-xs mt-2">
                            Guarde este código para referencia futura
                        </p>
                    </div>

                    {/* Datos Registrados */}
                    <div className="mb-8">
                        <h2 className="text-xl font-semibold text-gray-800 mb-4">
                            Datos Registrados
                        </h2>

                        <div className="grid grid-cols-2 gap-4">
                            {/* Información Personal */}
                            <div className="col-span-2 md:col-span-1">
                                <div className="space-y-3">
                                    <div>
                                        <p className="text-sm text-gray-500">Nombre Completo</p>
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
                                </div>
                            </div>

                            {/* Información de Contacto */}
                            <div className="col-span-2 md:col-span-1">
                                <div className="space-y-3">
                                    <div>
                                        <p className="text-sm text-gray-500">Email</p>
                                        <p className="text-gray-900 font-medium">
                                            {preRegistro.email}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">Teléfono</p>
                                        <p className="text-gray-900 font-medium">
                                            {preRegistro.telefono_contacto}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* Información de la Cita */}
                            <div className="col-span-2">
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <p className="text-sm text-gray-500">Fecha Deseada</p>
                                        <p className="text-gray-900 font-medium">
                                            {formatDate(preRegistro.fecha_deseada)}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">Hora Deseada</p>
                                        <p className="text-gray-900 font-medium">
                                            {formatTime(preRegistro.hora_deseada)}
                                        </p>
                                    </div>
                                    {preRegistro.motivo && (
                                        <div className="col-span-2">
                                            <p className="text-sm text-gray-500">Motivo</p>
                                            <p className="text-gray-900 font-medium">
                                                {preRegistro.motivo}
                                            </p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Estado */}
                    <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                        <div className="flex items-start">
                            <div className="flex-shrink-0">
                                <svg
                                    className="h-5 w-5 text-yellow-600"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fillRule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clipRule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm text-yellow-700">
                                    <strong>Estado actual:</strong> {preRegistro.estado}
                                </p>
                                <p className="text-sm text-yellow-600 mt-1">
                                    Haga clic en el botón de abajo para confirmar su cita
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Botones */}
                    <div className="flex gap-4">
                        <button
                            onClick={handleConfirmar}
                            disabled={processing}
                            className="flex-1 bg-green-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                        >
                            {processing ? 'Confirmando...' : 'Confirmar Cita'}
                        </button>
                        <a
                            href={route('welcome')}
                            className="flex-1 bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg hover:bg-gray-400 transition duration-200 text-center"
                        >
                            Volver al Inicio
                        </a>
                    </div>
                </div>

                {/* Mensaje informativo */}
                <div className="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                    <p className="text-gray-700">
                        Se ha enviado un email de confirmación a
                        <strong className="block text-blue-600 mt-1">
                            {preRegistro.email}
                        </strong>
                    </p>
                </div>
            </div>
        </div>
    );
}

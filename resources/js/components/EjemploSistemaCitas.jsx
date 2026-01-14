import React, { useState } from 'react';
import FormPreRegistroCita from './FormPreRegistroCita';
import PreRegistroListaCitas from './PreRegistroListaCitas';
import ConsultarCita from './ConsultarCita';
import RecepcionCitas from './RecepcionCitas';

/**
 * Componente de ejemplo que muestra todo el flujo del sistema de pre-registro de citas
 *
 * PÚBLICO (sin autenticación):
 * - FormPreRegistroCita: Un solo pre-registro
 * - PreRegistroListaCitas: Múltiples pre-registros
 * - ConsultarCita: Consultar estado
 *
 * RECEPCIÓN (requiere autenticación):
 * - RecepcionCitas: Gestionar y confirmar pre-registros
 */
const EjemploSistemaCitas = ({ FormPersona, esRecepcion = false }) => {
    const [vistaPublica, setVistaPublica] = useState('individual'); // 'individual' | 'multiple' | 'consultar'

    // Si es vista de recepción, mostrar solo el componente de recepción
    if (esRecepcion) {
        return (
            <div>
                <RecepcionCitas FormPersona={FormPersona} />
            </div>
        );
    }

    // Vista pública - mostrar opciones para usuarios finales
    return (
        <div className="min-h-screen bg-gray-50">
            {/* Header con navegación */}
            <div className="bg-white border-b shadow-sm">
                <div className="max-w-7xl mx-auto px-6 py-4">
                    <h1 className="text-3xl font-bold text-gray-900 mb-4">
                        Sistema de Pre-registro de Citas
                    </h1>

                    {/* Tabs de navegación */}
                    <div className="flex gap-2">
                        <button
                            onClick={() => setVistaPublica('individual')}
                            className={`px-4 py-2 rounded-lg font-semibold transition-colors ${
                                vistaPublica === 'individual'
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            }`}
                        >
                            📝 Registro Individual
                        </button>
                        <button
                            onClick={() => setVistaPublica('multiple')}
                            className={`px-4 py-2 rounded-lg font-semibold transition-colors ${
                                vistaPublica === 'multiple'
                                    ? 'bg-purple-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            }`}
                        >
                            📋 Registro Múltiple
                        </button>
                        <button
                            onClick={() => setVistaPublica('consultar')}
                            className={`px-4 py-2 rounded-lg font-semibold transition-colors ${
                                vistaPublica === 'consultar'
                                    ? 'bg-teal-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            }`}
                        >
                            🔍 Consultar Estado
                        </button>
                    </div>
                </div>
            </div>

            {/* Contenido según vista seleccionada */}
            <div className="py-8">
                {vistaPublica === 'individual' && (
                    <div>
                        <div className="max-w-4xl mx-auto mb-6 px-6">
                            <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h3 className="font-semibold text-blue-900 mb-2">ℹ️ Registro Individual</h3>
                                <p className="text-sm text-blue-800">
                                    Registra una cita para ti o para otra persona. Solo necesitas proporcionar el nombre completo,
                                    los demás datos son opcionales. Recibirás un código de confirmación que debes presentar en recepción.
                                </p>
                            </div>
                        </div>
                        <FormPreRegistroCita />
                    </div>
                )}

                {vistaPublica === 'multiple' && (
                    <div>
                        <div className="max-w-4xl mx-auto mb-6 px-6">
                            <div className="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <h3 className="font-semibold text-purple-900 mb-2">ℹ️ Registro Múltiple</h3>
                                <p className="text-sm text-purple-800">
                                    Ideal para registrar varias personas a la vez (familia, grupo). Escribe los nombres
                                    separados por comas, uno por línea. Cada persona recibirá su propio código de confirmación.
                                </p>
                            </div>
                        </div>
                        <PreRegistroListaCitas />
                    </div>
                )}

                {vistaPublica === 'consultar' && (
                    <div>
                        <div className="max-w-3xl mx-auto mb-6 px-6">
                            <div className="bg-teal-50 border border-teal-200 rounded-lg p-4">
                                <h3 className="font-semibold text-teal-900 mb-2">ℹ️ Consultar Estado</h3>
                                <p className="text-sm text-teal-800">
                                    Verifica el estado de tu pre-registro usando tu código de confirmación o número de documento.
                                    Aquí podrás ver si tu registro está pendiente, confirmado o ya fue atendido.
                                </p>
                            </div>
                        </div>
                        <ConsultarCita />
                    </div>
                )}
            </div>

            {/* Footer informativo */}
            <div className="max-w-7xl mx-auto px-6 py-8">
                <div className="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                    <h3 className="text-lg font-bold text-gray-900 mb-3">
                        ¿Cómo funciona el sistema?
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                        <div>
                            <div className="flex items-center gap-2 mb-2">
                                <span className="text-2xl">1️⃣</span>
                                <h4 className="font-semibold text-gray-800">Pre-registra tu cita</h4>
                            </div>
                            <p className="text-gray-600">
                                Completa el formulario con tus datos básicos. No te preocupes si no tienes toda la información,
                                el personal de recepción te ayudará a completarla.
                            </p>
                        </div>
                        <div>
                            <div className="flex items-center gap-2 mb-2">
                                <span className="text-2xl">2️⃣</span>
                                <h4 className="font-semibold text-gray-800">Guarda tu código</h4>
                            </div>
                            <p className="text-gray-600">
                                Recibirás un código de confirmación único. Guárdalo o toma una captura de pantalla.
                                Lo necesitarás al llegar a la clínica.
                            </p>
                        </div>
                        <div>
                            <div className="flex items-center gap-2 mb-2">
                                <span className="text-2xl">3️⃣</span>
                                <h4 className="font-semibold text-gray-800">Acude a recepción</h4>
                            </div>
                            <p className="text-gray-600">
                                Presenta tu código o documento en recepción. El personal verificará tus datos y
                                completará tu registro formal para atenderte.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default EjemploSistemaCitas;

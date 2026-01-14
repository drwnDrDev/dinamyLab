import React from 'react';

export default function CitaExito() {
    return (
        <div className="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div className="max-w-md w-full text-center">
                {/* Icono de éxito */}
                <div className="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
                    <svg
                        className="w-10 h-10 text-green-600"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fillRule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clipRule="evenodd"
                        />
                    </svg>
                </div>

                {/* Contenido principal */}
                <div className="bg-white rounded-lg shadow-lg p-8">
                    <h1 className="text-3xl font-bold text-gray-900 mb-3">
                        ¡Cita Confirmada!
                    </h1>

                    <p className="text-gray-600 mb-6">
                        Tu cita ha sido registrada y confirmada exitosamente.
                    </p>

                    <div className="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <p className="text-gray-700 mb-2">
                            Los detalles de tu cita han sido enviados a tu correo electrónico.
                        </p>
                        <p className="text-sm text-gray-600">
                            Por favor, revisa tu bandeja de entrada y tu carpeta de spam.
                        </p>
                    </div>

                    {/* Próximos pasos */}
                    <div className="text-left bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h2 className="font-semibold text-blue-900 mb-3">
                            Próximos Pasos:
                        </h2>
                        <ol className="space-y-2 text-sm text-blue-800">
                            <li className="flex items-start">
                                <span className="font-bold mr-2">1.</span>
                                <span>Revisa el correo de confirmación</span>
                            </li>
                            <li className="flex items-start">
                                <span className="font-bold mr-2">2.</span>
                                <span>Presenta tu documento de identificación en la cita</span>
                            </li>
                            <li className="flex items-start">
                                <span className="font-bold mr-2">3.</span>
                                <span>Llega con 10 minutos de anticipación</span>
                            </li>
                            <li className="flex items-start">
                                <span className="font-bold mr-2">4.</span>
                                <span>Si necesitas cambiar la cita, contáctanos</span>
                            </li>
                        </ol>
                    </div>

                    {/* Botones */}
                    <div className="flex flex-col gap-3">
                        <a
                            href={route('welcome')}
                            className="bg-green-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-green-700 transition duration-200 text-center"
                        >
                            Volver al Inicio
                        </a>
                    </div>
                </div>

                {/* Contacto */}
                <div className="mt-6 text-gray-600 text-sm">
                    <p>
                        ¿Preguntas?{' '}
                        <a href="mailto:contacto@laboratorio.com" className="text-green-600 hover:underline">
                            Contáctanos aquí
                        </a>
                    </p>
                </div>
            </div>
        </div>
    );
}

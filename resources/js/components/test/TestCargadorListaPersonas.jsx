import React from 'react';
import CargadorListaPersonas from '../CargadorListaPersonas';

/**
 * Componente de prueba para el módulo CargadorListaPersonas
 *
 * USO:
 * - Importar este componente en tus rutas
 * - Visitar la ruta para probar el cargador
 *
 * Datos de ejemplo:
 * Carlos Ramirez,1012555321
 * Luiz Alberto Diaz, 10101010
 * Zonia Ramirez Fierro,
 * Liliana Diaz Marun, 123123654
 */
const TestCargadorListaPersonas = () => {
    const [personasSeleccionadas, setPersonasSeleccionadas] = React.useState([]);

    const handlePersonaSeleccionada = (persona) => {
        console.log('👤 Persona seleccionada:', persona);
        setPersonasSeleccionadas(prev => [...prev, persona]);
    };

    return (
        <div className="p-8 bg-gray-100 min-h-screen">
            <div className="max-w-6xl mx-auto">
                <h1 className="text-3xl font-bold text-gray-900 mb-8">
                    Prueba: Cargador de Lista de Personas
                </h1>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Lado izquierdo: Cargador */}
                    <div>
                        <h2 className="text-xl font-semibold text-gray-800 mb-4">
                            Cargador
                        </h2>
                        <CargadorListaPersonas
                            onPersonasLoaded={handlePersonaSeleccionada}
                            perfil="Paciente"
                        />
                    </div>

                    {/* Lado derecho: Personas seleccionadas */}
                    <div>
                        <h2 className="text-xl font-semibold text-gray-800 mb-4">
                            Personas seleccionadas ({personasSeleccionadas.length})
                        </h2>

                        {personasSeleccionadas.length === 0 ? (
                            <div className="p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-800">
                                <p>Selecciona una persona del cargador para verla aquí</p>
                            </div>
                        ) : (
                            <div className="space-y-4 max-h-96 overflow-y-auto">
                                {personasSeleccionadas.map((persona, idx) => (
                                    <div
                                        key={idx}
                                        className="p-4 bg-white border border-gray-300 rounded-lg shadow-sm hover:shadow-md transition-shadow"
                                    >
                                        <div className="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p className="font-medium text-gray-700">Nombre</p>
                                                <p className="text-gray-600">
                                                    {persona.primer_nombre} {persona.segundo_nombre}
                                                </p>
                                            </div>
                                            <div>
                                                <p className="font-medium text-gray-700">Apellido</p>
                                                <p className="text-gray-600">
                                                    {persona.primer_apellido} {persona.segundo_apellido}
                                                </p>
                                            </div>
                                            <div>
                                                <p className="font-medium text-gray-700">Documento</p>
                                                <p className="text-gray-600">
                                                    {persona.tipo_documento}: {persona.numero_documento || 'N/A'}
                                                </p>
                                            </div>
                                            <div>
                                                <p className="font-medium text-gray-700">Teléfono</p>
                                                <p className="text-gray-600">
                                                    {persona.telefono || 'N/A'}
                                                </p>
                                            </div>
                                            <div className="col-span-2">
                                                <p className="font-medium text-gray-700">Estado</p>
                                                {persona.existente ? (
                                                    <span className="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                                                        ✓ Existente en BD
                                                    </span>
                                                ) : (
                                                    <span className="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                                        + Nuevo registro
                                                    </span>
                                                )}
                                            </div>
                                        </div>

                                        {/* JSON para copiar */}
                                        <details className="mt-3 pt-3 border-t border-gray-200">
                                            <summary className="text-xs font-medium text-gray-700 cursor-pointer hover:text-gray-900">
                                                Ver JSON
                                            </summary>
                                            <pre className="mt-2 p-2 bg-gray-50 text-xs overflow-auto rounded border border-gray-200">
                                                {JSON.stringify(persona, null, 2)}
                                            </pre>
                                        </details>
                                    </div>
                                ))}
                            </div>
                        )}

                        {/* Botón para limpiar */}
                        {personasSeleccionadas.length > 0 && (
                            <button
                                onClick={() => setPersonasSeleccionadas([])}
                                className="mt-4 w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-colors"
                            >
                                Limpiar selecciones
                            </button>
                        )}
                    </div>
                </div>

                {/* Información de uso */}
                <div className="mt-12 p-6 bg-white border border-gray-300 rounded-lg">
                    <h3 className="text-lg font-semibold text-gray-800 mb-4">📋 Información de prueba</h3>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 className="font-medium text-gray-700 mb-2">Datos de ejemplo:</h4>
                            <pre className="p-3 bg-gray-50 border border-gray-300 rounded text-xs overflow-auto">
{`Carlos Ramirez,1012555321
Luiz Alberto Diaz, 10101010
Zonia Ramirez Fierro,
Liliana Diaz Marun, 123123654`}
                            </pre>
                        </div>

                        <div>
                            <h4 className="font-medium text-gray-700 mb-2">Características:</h4>
                            <ul className="text-sm text-gray-600 space-y-1">
                                <li>✅ Parsea nombres y apellidos automáticamente</li>
                                <li>✅ Soporta número de documento opcional</li>
                                <li>✅ Busca personas existentes en BD</li>
                                <li>✅ Muestra estado de cada persona</li>
                                <li>✅ Interfaz intuitiva y responsive</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default TestCargadorListaPersonas;

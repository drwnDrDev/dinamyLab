import React, { useState } from 'react';
import axios from 'axios';
import Loader from './Loader';

/**
 * Componente para cargar una lista de personas desde un textarea
 * y precargar los datos en FormPersona
 *
 * Formato esperado:
 * Carlos Ramirez,1012555321
 * Luiz Alberto Diaz, 10101010
 * Zonia Ramirez Fierro,
 * Liliana Diaz Marun, 123123654
 */
const CargadorListaPersonas = ({ onPersonasLoaded, perfil = 'Paciente' }) => {
    const [contenido, setContenido] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [tipoDocumento, setTipoDocumento] = useState('CC');
    const [personasParseadas, setPersonasParseadas] = useState([]);
    const [mostrarResultados, setMostrarResultados] = useState(false);

    const handleParsear = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        if (!contenido.trim()) {
            setError('Por favor ingresa una lista de personas');
            setLoading(false);
            return;
        }

        try {
            const response = await axios.post('/api/personas/parsear-lista', {
                contenido: contenido,
                tipo_documento: tipoDocumento
            });

            console.log('✅ Lista parseada:', response.data);
            setPersonasParseadas(response.data.data);
            setMostrarResultados(true);
        } catch (err) {
            console.error('❌ Error al parsear:', err);
            setError(
                err.response?.data?.error ||
                err.message ||
                'Error al procesar la lista'
            );
        } finally {
            setLoading(false);
        }
    };

    const handleSeleccionarPersona = (persona) => {
        console.log('Persona seleccionada:', persona);
        if (onPersonasLoaded) {
            onPersonasLoaded(persona);
        }
    };

    const handleLimpiar = () => {
        setContenido('');
        setPersonasParseadas([]);
        setMostrarResultados(false);
        setError(null);
    };

    return (
        <div className="max-w-2xl mx-auto p-4">
            <div className="bg-white rounded-lg shadow-sm border border-gray-200">
                {/* Sección de entrada */}
                {!mostrarResultados ? (
                    <>
                        <div className="p-6 border-b border-gray-200">
                            <h2 className="text-lg font-semibold text-gray-800 mb-4">
                                Cargar lista de {perfil}s
                            </h2>

                            <form onSubmit={handleParsear} className="space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Tipo de Documento
                                    </label>
                                    <select
                                        value={tipoDocumento}
                                        onChange={(e) => setTipoDocumento(e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                                    >
                                        <option value="CC">Cédula de Ciudadanía (CC)</option>
                                        <option value="CE">Cédula de Extranjería (CE)</option>
                                        <option value="PA">Pasaporte (PA)</option>
                                        <option value="PE">Permiso Especial (PE)</option>
                                        <option value="TI">Tarjeta de Identidad (TI)</option>
                                        <option value="RC">Registro Civil (RC)</option>
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Contenido (Nombres Apellidos, Número de Documento)
                                    </label>
                                    <p className="text-xs text-gray-500 mb-2">
                                        Ingresa una persona por línea. El número de documento es opcional.
                                    </p>
                                    <textarea
                                        value={contenido}
                                        onChange={(e) => setContenido(e.target.value)}
                                        placeholder={`Carlos Ramirez,1012555321\nLuiz Alberto Diaz, 10101010\nZonia Ramirez Fierro,\nLiliana Diaz Marun, 123123654`}
                                        className="w-full h-40 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary font-mono text-sm"
                                    />
                                </div>

                                {error && (
                                    <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm">
                                        {error}
                                    </div>
                                )}

                                <div className="flex gap-2">
                                    <button
                                        type="submit"
                                        disabled={loading}
                                        className="flex-1 inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                    >
                                        {loading ? (
                                            <>
                                                <div className="animate-spin mr-2 h-4 w-4 border-b-2 border-white rounded-full"></div>
                                                Parseando...
                                            </>
                                        ) : (
                                            '🔍 Parsear Lista'
                                        )}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div className="p-4 bg-blue-50 border-t border-blue-200">
                            <p className="text-xs text-blue-800">
                                💡 <strong>Tip:</strong> Cada línea debe contener nombre(s) y apellido(s), opcionalmente seguido de una coma y el número de documento.
                            </p>
                        </div>
                    </>
                ) : (
                    <>
                        {/* Sección de resultados */}
                        <div className="p-6 border-b border-gray-200">
                            <div className="flex justify-between items-center mb-4">
                                <h2 className="text-lg font-semibold text-gray-800">
                                    Resultados ({personasParseadas.length})
                                </h2>
                                <button
                                    onClick={handleLimpiar}
                                    className="text-sm px-3 py-1 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md"
                                >
                                    ← Atrás
                                </button>
                            </div>

                            {personasParseadas.length === 0 ? (
                                <div className="p-4 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-md text-sm">
                                    No se encontraron personas para procesar.
                                </div>
                            ) : (
                                <div className="space-y-2 max-h-96 overflow-y-auto">
                                    {personasParseadas.map((persona, idx) => (
                                        <div
                                            key={idx}
                                            onClick={() => handleSeleccionarPersona(persona)}
                                            className={`p-4 border rounded-md cursor-pointer transition-all ${
                                                persona.existente
                                                    ? 'bg-green-50 border-green-300 hover:bg-green-100'
                                                    : 'bg-gray-50 border-gray-300 hover:bg-gray-100'
                                            }`}
                                        >
                                            <div className="flex justify-between items-start gap-4">
                                                <div className="flex-1">
                                                    <p className="font-semibold text-gray-900">
                                                        {persona.primer_nombre} {persona.segundo_nombre} {persona.primer_apellido} {persona.segundo_apellido}
                                                    </p>
                                                    {persona.numero_documento && (
                                                        <p className="text-sm text-gray-600">
                                                            {persona.tipo_documento}: {persona.numero_documento}
                                                        </p>
                                                    )}
                                                    {persona.fecha_nacimiento && (
                                                        <p className="text-sm text-gray-500">
                                                            Nacimiento: {persona.fecha_nacimiento}
                                                        </p>
                                                    )}
                                                </div>
                                                <div className="text-right">
                                                    {persona.existente ? (
                                                        <span className="inline-block px-2 py-1 bg-green-200 text-green-800 text-xs font-medium rounded">
                                                            ✓ Existente
                                                        </span>
                                                    ) : (
                                                        <span className="inline-block px-2 py-1 bg-blue-200 text-blue-800 text-xs font-medium rounded">
                                                            + Nuevo
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="p-4 bg-gray-50 border-t border-gray-200">
                            <p className="text-xs text-gray-600">
                                Haz clic en una persona para cargarla en el formulario.
                            </p>
                        </div>
                    </>
                )}
            </div>

            {loading && <Loader />}
        </div>
    );
};

export default CargadorListaPersonas;

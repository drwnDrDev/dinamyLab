import React, { useState } from 'react';
import axios from 'axios';

/**
 * Componente para pre-registrar MÚLTIPLES citas desde una lista
 * Usuario final puede copiar/pegar lista de familiares/amigos
 * NO requiere autenticación
 */
const PreRegistroListaCitas = ({ onSuccess }) => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [codigos, setCodigos] = useState([]);
    const [form, setForm] = useState({
        contenido: '',
        fecha_deseada: '',
        motivo: '',
    });

    const handleChange = (e) => {
        setForm({
            ...form,
            [e.target.name]: e.target.value
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        try {
            const response = await axios.post('/api/citas/pre-registrar-lista', form);

            console.log('Pre-registros exitosos:', response.data);
            setCodigos(response.data.data);

            if (onSuccess) {
                onSuccess(response.data);
            }
        } catch (err) {
            console.error('Error:', err);
            setError(err.response?.data?.message || 'Error al registrar');
        } finally {
            setLoading(false);
        }
    };

    const handleReset = () => {
        setCodigos([]);
        setForm({
            contenido: '',
            fecha_deseada: '',
            motivo: '',
        });
    };

    if (codigos.length > 0) {
        return (
            <div className="max-w-4xl mx-auto p-8">
                <div className="bg-green-50 border-2 border-green-500 rounded-lg p-6">
                    <h2 className="text-2xl font-bold text-green-800 mb-4 text-center">
                        ¡{codigos.length} citas pre-registradas!
                    </h2>

                    <div className="bg-white rounded-lg p-6 mb-4">
                        <p className="text-sm text-gray-600 mb-4">
                            Códigos de confirmación generados:
                        </p>

                        <div className="space-y-3 max-h-96 overflow-y-auto">
                            {codigos.map((registro, idx) => (
                                <div
                                    key={idx}
                                    className="flex items-center justify-between p-3 bg-gray-50 rounded border border-gray-200"
                                >
                                    <div className="flex-1">
                                        <p className="font-semibold text-gray-800">
                                            {registro.nombres_completos}
                                        </p>
                                        {registro.numero_documento && (
                                            <p className="text-sm text-gray-600">
                                                Doc: {registro.numero_documento}
                                            </p>
                                        )}
                                    </div>
                                    <div className="text-right">
                                        <p className="text-sm text-gray-500">Código:</p>
                                        <p className="text-lg font-mono font-bold text-green-600">
                                            {registro.codigo_confirmacion}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* Botón para copiar todos los códigos */}
                        <button
                            onClick={() => {
                                const texto = codigos.map(r =>
                                    `${r.nombres_completos}: ${r.codigo_confirmacion}`
                                ).join('\n');
                                navigator.clipboard.writeText(texto);
                                alert('Códigos copiados al portapapeles');
                            }}
                            className="mt-4 w-full py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                        >
                            📋 Copiar todos los códigos
                        </button>
                    </div>

                    <div className="text-left space-y-2 text-sm text-gray-700 bg-blue-50 p-4 rounded">
                        <p className="font-semibold">Instrucciones:</p>
                        <p>✅ Guarda estos códigos o toma captura de pantalla</p>
                        <p>✅ Al llegar a recepción, presenta el código o documento</p>
                        <p>✅ El personal completará el registro de cada persona</p>
                    </div>

                    <button
                        onClick={handleReset}
                        className="mt-6 w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700"
                    >
                        Hacer otro registro
                    </button>
                </div>
            </div>
        );
    }

    return (
        <div className="max-w-4xl mx-auto p-8">
            <div className="bg-white rounded-lg shadow-lg border border-gray-200">
                <div className="p-6 border-b border-gray-200 bg-purple-50">
                    <h2 className="text-2xl font-bold text-gray-800">
                        Pre-registro Múltiple de Citas
                    </h2>
                    <p className="text-sm text-gray-600 mt-2">
                        Registra varias personas a la vez. Ideal para familia o grupo.
                    </p>
                </div>

                <form onSubmit={handleSubmit} className="p-6 space-y-6">
                    {/* Lista de personas */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Lista de personas *
                        </label>
                        <textarea
                            name="contenido"
                            value={form.contenido}
                            onChange={handleChange}
                            required
                            rows="8"
                            placeholder={`Escribe una persona por línea:
Carlos Ramirez, 1012555321
Zonia Fierro, 10101010
Juan Pérez,
María López, 123456789

(El documento es opcional)`}
                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-mono text-sm"
                        />
                        <div className="mt-2 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
                            <p className="font-semibold mb-1">Formato:</p>
                            <p>• Una persona por línea</p>
                            <p>• Nombre completo, número de documento (opcional)</p>
                            <p>• Separar con coma</p>
                        </div>
                    </div>

                    {/* Fecha deseada para todos */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Fecha deseada (para todos)
                        </label>
                        <input
                            type="date"
                            name="fecha_deseada"
                            value={form.fecha_deseada}
                            onChange={handleChange}
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        />
                    </div>

                    {/* Motivo común */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Motivo (aplica a todos)
                        </label>
                        <textarea
                            name="motivo"
                            value={form.motivo}
                            onChange={handleChange}
                            rows="3"
                            placeholder="Ej: Exámenes de laboratorio, Consulta general, etc."
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        />
                    </div>

                    {error && (
                        <div className="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                            {error}
                        </div>
                    )}

                    {/* Botón */}
                    <button
                        type="submit"
                        disabled={loading}
                        className="w-full py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        {loading ? 'Registrando...' : `Pre-registrar ${form.contenido.split('\n').filter(l => l.trim()).length || '...'} persona(s)`}
                    </button>

                    <p className="text-xs text-gray-500 text-center">
                        Se generará un código único para cada persona
                    </p>
                </form>
            </div>
        </div>
    );
};

export default PreRegistroListaCitas;

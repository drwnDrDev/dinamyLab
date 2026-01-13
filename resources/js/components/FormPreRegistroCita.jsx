import React, { useState } from 'react';
import axios from 'axios';

/**
 * Formulario PÚBLICO para pre-registro de citas
 * NO requiere autenticación
 * Usuarios sin conocimiento técnico pueden pre-agendar
 */
const FormPreRegistroCita = ({ onSuccess }) => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [codigo, setCodigo] = useState(null);
    const [form, setForm] = useState({
        nombres_completos: '',
        numero_documento: '',
        telefono_contacto: '',
        email: '',
        fecha_deseada: '',
        hora_deseada: '',
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
            const response = await axios.post('/api/citas/pre-registrar', form);

            console.log('Pre-registro exitoso:', response.data);
            setCodigo(response.data.codigo);

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

    if (codigo) {
        return (
            <div className="max-w-2xl mx-auto p-8">
                <div className="bg-green-50 border-2 border-green-500 rounded-lg p-6 text-center">
                    <h2 className="text-2xl font-bold text-green-800 mb-4">
                        ¡Cita pre-registrada!
                    </h2>
                    <div className="bg-white rounded-lg p-6 mb-4">
                        <p className="text-sm text-gray-600 mb-2">Tu código de confirmación es:</p>
                        <p className="text-4xl font-mono font-bold text-green-600 tracking-wider">
                            {codigo}
                        </p>
                    </div>
                    <div className="text-left space-y-2 text-sm text-gray-700">
                        <p>✅ Guarda este código</p>
                        <p>✅ Al llegar a recepción, proporciona el código o tu documento</p>
                        <p>✅ El personal completará tu registro</p>
                    </div>
                    <button
                        onClick={() => {
                            setCodigo(null);
                            setForm({
                                nombres_completos: '',
                                numero_documento: '',
                                telefono_contacto: '',
                                email: '',
                                fecha_deseada: '',
                                hora_deseada: '',
                                motivo: '',
                            });
                        }}
                        className="mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Hacer otro registro
                    </button>
                </div>
            </div>
        );
    }

    return (
        <div className="max-w-2xl mx-auto p-8">
            <div className="bg-white rounded-lg shadow-lg border border-gray-200">
                <div className="p-6 border-b border-gray-200 bg-blue-50">
                    <h2 className="text-2xl font-bold text-gray-800">
                        Pre-registro de Cita
                    </h2>
                    <p className="text-sm text-gray-600 mt-2">
                        Completa los datos básicos. Al llegar a recepción, completaremos tu registro.
                    </p>
                </div>

                <form onSubmit={handleSubmit} className="p-6 space-y-4">
                    {/* Nombre completo */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Nombre completo *
                        </label>
                        <input
                            type="text"
                            name="nombres_completos"
                            value={form.nombres_completos}
                            onChange={handleChange}
                            required
                            placeholder="Ej: Carlos Ramirez López"
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                        <p className="text-xs text-gray-500 mt-1">
                            Escribe tu nombre como lo conoces
                        </p>
                    </div>

                    {/* Documento */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Número de documento (opcional)
                        </label>
                        <input
                            type="text"
                            name="numero_documento"
                            value={form.numero_documento}
                            onChange={handleChange}
                            placeholder="Si lo tienes a mano"
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    {/* Teléfono */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Teléfono de contacto
                        </label>
                        <input
                            type="tel"
                            name="telefono_contacto"
                            value={form.telefono_contacto}
                            onChange={handleChange}
                            placeholder="3001234567"
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    {/* Email */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Email (opcional)
                        </label>
                        <input
                            type="email"
                            name="email"
                            value={form.email}
                            onChange={handleChange}
                            placeholder="tu@email.com"
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    {/* Fecha deseada */}
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Fecha deseada
                            </label>
                            <input
                                type="date"
                                name="fecha_deseada"
                                value={form.fecha_deseada}
                                onChange={handleChange}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Hora deseada
                            </label>
                            <input
                                type="time"
                                name="hora_deseada"
                                value={form.hora_deseada}
                                onChange={handleChange}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>

                    {/* Motivo */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Motivo de la consulta
                        </label>
                        <textarea
                            name="motivo"
                            value={form.motivo}
                            onChange={handleChange}
                            rows="3"
                            placeholder="Describe brevemente el motivo de tu cita"
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                        className="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        {loading ? 'Registrando...' : 'Pre-registrar Cita'}
                    </button>

                    <p className="text-xs text-gray-500 text-center">
                        * Solo necesitas el nombre completo para empezar
                    </p>
                </form>
            </div>
        </div>
    );
};

export default FormPreRegistroCita;

import React, { useState } from 'react';

export default function RegistroCitaAnonimo({ sedes, modalidades, csrfToken, actionUrl }) {
    const [formData, setFormData] = useState({
        nombres_completos: '',
        tipo_documento: 'CC',
        numero_documento: '',
        telefono_contacto: '',
        email: '',
        fecha_deseada: '',
        hora_deseada: '',
        sede_id: '',
        modalidad_id: '',
        motivo: '',
        observaciones: '',
    });

    const [errors, setErrors] = useState({});
    const [processing, setProcessing] = useState(false);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
        // Limpiar error del campo al cambiar
        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: null
            }));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    alert(data.message || 'Error al registrar la cita');
                }
                setProcessing(false);
                return;
            }

            // Redireccionar a confirmación
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
            setProcessing(false);
        }
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
            <div className="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
                {/* Encabezado */}
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900 mb-2">
                        Registrar Nueva Cita
                    </h1>
                    <p className="text-gray-600">
                        Complete el formulario para agendar su cita. No requiere autenticación.
                    </p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Sección Datos Personales */}
                    <div className="border-b pb-6">
                        <h2 className="text-xl font-semibold text-gray-800 mb-4">
                            Datos Personales
                        </h2>

                        {/* Nombres Completos */}
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Nombres Completos *
                            </label>
                            <input
                                type="text"
                                name="nombres_completos"
                                value={formData.nombres_completos}
                                onChange={handleChange}
                                placeholder="Ej: Juan Pérez García"
                                className={`w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
                                    errors.nombres_completos ? 'border-red-500' : 'border-gray-300'
                                }`}
                            />
                            {errors.nombres_completos && (
                                <p className="mt-1 text-sm text-red-600">{errors.nombres_completos[0]}</p>
                            )}
                        </div>

                        {/* Tipo y Número de Documento */}
                        <div className="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Documento *
                                </label>
                                <select
                                    name="tipo_documento"
                                    value={formData.tipo_documento}
                                    onChange={handleChange}
                                    className={`w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
                                        errors.tipo_documento ? 'border-red-500' : 'border-gray-300'
                                    }`}
                                >
                                    <option value="CC">Cédula de Ciudadanía</option>
                                    <option value="CE">Cédula de Extranjería</option>
                                    <option value="TI">Tarjeta de Identidad</option>
                                    <option value="PA">Pasaporte</option>
                                    <option value="PE">Permiso Especial</option>
                                </select>
                                {errors.tipo_documento && (
                                    <p className="mt-1 text-sm text-red-600">{errors.tipo_documento[0]}</p>
                                )}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Número de Documento *
                                </label>
                                <input
                                    type="text"
                                    name="numero_documento"
                                    value={formData.numero_documento}
                                    onChange={handleChange}
                                    placeholder="Ej: 1234567890"
                                    className={`w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
                                        errors.numero_documento ? 'border-red-500' : 'border-gray-300'
                                    }`}
                                />
                                {errors.numero_documento && (
                                    <p className="mt-1 text-sm text-red-600">{errors.numero_documento[0]}</p>
                                )}
                            </div>
                        </div>

                        {/* Teléfono y Email */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Teléfono de Contacto *
                                </label>
                                <input
                                    type="tel"
                                    name="telefono_contacto"
                                    value={formData.telefono_contacto}
                                    onChange={handleChange}
                                    placeholder="Ej: 3001234567"
                                    className={`w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
                                        errors.telefono_contacto ? 'border-red-500' : 'border-gray-300'
                                    }`}
                                />
                                {errors.telefono_contacto && (
                                    <p className="mt-1 text-sm text-red-600">{errors.telefono_contacto[0]}</p>
                                )}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Correo Electrónico *
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    value={formData.email}
                                    onChange={handleChange}
                                    placeholder="Ej: usuario@ejemplo.com"
                                    className={`w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
                                        errors.email ? 'border-red-500' : 'border-gray-300'
                                    }`}
                                />
                                {errors.email && (
                                    <p className="mt-1 text-sm text-red-600">{errors.email[0]}</p>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Sección Información de la Cita */}
                    <div className="border-b pb-6">
                        <h2 className="text-xl font-semibold text-gray-800 mb-4">
                            Información de la Cita
                        </h2>

                        {/* Fecha y Hora */}
                        <div className="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha Deseada *
                                </label>
                                <input
                                    type="date"
                                    name="fecha_deseada"
                                    value={formData.fecha_deseada}
                                    onChange={handleChange}
                                    min={new Date().toISOString().split('T')[0]}
                                    className={`w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
                                        errors.fecha_deseada ? 'border-red-500' : 'border-gray-300'
                                    }`}
                                />
                                {errors.fecha_deseada && (
                                    <p className="mt-1 text-sm text-red-600">{errors.fecha_deseada[0]}</p>
                                )}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Hora Deseada *
                                </label>
                                <input
                                    type="time"
                                    name="hora_deseada"
                                    value={formData.hora_deseada}
                                    onChange={handleChange}
                                    className={`w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
                                        errors.hora_deseada ? 'border-red-500' : 'border-gray-300'
                                    }`}
                                />
                                {errors.hora_deseada && (
                                    <p className="mt-1 text-sm text-red-600">{errors.hora_deseada[0]}</p>
                                )}
                            </div>
                        </div>

                        {/* Sede y Modalidad */}
                        <div className="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Sede (Opcional)
                                </label>
                                <select
                                    name="sede_id"
                                    value={formData.sede_id}
                                    onChange={handleChange}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Seleccione una sede...</option>
                                    {sedes.map((sede) => (
                                        <option key={sede.id} value={sede.id}>
                                            {sede.nombre} - {sede.ciudad}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Modalidad de Atención (Opcional)
                                </label>
                                <select
                                    name="modalidad_id"
                                    value={formData.modalidad_id}
                                    onChange={handleChange}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Seleccione una modalidad...</option>
                                    {modalidades.map((modalidad) => (
                                        <option key={modalidad.id} value={modalidad.id}>
                                            {modalidad.nombre}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Motivo */}
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Motivo de la Cita
                            </label>
                            <input
                                type="text"
                                name="motivo"
                                value={formData.motivo}
                                onChange={handleChange}
                                placeholder="Ej: Chequeo general de salud"
                                className={`w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
                                    errors.motivo ? 'border-red-500' : 'border-gray-300'
                                }`}
                            />
                            {errors.motivo && (
                                <p className="mt-1 text-sm text-red-600">{errors.motivo[0]}</p>
                            )}
                        </div>

                        {/* Observaciones */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones Adicionales
                            </label>
                            <textarea
                                name="observaciones"
                                value={formData.observaciones}
                                onChange={handleChange}
                                placeholder="Información adicional que considere importante..."
                                rows="4"
                                className={`w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none ${
                                    errors.observaciones ? 'border-red-500' : 'border-gray-300'
                                }`}
                            />
                            {errors.observaciones && (
                                <p className="mt-1 text-sm text-red-600">{errors.observaciones[0]}</p>
                            )}
                        </div>
                    </div>

                    {/* Botones */}
                    <div className="flex gap-4 pt-4">
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex-1 bg-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                        >
                            {processing ? 'Registrando...' : 'Registrar Cita'}
                        </button>
                        <button
                            type="button"
                            onClick={() => setFormData({
                                nombres_completos: '',
                                tipo_documento: 'CC',
                                numero_documento: '',
                                telefono_contacto: '',
                                email: '',
                                fecha_deseada: '',
                                hora_deseada: '',
                                sede_id: '',
                                modalidad_id: '',
                                motivo: '',
                                observaciones: '',
                            })}
                            className="flex-1 bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg hover:bg-gray-400 transition duration-200"
                        >
                            Limpiar
                        </button>
                    </div>

                    <p className="text-sm text-gray-500 text-center mt-4">
                        Los campos marcados con * son obligatorios
                    </p>
                </form>
            </div>
        </div>
    );
}

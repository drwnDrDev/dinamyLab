import React, { useState, useEffect } from 'react';
import axios from 'axios';

/**
 * Componente para completar el registro de una persona pre-registrada
 * Se usa en recepción para confirmar y crear el registro formal
 * Integra con FormPersona (que debe recibirse como prop)
 */
const ConfirmarPreRegistro = ({ preRegistro, FormPersona, onSuccess, onCancel }) => {
    const [paso, setPaso] = useState(1); // 1: Verificar datos, 2: Completar formulario
    const [datosIniciales, setDatosIniciales] = useState({});
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    useEffect(() => {
        if (preRegistro) {
            prepararDatosIniciales();
        }
    }, [preRegistro]);

    const prepararDatosIniciales = () => {
        // Preparar datos iniciales para FormPersona basados en pre-registro
        const datos = {
            // Usar datos parseados si existen, sino usar nombres completos
            primer_nombre: preRegistro.datos_parseados?.primer_nombre || '',
            segundo_nombre: preRegistro.datos_parseados?.segundo_nombre || '',
            primer_apellido: preRegistro.datos_parseados?.primer_apellido || '',
            segundo_apellido: preRegistro.datos_parseados?.segundo_apellido || '',

            // Si no hay datos parseados, poner nombres_completos en primer_nombre
            ...((!preRegistro.datos_parseados || !preRegistro.datos_parseados.primer_nombre) && {
                primer_nombre: preRegistro.nombres_completos
            }),

            // Documento si existe
            numero_documento: preRegistro.numero_documento || '',

            // Contacto
            telefonos: preRegistro.telefono_contacto ? [{ numero: preRegistro.telefono_contacto }] : [],
            correos: preRegistro.email ? [{ correo: preRegistro.email }] : []
        };

        setDatosIniciales(datos);
    };

    const handleConfirmarYCompletar = async (datosPersona) => {
        setLoading(true);
        setError(null);

        try {
            const response = await axios.put(
                `/api/recepcion/pre-registros/${preRegistro.id}/confirmar`,
                {
                    datos_persona: datosPersona
                }
            );

            console.log('Confirmación exitosa:', response.data);

            if (onSuccess) {
                onSuccess(response.data);
            }
        } catch (err) {
            console.error('Error al confirmar:', err);
            setError(err.response?.data?.message || 'Error al confirmar registro');
        } finally {
            setLoading(false);
        }
    };

    if (!preRegistro) {
        return (
            <div className="text-center p-8 text-gray-500">
                No hay pre-registro seleccionado
            </div>
        );
    }

    return (
        <div className="max-w-5xl mx-auto">
            {/* Header */}
            <div className="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-t-lg">
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-bold">Confirmar Pre-registro</h2>
                        <p className="text-blue-100 mt-1">
                            Código: {preRegistro.codigo_confirmacion}
                        </p>
                    </div>
                    {onCancel && (
                        <button
                            onClick={onCancel}
                            className="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors"
                        >
                            ✕ Cerrar
                        </button>
                    )}
                </div>
            </div>

            <div className="bg-white rounded-b-lg shadow-lg">
                {/* Navegación de pasos */}
                <div className="border-b border-gray-200 px-6 py-4">
                    <div className="flex items-center justify-center space-x-4">
                        <div className={`flex items-center ${paso >= 1 ? 'text-blue-600' : 'text-gray-400'}`}>
                            <span className={`w-8 h-8 rounded-full flex items-center justify-center font-bold ${paso >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'}`}>
                                1
                            </span>
                            <span className="ml-2 font-semibold">Verificar</span>
                        </div>
                        <div className="w-12 h-1 bg-gray-300"></div>
                        <div className={`flex items-center ${paso >= 2 ? 'text-blue-600' : 'text-gray-400'}`}>
                            <span className={`w-8 h-8 rounded-full flex items-center justify-center font-bold ${paso >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'}`}>
                                2
                            </span>
                            <span className="ml-2 font-semibold">Completar</span>
                        </div>
                    </div>
                </div>

                {/* Contenido según el paso */}
                <div className="p-6">
                    {paso === 1 && (
                        <div className="space-y-6">
                            <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p className="text-yellow-800 font-semibold mb-2">
                                    📋 Verifica los datos con el paciente antes de continuar
                                </p>
                                <p className="text-sm text-yellow-700">
                                    Los datos mostrados fueron proporcionados por el usuario al hacer el pre-registro
                                </p>
                            </div>

                            {/* Datos del pre-registro */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="p-4 bg-gray-50 rounded-lg">
                                    <label className="text-sm font-semibold text-gray-600">Nombre completo</label>
                                    <p className="text-lg font-bold text-gray-900 mt-1">
                                        {preRegistro.nombres_completos}
                                    </p>
                                </div>

                                {preRegistro.numero_documento && (
                                    <div className="p-4 bg-gray-50 rounded-lg">
                                        <label className="text-sm font-semibold text-gray-600">Documento</label>
                                        <p className="text-lg font-bold text-gray-900 mt-1 font-mono">
                                            {preRegistro.numero_documento}
                                        </p>
                                    </div>
                                )}

                                {preRegistro.telefono_contacto && (
                                    <div className="p-4 bg-gray-50 rounded-lg">
                                        <label className="text-sm font-semibold text-gray-600">Teléfono</label>
                                        <p className="text-lg font-bold text-gray-900 mt-1">
                                            {preRegistro.telefono_contacto}
                                        </p>
                                    </div>
                                )}

                                {preRegistro.email && (
                                    <div className="p-4 bg-gray-50 rounded-lg">
                                        <label className="text-sm font-semibold text-gray-600">Email</label>
                                        <p className="text-lg font-bold text-gray-900 mt-1">
                                            {preRegistro.email}
                                        </p>
                                    </div>
                                )}

                                {preRegistro.fecha_deseada && (
                                    <div className="p-4 bg-gray-50 rounded-lg">
                                        <label className="text-sm font-semibold text-gray-600">Fecha deseada</label>
                                        <p className="text-lg font-bold text-gray-900 mt-1">
                                            {new Date(preRegistro.fecha_deseada).toLocaleDateString('es-ES')}
                                            {preRegistro.hora_deseada && ` - ${preRegistro.hora_deseada}`}
                                        </p>
                                    </div>
                                )}

                                {preRegistro.motivo && (
                                    <div className="p-4 bg-gray-50 rounded-lg md:col-span-2">
                                        <label className="text-sm font-semibold text-gray-600">Motivo</label>
                                        <p className="text-gray-900 mt-1">
                                            {preRegistro.motivo}
                                        </p>
                                    </div>
                                )}
                            </div>

                            {/* Datos parseados (info adicional) */}
                            {preRegistro.datos_parseados && (
                                <div className="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p className="text-blue-800 font-semibold mb-3">
                                        📝 Sugerencia de nombres (análisis automático):
                                    </p>
                                    <div className="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                        {preRegistro.datos_parseados.primer_nombre && (
                                            <div>
                                                <span className="text-blue-600">1er Nombre:</span>
                                                <p className="font-semibold">{preRegistro.datos_parseados.primer_nombre}</p>
                                            </div>
                                        )}
                                        {preRegistro.datos_parseados.segundo_nombre && (
                                            <div>
                                                <span className="text-blue-600">2do Nombre:</span>
                                                <p className="font-semibold">{preRegistro.datos_parseados.segundo_nombre}</p>
                                            </div>
                                        )}
                                        {preRegistro.datos_parseados.primer_apellido && (
                                            <div>
                                                <span className="text-blue-600">1er Apellido:</span>
                                                <p className="font-semibold">{preRegistro.datos_parseados.primer_apellido}</p>
                                            </div>
                                        )}
                                        {preRegistro.datos_parseados.segundo_apellido && (
                                            <div>
                                                <span className="text-blue-600">2do Apellido:</span>
                                                <p className="font-semibold">{preRegistro.datos_parseados.segundo_apellido}</p>
                                            </div>
                                        )}
                                    </div>
                                    <p className="text-xs text-blue-600 mt-2">
                                        Estos datos se pre-cargarán en el formulario, puedes ajustarlos según corresponda
                                    </p>
                                </div>
                            )}

                            {/* Botón continuar */}
                            <div className="flex justify-end gap-3 pt-4 border-t">
                                {onCancel && (
                                    <button
                                        onClick={onCancel}
                                        className="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300"
                                    >
                                        Cancelar
                                    </button>
                                )}
                                <button
                                    onClick={() => setPaso(2)}
                                    className="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors"
                                >
                                    Continuar al formulario →
                                </button>
                            </div>
                        </div>
                    )}

                    {paso === 2 && (
                        <div className="space-y-6">
                            <div className="bg-green-50 border border-green-200 rounded-lg p-4">
                                <p className="text-green-800 font-semibold mb-2">
                                    ✏️ Completa el registro formal de la persona
                                </p>
                                <p className="text-sm text-green-700">
                                    Los campos se han pre-cargado con la información disponible.
                                    Completa o corrige los datos faltantes con ayuda del paciente.
                                </p>
                            </div>

                            {error && (
                                <div className="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                                    {error}
                                </div>
                            )}

                            {/* Integración con FormPersona */}
                            {FormPersona ? (
                                <div className="border border-gray-200 rounded-lg p-4">
                                    <FormPersona
                                        datosIniciales={datosIniciales}
                                        onSubmit={handleConfirmarYCompletar}
                                        textoBoton="✅ Confirmar y guardar registro"
                                        loading={loading}
                                    />
                                </div>
                            ) : (
                                <div className="p-8 text-center text-red-600 bg-red-50 rounded-lg">
                                    <p className="font-semibold">Error: Componente FormPersona no disponible</p>
                                    <p className="text-sm mt-2">
                                        Debes pasar el componente FormPersona como prop para poder completar el registro
                                    </p>
                                </div>
                            )}

                            {/* Botones de navegación */}
                            <div className="flex justify-between pt-4 border-t">
                                <button
                                    onClick={() => setPaso(1)}
                                    disabled={loading}
                                    className="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 disabled:opacity-50"
                                >
                                    ← Volver
                                </button>

                                {onCancel && (
                                    <button
                                        onClick={onCancel}
                                        disabled={loading}
                                        className="px-6 py-3 bg-red-100 text-red-700 font-semibold rounded-lg hover:bg-red-200 disabled:opacity-50"
                                    >
                                        Cancelar proceso
                                    </button>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ConfirmarPreRegistro;

/**
 * EJEMPLO DE INTEGRACIÓN DEL MÓDULO CARGADOR DE LISTA DE PERSONAS
 *
 * Este archivo muestra cómo integrar el CargadorListaPersonas
 * en el CrearOrdenComponent para permitir cargar múltiples pacientes
 * desde una lista.
 *
 * COPIAR Y ADAPTAR EN CrearOrdenComponent.jsx
 */

import React, { useState, useEffect } from 'react';
import FormPersona from './FormPersona';
import DatosExamenes from './DatosExamenes';
import CompletedCheck from './CompletedCheck';
import DatosPersona from './DatosPersona';
import DatosOrden from './DatosOrden';
import CargadorListaPersonas from './CargadorListaPersonas'; // ← NUEVO
import { useListaPersonas } from './hooks/useListaPersonas'; // ← NUEVO
import useAxiosAuth from './hooks/useAxiosAuth';
import ordenDataDefault from './ordenDataDefault';
import { useOrderValidation } from './hooks/useOrdenValidation';

const CrearOrdenComponentMejorado = (paciente, { dataDefoult = ordenDataDefault } = {}) => {
    const { axiosInstance, csrfLoaded, error: csrfError } = useAxiosAuth();
    const { cargarPersona } = useListaPersonas(); // ← NUEVO
    const [mostrarCargadorLista, setMostrarCargadorLista] = useState(true); // ← NUEVO
    const [modoCreacionManual, setModoCreacionManual] = useState(false); // ← NUEVO

    const initialFormState = {
        numero_orden: '',
        paciente_id: null,
        examenes: [],
        cod_servicio: null,
        via_ingreso: '',
        cie_principal: null,
        cie_relacionado: null,
        finalidad: '',
        modalidad: '',
        abono: 0,
        total: 0,
        fecha_orden: new Date().toISOString().split('T')[0] + ' ' + new Date().toTimeString().slice(0, 5),
    };

    const [persona, setPersona] = useState(paciente.paciente || null);
    const [loading, setLoading] = useState(false);
    const [completeMessage, setCompleteMessage] = useState(false);
    const [error, setError] = useState(null);
    const [formOrden, setFormOrden] = useState(initialFormState);
    const { validateField, validateForm, errors, clearError } = useOrderValidation();

    console.log('datos recibidos de paciente:', paciente);

    useEffect(() => {
        if (!persona) {
            return;
        }
        setFormOrden(prev => {
            const newState = {
                ...prev,
                paciente_id: persona.id || null
            };
            console.log('📝 Estado del formulario actualizado:', newState);
            return newState;
        });
    }, [persona]);

    useEffect(() => {
        if (dataDefoult && Object.keys(dataDefoult).length > 0) {
            const { cod_servicio, via_ingreso, cie_principal, cie_relacionado, finalidad, modalidad } = dataDefoult;
            setFormOrden(prev => ({
                ...prev,
                cod_servicio: cod_servicio || prev.cod_servicio,
                via_ingreso: via_ingreso || prev.via_ingreso,
                cie_principal: cie_principal || prev.cie_principal,
                cie_relacionado: cie_relacionado || prev.cie_relacionado,
                finalidad: finalidad || prev.finalidad,
                modalidad: modalidad || prev.modalidad,
            }));
        }
    }, []);

    // ← NUEVO: Handler para personas del cargador
    const handlePersonaDelCargador = (personaDelCargador) => {
        console.log('✅ Persona seleccionada del cargador:', personaDelCargador);

        const personaFormateada = cargarPersona(personaDelCargador);
        handlePersonaUpdate(personaFormateada);
        setMostrarCargadorLista(false);
    };

    const handlePersonaUpdate = (nuevaPersona) => {
        console.log('🔄 Actualizando persona:', nuevaPersona);
        if (!nuevaPersona) {
            console.error('Error: nuevaPersona es null o undefined');
            return;
        }

        const datosPersona = nuevaPersona?.data || nuevaPersona;

        if (!datosPersona.id) {
            console.error('Error: La persona no tiene ID');
            return;
        }

        setPersona(datosPersona);
    };

    const handleTablasRefUpdate = (e) => {
        const { name, value } = e.target;
        setFormOrden((prev) => ({
            ...prev,
            [name]: value
        }));
        clearError(name);
    }

    const handleComplete = () => {
        setTimeout(() => {
            setCompleteMessage(true);
        }, 3000);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!csrfLoaded) {
            setError('⏳ Autenticación en progreso...');
            return;
        }

        console.log('Validando orden:', formOrden);

        if (!validateForm(formOrden)) {
            console.log('Errores de validación:', errors);
            alert('Por favor complete todos los campos obligatorios');
            return;
        }

        setLoading(true);
        setError(null);

        try {
            if (!formOrden.paciente_id) {
                setError('Por favor, seleccione un paciente.');
                return;
            }
            if (formOrden.examenes.length === 0) {
                setError('Por favor, seleccione al menos un examen.');
                return;
            }

            const response = await axiosInstance.post('/api/ordenes', formOrden);

            setCompleteMessage(true);

            if (response?.data?.data?.id) {
                window.location.href = `/ordenes-medicas/${response.data.data.id}/ver`;
            }

        } catch (err) {
            setError(err.response?.data?.message || 'Error al crear la orden');
        } finally {
            setLoading(false);
        }
    };

    if (error) {
        return <div className="text-red-600">Error: {error}</div>;
    }

    return (
        <>
        {completeMessage && <CompletedCheck />}

        <div className="crear-orden-wrapper relative">
            <div className="header max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8">
                <h1 className="text-2xl font-bold text-titles mb-4">Crear Nueva Orden</h1>
                <label htmlFor="numero_orden" className='text-xl pr-4'>Numero de Orden: </label>
                <input
                    type="number"
                    onChange={handleTablasRefUpdate}
                    name="numero_orden"
                    value={formOrden.numero_orden}
                    className={`h-9 w-32 p-2 border-borders focus:border-primary focus:ring-primary rounded-md ${errors.numero_orden ? 'border-red-500' : ''}`}
                />
                {errors.numero_orden && (
                    <p className="mt-1 text-sm text-red-600">{errors.numero_orden}</p>
                )}
            </div>

            {/* ← NUEVO: Sección del cargador de lista */}
            {mostrarCargadorLista && !persona && !modoCreacionManual && (
                <section className="cargador_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
                    <div className="mb-4 pb-4 border-b border-secondary flex justify-between items-center">
                        <h2 className="text-lg font-semibold text-titles">Opciones para seleccionar paciente</h2>
                        <button
                            type="button"
                            onClick={() => setModoCreacionManual(true)}
                            className="text-sm px-3 py-1 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md"
                        >
                            Crear forma manual →
                        </button>
                    </div>

                    <CargadorListaPersonas
                        onPersonasLoaded={handlePersonaDelCargador}
                        perfil="Paciente"
                    />
                </section>
            )}

            {/* ← NUEVO: Modo creación manual */}
            {modoCreacionManual && !persona && (
                <section className="paciente_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
                    <div className="mb-4 pb-4 border-b border-secondary flex justify-between items-center">
                        <h2 className="text-lg font-semibold text-titles">Crear paciente manualmente</h2>
                        <button
                            type="button"
                            onClick={() => setModoCreacionManual(false)}
                            className="text-sm px-3 py-1 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md"
                        >
                            ← Cargar desde lista
                        </button>
                    </div>

                    <FormPersona
                        persona={null}
                        setPersona={handlePersonaUpdate}
                        perfil="Paciente"
                    />
                </section>
            )}

            {/* Sección paciente seleccionado */}
            {persona && (
                <>
                    <section className="paciente_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
                        <DatosPersona persona={persona} />
                        <div className="mt-4 flex justify-end">
                            <button
                                onClick={() => {
                                    setPersona(null);
                                    setMostrarCargadorLista(true);
                                    setModoCreacionManual(false);
                                }}
                                className="px-4 py-2 text-sm text-gray-600 hover:text-primary"
                            >
                                Cambiar paciente
                            </button>
                        </div>
                    </section>

                    {/* Sección de exámenes */}
                    <section className="examenes_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
                        <DatosExamenes
                            formExamenes={formOrden.examenes}
                            onUpdate={(nuevosExamenes) =>
                                setFormOrden((prev) => ({
                                    ...prev,
                                    examenes: nuevosExamenes,
                                }))
                            }
                            persona={persona}
                            onChangeValores={handleTablasRefUpdate}
                        />
                    </section>

                    {/* Sección de resumen */}
                    {formOrden.examenes.length > 0 && (
                        <section className="resumen_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
                            <DatosOrden
                                formOrden={formOrden}
                                onUpdate={handleTablasRefUpdate}
                                error={errors}
                            />
                        </section>
                    )}
                </>
            )}

            {/* Botón de envío */}
            {persona && (
                <div className="flex justify-end px-4 py-2 max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8">
                    <button
                        type="button"
                        onClick={handleSubmit}
                        disabled={loading}
                        className="inline-flex items-center justify-center rounded-md border border-transparent bg-primary py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-titles focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {loading ? (
                            <>
                                <div className="animate-spin mr-2 h-4 w-4 border-b-2 border-white rounded-full"></div>
                                {loading ? 'Creando...' : 'Guardando...'}
                            </>
                        ) : (
                            'Crear Orden'
                        )}
                    </button>
                </div>
            )}
        </div>
        </>
    );
};

export default CrearOrdenComponentMejorado;

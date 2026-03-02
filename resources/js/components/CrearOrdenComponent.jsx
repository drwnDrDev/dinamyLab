import React, { useState, useEffect } from 'react';
import FormPersona from './FormPersona';
import DatosExamenes from './DatosExamenes';
import CompletedCheck from './CompletedCheck';
import DatosPersona from './DatosPersona';
import DatosOrden from './DatosOrden';
import StepperOrden from './StepperOrden';
import useAxiosAuth from './hooks/useAxiosAuth';
import ordenDataDefault from './ordenDataDefault';
import { useOrderValidation } from './hooks/useOrdenValidation';

const formatCOP = (value) =>
    new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(value);

const CrearOrdenComponent = ({ paciente, dataDefoult = ordenDataDefault }) => {
    const { axiosInstance, csrfLoaded } = useAxiosAuth();
    const [next_numero, setNextNumero] = useState(null);

    useEffect(() => {
        const fetchNextNumero = async () => {
            try {
                const response = await axiosInstance.get('/api/ordenes/max-numero');
                if (response.status === 200) {
                    setNextNumero(response.data.next_numero);
                }
            } catch (error) {
                console.error('Error al obtener el siguiente número de orden:', error);
            }
        };
        fetchNextNumero();
    }, [axiosInstance]);

    const initialFormState = {
        numero_orden: next_numero || '',
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

    const [persona, setPersona] = useState(paciente || null);
    const [loading, setLoading] = useState(false);
    const [completeMessage, setCompleteMessage] = useState(false);
    const [error, setError] = useState(null);
    const [formOrden, setFormOrden] = useState(initialFormState);
    const [currentStep, setCurrentStep] = useState(1);
    const [stepError, setStepError] = useState(null);
    const { validateForm, errors, clearError } = useOrderValidation();

    useEffect(() => {
        if (!persona) return;
        setFormOrden(prev => ({ ...prev, paciente_id: persona.id || null }));
    }, [persona]);

    useEffect(() => {
        if (next_numero !== null) {
            setFormOrden(prev => ({ ...prev, numero_orden: next_numero }));
        }
    }, [next_numero]);

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

    const handlePersonaUpdate = (nuevaPersona) => {
        if (!nuevaPersona) return;
        const datosPersona = nuevaPersona?.data || nuevaPersona;
        if (!datosPersona.id) return;
        setPersona(datosPersona);
        setStepError(null);
    };

    const handleTablasRefUpdate = (e) => {
        const { name, value } = e.target;
        setFormOrden(prev => ({ ...prev, [name]: value }));
        clearError(name);
    };

    const handleValoresUpdate = (name, value) => {
        setFormOrden(prev => ({ ...prev, [name]: value }));
        clearError(name);
    };

    const getStepError = (step) => {
        switch (step) {
            case 1:
                return !persona ? 'Debe seleccionar o registrar un paciente para continuar.' : null;
            case 2:
                return formOrden.examenes.length === 0 ? 'Debe agregar al menos un examen para continuar.' : null;
            case 3: {
                const campos = ['modalidad', 'cod_servicio', 'cie_principal', 'finalidad', 'via_ingreso'];
                const faltantes = campos.filter(c => !formOrden[c]);
                return faltantes.length > 0 ? 'Complete todos los campos obligatorios para continuar.' : null;
            }
            default:
                return null;
        }
    };

    const handleNext = () => {
        const err = getStepError(currentStep);
        if (err) {
            setStepError(err);
            return;
        }
        setStepError(null);
        setCurrentStep(prev => Math.min(prev + 1, 4));
    };

    const handlePrev = () => {
        setStepError(null);
        setCurrentStep(prev => Math.max(prev - 1, 1));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!csrfLoaded) {
            setError('Autenticación en progreso, intente de nuevo.');
            return;
        }

        if (!validateForm(formOrden)) {
            setStepError('Por favor complete todos los campos obligatorios.');
            return;
        }

        setLoading(true);
        setError(null);

        try {
            const response = await axiosInstance.post('/api/ordenes', formOrden);
            setCompleteMessage(true);
            if (response?.data?.data?.id) {
                window.location.href = `/ordenes-medicas/${response.data.data.id}/ver`;
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Error al crear la orden. Intente nuevamente.');
        } finally {
            setLoading(false);
        }
    };

    const stepTitles = {
        1: 'Paciente',
        2: 'Exámenes',
        3: 'Datos de la Orden',
        4: 'Resumen y Confirmación',
    };

    return (
        <>
            {completeMessage && <CompletedCheck />}

            <div className="crear-orden-wrapper max-w-3xl mx-auto px-4 py-6">

                {/* Barra de progreso */}
                <StepperOrden currentStep={currentStep} />

                {/* Tarjeta principal */}
                <div className="bg-background rounded-xl border border-secondary shadow-sm overflow-hidden">

                    {/* Header de la tarjeta */}
                    <div className="px-6 py-4 border-b border-secondary bg-white flex items-center justify-between">
                        <div>
                            <p className="text-xs text-gray-400 uppercase tracking-wide font-medium">
                                Paso {currentStep} de 4
                            </p>
                            <h1 className="text-xl font-bold text-titles mt-0.5">
                                {stepTitles[currentStep]}
                            </h1>
                        </div>
                        {/* Número de orden */}
                        <div className="flex items-center gap-2">
                            <label htmlFor="numero_orden" className="text-sm text-gray-500 font-medium">
                                Orden N°
                            </label>
                            <input
                                type="number"
                                id="numero_orden"
                                onChange={handleTablasRefUpdate}
                                name="numero_orden"
                                value={formOrden.numero_orden}
                                className={`h-9 w-24 px-2 text-sm text-center font-semibold text-titles bg-white border rounded-md focus:outline-none focus:ring-1 focus:border-primary focus:ring-primary transition-colors ${
                                    errors.numero_orden ? 'border-red-400' : 'border-borders'
                                }`}
                            />
                        </div>
                    </div>

                    {/* Contenido del paso */}
                    <div className="px-6 py-5">

                        {/* Banner de error de paso (validación) */}
                        {stepError && (
                            <div className="mb-4 p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-md text-sm flex items-start gap-2">
                                <svg className="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                </svg>
                                {stepError}
                            </div>
                        )}

                        {/* Banner de error de API */}
                        {error && (
                            <div className="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-md text-sm flex items-start gap-2">
                                <svg className="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {error}
                            </div>
                        )}

                        {/* PASO 1: Paciente */}
                        {currentStep === 1 && (
                            persona ? (
                                <>
                                    <DatosPersona persona={persona} />
                                    <div className="mt-4 flex justify-end">
                                        <button
                                            type="button"
                                            onClick={() => setPersona(null)}
                                            className="text-sm text-gray-500 hover:text-titles underline"
                                        >
                                            Cambiar paciente
                                        </button>
                                    </div>
                                </>
                            ) : (
                                <FormPersona
                                    persona={null}
                                    setPersona={handlePersonaUpdate}
                                    perfil="Paciente"
                                />
                            )
                        )}

                        {/* PASO 2: Exámenes */}
                        {currentStep === 2 && (
                            <DatosExamenes
                                formExamenes={formOrden.examenes}
                                onUpdate={(nuevosExamenes) =>
                                    setFormOrden(prev => ({ ...prev, examenes: nuevosExamenes }))
                                }
                                persona={persona}
                                onChangeValores={handleValoresUpdate}
                            />
                        )}

                        {/* PASO 3: Datos de la Orden */}
                        {currentStep === 3 && (
                            <DatosOrden
                                formOrden={formOrden}
                                onUpdate={handleTablasRefUpdate}
                                error={errors}
                            />
                        )}

                        {/* PASO 4: Resumen */}
                        {currentStep === 4 && (
                            <div className="space-y-4">
                                <div className="p-4 bg-white border border-secondary rounded-lg">
                                    <h3 className="text-sm font-semibold text-titles mb-2">Paciente</h3>
                                    <DatosPersona persona={persona} />
                                </div>
                                <div className="p-4 bg-white border border-secondary rounded-lg">
                                    <h3 className="text-sm font-semibold text-titles mb-2">
                                        Exámenes ({formOrden.examenes.length})
                                    </h3>
                                    <ul className="text-sm text-text space-y-1">
                                        {formOrden.examenes.map((ex, i) => (
                                            <li key={i} className="flex justify-between">
                                                <span>{ex.nombre}</span>
                                                <span className="text-gray-500">×{ex.cantidad || 1}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                                <div className="p-4 bg-white border border-secondary rounded-lg">
                                    <h3 className="text-sm font-semibold text-titles mb-2">Total a cobrar</h3>
                                    <p className="text-2xl font-bold text-titles">
                                        {formatCOP(formOrden.total)}
                                    </p>
                                    {formOrden.abono > 0 && (
                                        <p className="text-sm text-gray-500 mt-1">
                                            Abono: {formatCOP(formOrden.abono)}
                                        </p>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Footer de navegación */}
                    <div className="px-6 py-4 border-t border-secondary bg-white flex items-center justify-between">
                        <button
                            type="button"
                            onClick={handlePrev}
                            disabled={currentStep === 1}
                            className="inline-flex items-center gap-2 px-4 py-2 border border-borders text-titles bg-white rounded-md text-sm font-medium hover:bg-secondary transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Anterior
                        </button>

                        {currentStep < 4 ? (
                            <button
                                type="button"
                                onClick={handleNext}
                                className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-titles transition-colors"
                            >
                                Siguiente
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        ) : (
                            <button
                                type="button"
                                onClick={handleSubmit}
                                disabled={loading}
                                className="inline-flex items-center gap-2 px-5 py-2 bg-primary text-white rounded-md text-sm font-semibold hover:bg-titles transition-colors disabled:opacity-50"
                            >
                                {loading ? (
                                    <>
                                        <div className="animate-spin h-4 w-4 border-b-2 border-white rounded-full"></div>
                                        Creando orden...
                                    </>
                                ) : (
                                    <>
                                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Crear Orden
                                    </>
                                )}
                            </button>
                        )}
                    </div>

                </div>
            </div>
        </>
    );
};

export default CrearOrdenComponent;

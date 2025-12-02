import React, { useState, useEffect } from 'react';
import FormPersona from './FormPersona';
import DatosExamenes from './DatosExamenes';
import DatosPersona from './DatosPersona';
import DatosOrden from './DatosOrden';
import useAxiosAuth from './hooks/useAxiosAuth';
import ordenDataDefault from './ordenDataDefault';
import { useOrderValidation } from './hooks/useOrdenValidation';

// Configuración global de Axios para que funcione con las sesiones de Laravel


const CrearOrdenComponent = ({ dataDefoult = ordenDataDefault } = {}) => {
    const { axiosInstance, csrfLoaded, error: csrfError } = useAxiosAuth();
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
        fecha_orden: new Date().toISOString().split('T')[0] + ' ' + new Date().toTimeString().slice(0, 5), // Formato YYYY-MM-DD HH:mm
    };
    const [persona, setPersona] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [formOrden, setFormOrden] = useState(initialFormState);
    const { validateField, validateForm, errors, clearError } = useOrderValidation();

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

    console.log('📝 Estado del formulario después de aplicar datos por defecto:', formOrden);


    // Handler para actualizar la persona
    const handlePersonaUpdate = (nuevaPersona) => {
        console.log('🔄 Actualizando persona:', nuevaPersona);
        // Validamos que nuevaPersona no sea null y tenga los datos necesarios
        if (!nuevaPersona) {
            console.error('Error: nuevaPersona es null o undefined');
            return;
        }

        // Si nuevaPersona tiene la propiedad data, extraemos solo los datos
        const datosPersona = nuevaPersona?.data || nuevaPersona;

        // Validamos que tengamos los datos mínimos necesarios
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

    const handleValoresUpdate = (name, value) => {
        setFormOrden((prev) => ({
            ...prev,
            [name]: value
        }));
        clearError(name);
    }

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
        <div className="crear-orden-wrapper relative">
            <div className="header mb-4 flex justify-between items-center max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8">
                <h1 className="text-2xl font-bold text-titles">Crear Nueva Orden</h1>
                <input
                    type="number"
                    onChange={handleTablasRefUpdate}
                    name="numero_orden"
                    value={formOrden.numero_orden}
                    placeholder="N° de Orden"
                    className= {`h-9 w-32 p-2 border-borders focus:border-primary focus:ring-primary rounded-md ${errors.numero_orden ? 'border-red-500' : ''}`}
                />
                {errors.numero_orden && (
                    <p className="mt-1 text-sm text-red-600">{errors.numero_orden}</p>
                )}

            </div>
            <section className="paciente_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
                {persona ? (
                    <>
                        <DatosPersona persona={persona} />
                        <div className="mt-4 flex justify-end">
                            <button
                                onClick={() => setPersona(null)}
                                className="px-4 py-2 text-sm text-gray-600 hover:text-primary"
                            >
                                Cambiar paciente
                            </button>
                        </div>
                    </>
                ) : (
                    <FormPersona
                        persona={persona}
                        setPersona={handlePersonaUpdate}
                        perfil="Paciente"
                    />
                )}
            </section>
            {persona && (
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
                        onChangeValores={handleValoresUpdate}
                    />
                </section>
            )}
            {formOrden.examenes.length > 0 && (
                <section className="resumen_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
                    <DatosOrden
                        formOrden={formOrden}
                        onUpdate={handleTablasRefUpdate}
                    />
                </section>
            )}

            <div className="flex justify-end space-x-3">
                <button
                    type="button"
                    onClick={handleSubmit}
                    disabled={loading}
                    className="inline-flex items-center justify-center rounded-md border border-transparent bg-primary py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-titles focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    {loading ? (
                        <>
                            <div className="animate-spin mr-2 h-4 w-4 border-b-2 border-white rounded-full"></div>
                            {loading ? 'Actualizando...' : 'Guardando...'}
                        </>
                    ) : (
                        'Nueva Orden'
                    )}
                </button>
            </div>

        </div>
    );
};

export default CrearOrdenComponent;

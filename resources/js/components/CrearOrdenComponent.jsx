
import React, { useState, useEffect } from 'react';
import FormPersona from './FormPersona';
import DatosExamenes from './DatosExamenes';
import DatosPersona from './DatosPersona';
import DatosOrden from './DatosOrden';

const CrearOrdenComponent = () => {
    const initialFormState = {
        numero_orden: '',
        paciente_id: null,
        examenes: [],
        cod_servicio: null,
        via_ingreso: '01',
        cie_principal: null,
        cie_relacionado: null,
        finalidad: '15',
        modalidad: '01',
        fecha_orden: new Date().toISOString().slice(0, 10), // Formato YYYY-MM-DD hh:mm:ss
    };

    const [persona, setPersona] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [formOrden, setFormOrden] = useState(initialFormState);
    useEffect(() => {
        if (!persona) {
            return;
        }

        console.log('🔄 useEffect triggered - Persona actualizada:', persona);

        setFormOrden(prev => {
            const newState = {
                ...prev,
                paciente_id: persona.id || null
            };
            console.log('📝 Estado del formulario actualizado:', newState);
            return newState;
        });
    }, [persona]);

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
    }

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);
        console.log('Enviando formulario de orden:', formOrden);
        try {
            // Validar que se haya seleccionado un paciente
            if (!formOrden.paciente_id) {
                setError('Por favor, seleccione un paciente.');
                setLoading(false);
                return;
            }
            // Validar que se haya seleccionado al menos un examen
            if (formOrden.examenes.length === 0) {
                setError('Por favor, seleccione al menos un examen.');
                setLoading(false);
                return;
            }

            // Enviar los datos al backend
            const response = await fetch('/api/ordenes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: formOrden ? JSON.stringify(formOrden) : null,
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error al crear la orden.');
            }

            const data = await response.json();
            console.log('Orden creada con éxito:', data);
            // Resetear el formulario después de crear la orden
            setFormOrden(initialFormState);
            setPersona(null);
        } catch (err) {
                console.error('Error al crear la orden:', err);
                setError(err.message);
        } finally {
            setLoading(false);
        }




    }


    if (error) {
        return <div className="text-red-600">Error: {error}</div>;
    }

    return (
        <div className="crear-orden-wrapper relative">
            <div className="header mb-4 flex justify-between items-center max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8">
                <h1 className="text-2xl font-bold text-titles">Crear Nueva Orden</h1>
                <input type="number" onChange={handleTablasRefUpdate} name="numero_orden" value={formOrden.numero_orden} placeholder="N° de Orden" className="h-9 w-32 p-2 border-borders focus:border-primary focus:ring-primary
                                rounded-md"
                />

            </div>
            <section className="paciente_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
                {persona ? (
                    <>
                        <DatosPersona persona={persona}/>
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
                            {personaExistente ? 'Actualizando...' : 'Guardando...'}
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

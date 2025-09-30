
import React, { useState, useEffect } from 'react';
import FormPersona from './FormPersona';
import DatosExamenes from './DatosExamenes';
import DatosPersona from './DatosPersona';

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
        causa_externa: '38',
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
        // Si nuevaPersona tiene la propiedad data, extraemos solo los datos
        const datosPersona = nuevaPersona?.data || nuevaPersona;
        setPersona(datosPersona);
    };

    if (error) {
        return <div className="text-red-600">Error: {error}</div>;
    }

    return (
        <div className="crear-orden-wrapper">
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
                    />
                )}
            </section>
            {persona && (
                <section className="paciente_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
                    <DatosExamenes
                        formExamenes={formOrden.examenes}
                        onUpdate={(nuevosExamenes) =>
                            setFormOrden((prev) => ({
                                ...prev,
                                examenes: nuevosExamenes,
                            }))
                        }
                    />
                </section>
            )}

        </div>
    );
};

export default CrearOrdenComponent;

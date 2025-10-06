
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


    if (error) {
        return <div className="text-red-600">Error: {error}</div>;
    }

    return (
        <div className="crear-orden-wrapper relative">
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
                <section className="examenes_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
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
            {formOrden.examenes.length > 0 && (
                <section className="resumen_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
                    <DatosOrden
                        formOrden={formOrden}
                        onUpdate={handleTablasRefUpdate}
                    />
                </section>
            )}


        </div>
    );
};

export default CrearOrdenComponent;

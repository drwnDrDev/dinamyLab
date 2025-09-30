
import React, { useState, useEffect } from 'react';
import DatosPaciente from './DatosPaciente';
import DatosExamenes from './DatosExamenes';

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
        console.log('🔄 useEffect triggered - Persona actualizada:', persona);
        
        setFormOrden(prev => {
            const newState = {
                ...prev,
                paciente_id: persona?.data?.id || null
            };
            console.log('📝 Estado del formulario actualizado:', newState);
            return newState;
        });
    }, [persona]);
    

    

    // Handler para actualizar la persona
    const handlePersonaUpdate = (nuevaPersona) => {
        console.log('🔄 Actualizando persona:', nuevaPersona);
        setPersona(nuevaPersona);
    };

    if (error) {
        return <div className="text-red-600">Error: {error}</div>;
    }

    return (
        <div className="crear-orden-container">
            <DatosPaciente 
                persona={persona} 
                setPersona={handlePersonaUpdate}
            />
            
        </div>
    );
};

export default CrearOrdenComponent;

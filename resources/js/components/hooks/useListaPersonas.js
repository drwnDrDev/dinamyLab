import { useState, useCallback } from 'react';

/**
 * Hook para manejar la carga de lista de personas y la actualización de FormPersona
 */
export const useListaPersonas = () => {
    const [personasCargadas, setPersonasCargadas] = useState([]);
    const [personaActualActual, setPersonaActualActual] = useState(null);

    const cargarPersona = useCallback((persona) => {
        console.log('📥 Cargando persona desde lista:', persona);

        // Formatear los datos para FormPersona
        const personaFormateada = {
            data: {
                id: persona.id || null,
                tipo_documento: persona.tipo_documento || 'CC',
                numero_documento: persona.numero_documento || '',
                primer_nombre: persona.primer_nombre || '',
                segundo_nombre: persona.segundo_nombre || '',
                primer_apellido: persona.primer_apellido || '',
                segundo_apellido: persona.segundo_apellido || '',
                fecha_nacimiento: persona.fecha_nacimiento || '',
                sexo: persona.sexo || '',
                pais_origen: persona.pais_origen || '170',
                telefono: persona.telefono || '',
                zona: persona.zona || '02',
                pais_residencia: persona.pais_residencia || '170',
                correo: persona.correo || '',
                eps: persona.eps || '',
                tipo_afiliacion: persona.tipo_afiliacion || '',
            }
        };

        setPersonaActualActual(persona);
        return personaFormateada;
    }, []);

    const limpiar = useCallback(() => {
        setPersonasCargadas([]);
        setPersonaActualActual(null);
    }, []);

    return {
        personasCargadas,
        setPersonasCargadas,
        personaActualActual,
        cargarPersona,
        limpiar,
    };
};

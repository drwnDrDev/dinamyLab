import { useState, useEffect } from 'react';

const usePersonaById = (personaId) => {
  const [persona, setPersona] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    useEffect(() => {
        if (!personaId) {
            setPersona(null);
            return;
        }
        const fetchPersona = async () => {
            setLoading(true);
            setError(null);
            try {
                const response = await fetch(`/api/personas/${personaId}`);
                if (!response.ok) {
                    throw new Error('Error al obtener la persona');
                }
                const data = await response.json();
                setPersona(data);
            } catch (err) {
                setError(err.message);
                setPersona(null);
            }   finally {
                setLoading(false);
            }
        };
        fetchPersona();
    }, [personaId]);

    return { persona, loading, error };
}
export default usePersonaById;

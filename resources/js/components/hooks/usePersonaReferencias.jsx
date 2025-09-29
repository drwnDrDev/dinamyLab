import { useState, useEffect } from 'react';

export const usePersonaReferencias = () => {
  const [referencias, setReferencias] = useState({
    tiposDocumento: [],
    paises: [],
    municipios: [],
    epsList: [],
  });

  useEffect(() => {
    const cargar = () => {
      try {
        setReferencias({
          tiposDocumento: JSON.parse(localStorage.getItem('documentos_paciente_data')) || [],
          paises: JSON.parse(localStorage.getItem('paises_data')) || [],
          municipios: JSON.parse(localStorage.getItem('municipios_data')) || [],
          epsList: JSON.parse(localStorage.getItem('eps_data')) || [],
        });
      } catch (error) {
        console.error('Error al cargar las referencias:', error);
        // En caso de error, mantenemos los arrays vacíos por defecto
      }
    };
    cargar();
  }, []);

  return referencias;
};
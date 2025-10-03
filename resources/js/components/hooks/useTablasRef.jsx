import { useState, useEffect } from 'react';
import axios from 'axios';

export const useTablasRef = () => {
  const [tablas, setTablas] = useState({
    modalidades: [],
    finalidades: [],
    viasIngreso: [],
    tiposAfiliacion: [],
    servicios: [],
    causaAtencion: []  // Agregado causaAtencion
  });

  useEffect(() => {
    const cargar = async () => {
      const cargarTabla = async (key, endpoint) => {
        let datos = JSON.parse(localStorage.getItem(key)) || [];
        if (datos.length === 0) {
          try {
            const res = await axios.get(endpoint);
            datos = res.data || [];
            localStorage.setItem(key, JSON.stringify(datos));
          } catch (err) {
            console.error(`Error al cargar ${key}`, err);
          }
        }
        return datos;
      };

      const modalidades = await cargarTabla('modalidad_atencion_data', '/api/modalidades-atencion');
      const finalidades = await cargarTabla('finalidad_consulta_data', '/api/finalidades');
      const viasIngreso = await cargarTabla('via_ingreso_data', '/api/via-ingreso');
      const tiposAfiliacion = await cargarTabla('tipos_afiliacion_data', '/api/tipos-afiliacion');
      const servicios = await cargarTabla('servicios_habilitados_data', '/api/servicios-habilitados');
      // const causaAtencion = await cargarTabla('causa_atencion_data', '/api/causas-atencion'); // Si se necesita causaAtencion
      

      setTablas({ modalidades, finalidades, viasIngreso, tiposAfiliacion, servicios, causaAtencion: [] }); // Agregado causaAtencion
    };

    cargar();
  }, []);

  return tablas;
};
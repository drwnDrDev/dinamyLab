import { useState, useEffect } from 'react';
import axios from 'axios';

export const useCieCups = () => {
  const [datos, setDatos] = useState({
    cie10: [],
    cups: []
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

      const cie10 = await cargarTabla('default_cies_data', '/api/cie10');
      const cups = await cargarTabla('cups_data', '/api/cups');

      setDatos({ cie10, cups });
    };

    cargar();
  }, []);

  return datos;
};
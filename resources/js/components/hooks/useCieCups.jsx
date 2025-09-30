import { useState, useEffect } from 'react';
import axios from 'axios';

export const useCieCups = () => {
  const [cie10, setCie10] = useState([]);
  const [cups, setCups] = useState([]);

  useEffect(() => {
    const cargar = async () => {
      const cargarTabla = async (key, endpoint, setter) => {
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
        setter(datos);
      };

      await cargarTabla('default_cies_data', '/api/cie10', setCie10);
      await cargarTabla('cups_data', '/api/cups', setCups);
    };

    cargar();
  }, []);

  return { cie10, cups };
};
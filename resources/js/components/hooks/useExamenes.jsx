import { useState, useEffect } from 'react';
import axios from 'axios';

export const useExamenes = () => {
  const [examenes, setExamenes] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const cargarExamenes = async () => {
      try {
        const res = await axios.get('/api/examenes');
        setExamenes(res.data || []);
      } catch (err) {
        console.error('Error al cargar exámenes', err);
      } finally {
        setLoading(false);
      }
    };

    cargarExamenes();
  }, []);

  return { examenes, loading };
};
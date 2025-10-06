// ExamenesSection.jsx

import React, { useState } from 'react';
import { useExamenes } from './hooks/useExamenes';
import TablaExamenes from './TablaExamenes';
import ModalExamenes from './ModalExamenes';

const DatosExamenes = ({ formExamenes, onUpdate }) => {
  const { examenes, loading } = useExamenes(); // desde LocalStorage
  const disponibles = examenes?.data?.examenes || [];
  const [isModalOpen, setIsModalOpen] = useState(false);

  const handleAgregar = (nuevoExamen) => {
    const yaExiste = formExamenes.some(e => e.cup === nuevoExamen.cup);
    if (!yaExiste) {
      onUpdate([...formExamenes, { ...nuevoExamen, cantidad: 1 }]);
    }
  };
  const handleRemove = (index) => {
    const nuevosExamenes = [...formExamenes];
    nuevosExamenes.splice(index, 1);
    onUpdate(nuevosExamenes);
  }

  const handleCantidadChange = (index, nuevaCantidad) => {
    if (nuevaCantidad < 1) return; // No permitir cantidades menores a 1
    const nuevosExamenes = [...formExamenes];
    nuevosExamenes[index] = {
      ...nuevosExamenes[index],
      cantidad: nuevaCantidad
    };
    onUpdate(nuevosExamenes);
  }

  console.log('Exámenes disponibles, desde localStorage var=disponibles:', disponibles);
  console.log('Exámenes en el formulario, prop formExamenes:', formExamenes);

  return (
    <section>
      <h2>Exámenes</h2>
      <button className='bg-secondary' onClick={() => setIsModalOpen(true)}>Agregar examen</button>
      {formExamenes.length === 0 && <p>No hay exámenes seleccionados.</p>}

      {formExamenes.length > 0 && (
        <TablaExamenes
          examenes={formExamenes}
          onRemove={handleRemove}
          onCantidadChange={handleCantidadChange}
        />
      )}

      {isModalOpen && (
        <ModalExamenes
          disponibles={disponibles}
          examenesActuales={formExamenes}
          onAgregar={handleAgregar}
          onClose={() => setIsModalOpen(false)}
        />
      )}
    </section>
  );
};
export default DatosExamenes;
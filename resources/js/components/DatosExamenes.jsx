// ExamenesSection.jsx

import React, { useState } from 'react';
import { useExamenes } from './hooks/useExamenes';
import TablaExamenes from './TablaExamenes';
import ModalExamenes from './ModalExamenes';

const PlusIcon = () => (
  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
  </svg>
);

const DatosExamenes = ({ formExamenes, onUpdate, persona, onChangeValores }) => {
  const { examenes, loading } = useExamenes();
  const disponibles = examenes?.data || [];
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [sexoPaciente] = useState(persona?.sexo || 'A');

  const handleAgregar = (nuevoExamen) => {
    const yaExiste = formExamenes.some(e => e.id === nuevoExamen.id);
    if (!yaExiste) {
      onUpdate([...formExamenes, { ...nuevoExamen, cantidad: 1 }]);
    }
  };

  const handleRemove = (index) => {
    const nuevosExamenes = [...formExamenes];
    nuevosExamenes.splice(index, 1);
    onUpdate(nuevosExamenes);
  };

  const handleCantidadChange = (index, nuevaCantidad) => {
    if (nuevaCantidad < 1) return;
    const nuevosExamenes = [...formExamenes];
    nuevosExamenes[index] = { ...nuevosExamenes[index], cantidad: nuevaCantidad };
    onUpdate(nuevosExamenes);
  };

  return (
    <section>
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-lg font-semibold text-titles">Exámenes solicitados</h2>
        <button
          type="button"
          className="inline-flex items-center gap-2 px-3 py-1.5 bg-primary text-white text-sm font-medium rounded-md hover:bg-titles transition-colors"
          onClick={() => setIsModalOpen(true)}
        >
          <PlusIcon />
          Agregar examen
        </button>
      </div>

      {formExamenes.length === 0 && (
        <div className="py-8 text-center text-gray-400 border border-dashed border-borders rounded-lg">
          <p className="text-sm">No hay exámenes seleccionados.</p>
          <p className="text-xs mt-1">Usa el botón "Agregar examen" para buscar.</p>
        </div>
      )}

      {formExamenes.length > 0 && (
        <TablaExamenes
          examenes={formExamenes}
          onRemove={handleRemove}
          onCantidadChange={handleCantidadChange}
          onChangeValores={onChangeValores}
        />
      )}

      {isModalOpen && (
        <ModalExamenes
          disponibles={disponibles}
          examenesActuales={formExamenes}
          onAgregar={handleAgregar}
          onClose={() => setIsModalOpen(false)}
          sexoPaciente={sexoPaciente}
        />
      )}
    </section>
  );
};

export default DatosExamenes;

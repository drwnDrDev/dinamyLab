import React from 'react';

const ModalExamenes = ({ disponibles, onAgregar, examenesActuales, onClose }) => {
  const yaAgregados = new Set(examenesActuales.map(e => e.cup));

  console.log('ModalExamenes - Exámenes "disponibles":', disponibles);

  return (
    <div className="modal border border-secondary shadow-lg p-4 bg-white">
      <h3>Seleccionar Exámenes</h3>
      <ul>
        {disponibles.map(ex => (
          <li key={ex.id}>
            {ex.cup} {ex.nombre} {ex.valor}
            <button disabled={yaAgregados.has(ex.cup)} onClick={() => onAgregar(ex)}>
              {yaAgregados.has(ex.cup) ? "Agregado" : "Agregar"}
            </button>
          </li>
        ))}
      </ul>
    </div>
  );
};
export default ModalExamenes;
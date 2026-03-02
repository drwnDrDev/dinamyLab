import React from 'react';
import { useState } from 'react';

const ModalExamenes = ({ disponibles, onAgregar, examenesActuales, onClose, sexoPaciente }) => {
  const yaAgregados = new Set(examenesActuales.map(e => e.id));
  const [disponiblesFiltrados, setDisponiblesFiltrados] = useState(disponibles);

  // Filtrar exámenes según el sexo del paciente
  React.useEffect(() => {
    if (sexoPaciente === 'M') {
      setDisponiblesFiltrados(disponibles.filter(ex => ex.sexo_aplicable !== 'F'));
    } else if (sexoPaciente === 'F') {
      setDisponiblesFiltrados(disponibles.filter(ex => ex.sexo_aplicable !== 'M'));
    } else {
      setDisponiblesFiltrados(disponibles);
    }
  }, [sexoPaciente, disponibles]);

  return (
    <div className="fixed inset-0 flex items-center justify-center z-50">
      <div className="modal border border-secondary shadow-lg p-4 bg-white rounded-lg w-full max-w-2xl">
        <h3 className="text-lg font-semibold mb-3">Seleccionar Exámenes</h3>

        <div className="max-h-[60vh] overflow-y-auto">
          <ul className="divide-y divide-borders">
            {disponiblesFiltrados.map(ex => (
              <li key={ex.id} className="flex items-center justify-between py-1.5 px-2 hover:bg-gray-50">
                <div className="flex items-center gap-2 text-sm min-w-0">
                  <span className="text-gray-400 font-mono text-xs shrink-0">{ex.cup}</span>
                  <span className="text-text truncate">{ex.nombre}</span>
                </div>
                <button
                  disabled={yaAgregados.has(ex.id)}
                  onClick={() => onAgregar(ex)}
                  className={`text-xs px-2.5 py-1 rounded shrink-0 ml-3 ${
                    yaAgregados.has(ex.id)
                      ? 'bg-gray-100 text-gray-400 cursor-default'
                      : 'bg-primary text-white hover:bg-titles'
                  }`}
                >
                  {yaAgregados.has(ex.id) ? 'Agregado' : 'Agregar'}
                </button>
              </li>
            ))}
          </ul>
        </div>

        <div className="mt-3 flex justify-end">
          <button
            onClick={onClose}
            className="px-4 py-2 text-sm text-gray-600 hover:text-gray-800"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  );
};
export default ModalExamenes;

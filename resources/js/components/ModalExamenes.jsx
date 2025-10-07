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

  console.log('ModalExamenes - Exámenes "disponibles":', disponibles);

  return (
    <div className="fixed inset-0 flex items-center justify-center z-50">
      <div className="modal border border-secondary shadow-lg p-4 bg-white rounded-lg w-full max-w-2xl">
        <h3 className="text-xl font-semibold mb-4">Seleccionar Exámenes</h3>
        
        <div className="max-h-[60vh] overflow-y-auto">
          <ul className="space-y-2">
            {disponiblesFiltrados.map(ex => (
              <li key={ex.id} className="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                <div>
                  <span className="mr-2">{ex.cup}</span>
                  <span className="mr-2">{ex.nombre}</span>
                  <span>${ex.valor}</span>
                </div>
                <button 
                  disabled={yaAgregados.has(ex.id)} 
                  onClick={() => onAgregar(ex)}
                  className={`px-3 py-1 rounded ${
                    yaAgregados.has(ex.id) 
                      ? 'bg-gray-200 text-gray-500' 
                      : 'bg-primary text-white'
                  }`}
                >
                  {yaAgregados.has(ex.id) ? "Agregado" : "Agregar"}
                </button>
              </li>
            ))}
          </ul>
        </div>
        
        <div className="mt-4 flex justify-end">
          <button 
            onClick={onClose}
            className="px-4 py-2 text-gray-600 hover:text-gray-800"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  );
};
export default ModalExamenes;
import React from 'react';

const TablaExamenes = ({ examenes, onRemove }) => {
  console.log('TablaExamenes - examenes recibidos:', examenes);

  if (!Array.isArray(examenes)) {
    console.error('TablaExamenes: examenes no es un array');
    return null;
  }

  return (
    <>
      <table className="w-full mt-4">
        <thead className="bg-gray-50">
          <tr>
            <th className="px-4 py-2 text-left">Código</th>
            <th className="px-4 py-2 text-left">Nombre</th>
            <th className="px-4 py-2 text-right">Precio</th>
            <th className="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody>
          {examenes.map((ex, idx) => {
            // Validación de datos
            if (!ex) {
              console.warn(`Examen en índice ${idx} es null o undefined`);
              return null;
            }

            return (
              <tr key={idx} className="border-b hover:bg-gray-50">
                <td className="px-4 py-2">{ex.cup || 'N/A'}</td>
                <td className="px-4 py-2">{ex.nombre || 'Sin nombre'}</td>
                <td className="px-4 py-2 text-right">
                  ${typeof ex.valor === 'number' ? ex.valor.toFixed(2) : ex.valor || '0.00'}
                </td>
                <td className="px-4 py-2 text-center">
                  <button 
                    onClick={() => onRemove(idx)}
                    className="text-red-500 hover:text-red-700"
                  >
                    🗑️
                  </button>
                </td>
              </tr>
            );
          })}
        </tbody>
      </table>
    </>
  );
};
export default TablaExamenes;

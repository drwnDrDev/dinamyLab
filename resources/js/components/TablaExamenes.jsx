import React from 'react';

const TablaExamenes = ({ examenes, onRemove, onCantidadChange }) => {
  console.log('TablaExamenes - examenes recibidos:', examenes);

  if (!Array.isArray(examenes)) {
    console.error('TablaExamenes: examenes no es un array');
    return null;
  }

  // Función para convertir valor a número
  const parseValor = (valor) => {
    // Si es número, retornarlo directamente
    if (typeof valor === 'number') return valor;
    // Si es string, convertirlo a número
    if (typeof valor === 'string') return parseFloat(valor) || 0;
    // Si es undefined o null, retornar 0
    return 0;
  };

  // Calcular el total general
  const total = examenes.reduce((sum, ex) => {
    const cantidad = ex.cantidad || 1;
    const valor = parseValor(ex.valor);
    return sum + (cantidad * valor);
  }, 0);

  return (
    <div className="overflow-x-auto">
      <table className="w-full mt-4">
        <thead className="bg-gray-50">
          <tr>
            <th className="px-4 py-2 text-left">Código</th>
            <th className="px-4 py-2 text-left">Nombre</th>
            <th className="px-4 py-2 text-left">Cantidad</th>
            <th className="px-4 py-2 text-right">Valor U.</th>
            <th className="px-4 py-2 text-right">Subtotal</th>
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
                <td className="p-2">
                  <input 
                    type="number" 
                    min="1"
                    value={ex.cantidad || 1}
                    onChange={(e) => onCantidadChange(idx, parseInt(e.target.value) || 1)}
                    className="w-20 px-2 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-primary"
                  />
                </td>
                <td className="px-4 py-2 text-right">
                  ${parseValor(ex.valor).toFixed(2)}
                </td>
                <td className="px-4 py-2 text-right">
                  ${((ex.cantidad || 1) * parseValor(ex.valor)).toFixed(2)}
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
        <tfoot>
          <tr className="border-t-2 border-gray-200 font-semibold">
            <td colSpan="4" className="px-4 py-3 text-right">Total:</td>
            <td className="px-4 py-3 text-right">${total.toFixed(2)}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
  );
};
export default TablaExamenes;

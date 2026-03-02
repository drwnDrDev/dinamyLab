import React from 'react';
import { useState, useEffect } from 'react';

const TrashIcon = () => (
  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
    <path strokeLinecap="round" strokeLinejoin="round"
      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
  </svg>
);

const formatCOP = (value) =>
  new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(value);

const TablaExamenes = ({ examenes, onRemove, onCantidadChange, onChangeValores }) => {
  const [abono, setAbono] = useState(0);
  const [descuento, setDescuento] = useState(0);
  const [abonoActive, setAbonoActive] = useState(false);
  const [descuentoActive, setDescuentoActive] = useState(false);

  if (!Array.isArray(examenes)) return null;

  const parseValor = (valor) => {
    if (typeof valor === 'number') return valor;
    if (typeof valor === 'string') return parseFloat(valor) || 0;
    return 0;
  };

  const subtotal = examenes.reduce((sum, ex) => {
    return sum + ((ex.cantidad || 1) * parseValor(ex.valor));
  }, 0);

  const descuentoAplicado = descuentoActive ? descuento : 0;
  const total = subtotal - descuentoAplicado;
  const saldo = abonoActive ? total - abono : total;
  const abonoExcede = abonoActive && abono > total;
  const descuentoExcede = descuentoActive && descuento > subtotal;

  useEffect(() => {
    onChangeValores('total', total);
    onChangeValores('abono', abonoActive ? abono : 0);
  }, [total, abono, abonoActive, descuentoActive]);

  const chipClass = (active) =>
    `px-3 py-1.5 text-sm rounded-md border font-medium transition-colors ${
      active
        ? 'bg-primary text-white border-primary'
        : 'bg-white text-titles border-borders hover:bg-secondary'
    }`;

  return (
    <div className="overflow-x-auto">
      <table className="w-full mt-2 text-sm">
        <thead>
          <tr className="bg-secondary text-titles">
            <th className="px-4 py-2.5 text-left font-semibold rounded-tl-md">Código</th>
            <th className="px-4 py-2.5 text-left font-semibold">Nombre</th>
            <th className="px-4 py-2.5 text-center font-semibold w-24">Cantidad</th>
            <th className="px-4 py-2.5 text-right font-semibold">Valor U.</th>
            <th className="px-4 py-2.5 text-right font-semibold">Subtotal</th>
            <th className="px-4 py-2.5 rounded-tr-md w-10"></th>
          </tr>
        </thead>
        <tbody className="divide-y divide-borders">
          {examenes.map((ex, idx) => {
            if (!ex) return null;
            return (
              <tr key={idx} className="hover:bg-background transition-colors">
                <td className="px-4 py-2.5 text-gray-600 font-mono text-xs">{ex.cup || '—'}</td>
                <td className="px-4 py-2.5 text-text">{ex.nombre || 'Sin nombre'}</td>
                <td className="px-2 py-2 text-center">
                  <input
                    type="number"
                    min="1"
                    value={ex.cantidad || 1}
                    onChange={(e) => onCantidadChange(idx, parseInt(e.target.value) || 1)}
                    className="w-16 px-2 py-1 border border-borders rounded-md text-center focus:outline-none focus:ring-1 focus:ring-primary text-sm"
                  />
                </td>
                <td className="px-4 py-2.5 text-right text-gray-600">
                  {formatCOP(parseValor(ex.valor))}
                </td>
                <td className="px-4 py-2.5 text-right font-medium text-text">
                  {formatCOP((ex.cantidad || 1) * parseValor(ex.valor))}
                </td>
                <td className="px-2 py-2 text-center">
                  <button
                    onClick={() => onRemove(idx)}
                    className="text-red-400 hover:text-red-600 transition-colors p-1 rounded hover:bg-red-50"
                    title="Quitar examen"
                  >
                    <TrashIcon />
                  </button>
                </td>
              </tr>
            );
          })}
        </tbody>
        <tfoot>
          {descuentoActive && (
            <tr className="border-t border-borders">
              <td colSpan="4" className="px-4 py-2 text-right text-gray-600">Descuento:</td>
              <td className="px-4 py-2 text-right">
                <input
                  type="number"
                  min="0"
                  placeholder="0"
                  onChange={(e) => setDescuento(parseFloat(e.target.value) || 0)}
                  className={`w-28 px-2 py-1 border rounded-md text-right text-sm focus:outline-none focus:ring-1 ${
                    descuentoExcede ? 'border-red-400 focus:ring-red-400' : 'border-borders focus:ring-primary'
                  }`}
                />
              </td>
              <td></td>
            </tr>
          )}
          <tr className="border-t-2 border-borders font-semibold">
            <td colSpan="4" className="px-4 py-3 text-right text-titles">Total:</td>
            <td className={`px-4 py-3 text-right text-base ${descuentoExcede ? 'text-red-600' : 'text-titles'}`}>
              {formatCOP(total)}
            </td>
            <td></td>
          </tr>
          {abonoActive && (
            <>
              <tr className="border-t border-borders">
                <td colSpan="4" className="px-4 py-2 text-right text-gray-600">
                  <label htmlFor="input-abono" className="cursor-pointer">Abono:</label>
                </td>
                <td className="px-4 py-2 text-right">
                  <input
                    id="input-abono"
                    type="number"
                    min="0"
                    placeholder="0"
                    onChange={(e) => setAbono(parseFloat(e.target.value) || 0)}
                    className={`w-28 px-2 py-1 border rounded-md text-right text-sm focus:outline-none focus:ring-1 ${
                      abonoExcede ? 'border-red-400 focus:ring-red-400' : 'border-borders focus:ring-primary'
                    }`}
                  />
                </td>
                <td></td>
              </tr>
              <tr className="border-t border-borders">
                <td colSpan="4" className="px-4 py-2 text-right text-gray-600">Saldo:</td>
                <td className={`px-4 py-2 text-right font-semibold ${abonoExcede ? 'text-red-600' : 'text-text'}`}>
                  {formatCOP(saldo)}
                </td>
                <td></td>
              </tr>
            </>
          )}
        </tfoot>
      </table>

      {/* Controles de descuento y abono */}
      <div className="flex gap-2 mt-3 pt-3 border-t border-borders">
        <button type="button" onClick={() => setDescuentoActive(!descuentoActive)} className={chipClass(descuentoActive)}>
          Descuento
        </button>
        <button type="button" onClick={() => setAbonoActive(!abonoActive)} className={chipClass(abonoActive)}>
          Abono
        </button>
      </div>
    </div>
  );
};

export default TablaExamenes;

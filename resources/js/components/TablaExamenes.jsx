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
      </table>

      {/* Panel de resumen financiero — separado visualmente de la tabla */}
      <div className="mt-4 flex items-start justify-between gap-6 bg-secondary rounded-lg p-4">

        {/* Columna izquierda: controles */}
        <div className="flex flex-col gap-2">
          <p className="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Ajustes</p>
          <button type="button" onClick={() => setDescuentoActive(!descuentoActive)} className={chipClass(descuentoActive)}>
            Descuento
          </button>
          <button type="button" onClick={() => setAbonoActive(!abonoActive)} className={chipClass(abonoActive)}>
            Abono
          </button>
        </div>

        {/* Columna derecha: resumen de valores */}
        <div className="flex flex-col gap-2 min-w-[220px]">

          {/* Subtotal */}
          <div className="flex items-center justify-between gap-8">
            <span className="text-sm text-gray-500">Subtotal</span>
            <span className="text-sm text-text tabular-nums">{formatCOP(subtotal)}</span>
          </div>

          {/* Descuento (solo si activo) */}
          {descuentoActive && (
            <div className="flex items-center justify-between gap-8">
              <span className="text-sm text-gray-500">Descuento</span>
              <input
                type="number"
                min="0"
                placeholder="0"
                onChange={(e) => setDescuento(parseFloat(e.target.value) || 0)}
                className={`w-28 px-2 py-1 border rounded-md text-right text-sm bg-white focus:outline-none focus:ring-1 ${
                  descuentoExcede ? 'border-red-400 focus:ring-red-400' : 'border-borders focus:ring-primary'
                }`}
              />
            </div>
          )}

          {/* Separador */}
          <div className="border-t border-borders/60 my-0.5" />

          {/* Total — elemento dominante */}
          <div className="flex items-center justify-between gap-8">
            <span className="text-base font-semibold text-titles">Total</span>
            <span className={`text-2xl font-bold tabular-nums ${descuentoExcede ? 'text-red-500' : 'text-titles'}`}>
              {formatCOP(total)}
            </span>
          </div>

          {/* Abono y Saldo (solo si activo) */}
          {abonoActive && (
            <>
              <div className="border-t border-borders/60 my-0.5" />
              <div className="flex items-center justify-between gap-8">
                <label htmlFor="input-abono" className="text-sm text-gray-500 cursor-pointer">Abono</label>
                <input
                  id="input-abono"
                  type="number"
                  min="0"
                  placeholder="0"
                  onChange={(e) => setAbono(parseFloat(e.target.value) || 0)}
                  className={`w-28 px-2 py-1 border rounded-md text-right text-sm bg-white focus:outline-none focus:ring-1 ${
                    abonoExcede ? 'border-red-400 focus:ring-red-400' : 'border-borders focus:ring-primary'
                  }`}
                />
              </div>
              <div className="flex items-center justify-between gap-8">
                <span className="text-sm text-gray-500">Saldo</span>
                <span className={`text-sm font-semibold tabular-nums ${abonoExcede ? 'text-red-500' : 'text-text'}`}>
                  {formatCOP(saldo)}
                </span>
              </div>
            </>
          )}

        </div>
      </div>
    </div>
  );
};

export default TablaExamenes;

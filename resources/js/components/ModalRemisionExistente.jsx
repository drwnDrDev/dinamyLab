import React, { useEffect, useId, useRef } from 'react';
import { createPortal } from 'react-dom';

const formatCOP = value => new Intl.NumberFormat('es-CO', {
    style: 'currency', currency: 'COP', maximumFractionDigits: 2,
}).format(value);

export default function ModalRemisionExistente({ anterior, nueva, guardando, error, onElegir, onClose }) {
    const dialogRef = useRef(null);
    const titleId = useId();
    const descriptionId = useId();

    useEffect(() => {
        const dialog = dialogRef.current;
        dialog.showModal();
        return () => dialog.close();
    }, []);

    return createPortal(
        <dialog ref={dialogRef} aria-labelledby={titleId} aria-describedby={descriptionId}
            onCancel={event => { event.preventDefault(); if (!guardando) onClose(); }}
            onKeyDown={event => event.stopPropagation()}
            className="m-auto max-h-[90vh] w-[calc(100%_-_2rem)] max-w-xl overflow-y-auto rounded-xl border border-borders bg-white p-5 text-text shadow-xl backdrop:bg-black/40">
            <div className="flex items-start justify-between gap-3">
                <h3 id={titleId} className="text-lg font-semibold text-titles">El CUP {nueva.cup} ya existe</h3>
                <button type="button" onClick={onClose} disabled={guardando} aria-label="Cerrar y volver al formulario"
                    className="rounded px-2 text-xl text-gray-500 disabled:opacity-50">×</button>
            </div>
            <p id={descriptionId} className="mt-2 text-sm text-gray-600">
                Elige qué información usar en esta orden. Actualizar también guardará los nuevos datos en el catálogo.
            </p>
            <div className="mt-4 overflow-x-auto rounded-lg border border-borders">
                <table className="w-full text-left text-sm">
                    <thead className="bg-secondary text-titles">
                        <tr><th scope="col" className="p-3">Dato</th><th scope="col" className="p-3">Información anterior</th><th scope="col" className="p-3">Información nueva</th></tr>
                    </thead>
                    <tbody className="divide-y divide-borders">
                        <tr><th scope="row" className="p-3 font-medium">Nombre</th><td className="p-3">{anterior?.nombre ?? 'Sin tarifa anterior'}</td><td className="p-3">{nueva.nombre}</td></tr>
                        <tr><th scope="row" className="p-3 font-medium">Destino</th><td className="p-3">{anterior?.destino ?? 'Sin definir'}</td><td className="p-3">{nueva.destino}</td></tr>
                        <tr><th scope="row" className="p-3 font-medium">Valor</th><td className="p-3">{anterior ? formatCOP(anterior.valor) : 'Sin definir'}</td><td className="p-3">{formatCOP(nueva.valor)}</td></tr>
                    </tbody>
                </table>
            </div>
            <p className="mt-3 text-xs text-gray-500">Las órdenes ya guardadas conservarán su información.</p>
            {error && <p role="alert" className="mt-3 text-sm text-red-600">{error}</p>}
            {guardando && <p role="status" className="mt-3 text-sm text-gray-500">Guardando remisión...</p>}
            <div className="mt-5 flex flex-wrap justify-end gap-2">
                <button type="button" autoFocus disabled={guardando || !anterior} onClick={() => onElegir('conservar')}
                    className="rounded-md border border-borders px-3 py-2 text-sm text-titles hover:bg-secondary disabled:opacity-50">
                    Conservar información anterior
                </button>
                <button type="button" disabled={guardando} onClick={() => onElegir('actualizar')}
                    className="rounded-md bg-primary px-3 py-2 text-sm text-white hover:bg-titles disabled:opacity-50">
                    Actualizar información
                </button>
            </div>
        </dialog>,
        document.body,
    );
}

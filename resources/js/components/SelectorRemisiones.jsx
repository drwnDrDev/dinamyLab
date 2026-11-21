import React, { useEffect, useId, useState } from 'react';
import axios from 'axios';
import { crearRemisionSeleccionada, filtrarRemisiones } from '../lib/remisiones';

const formatCOP = value => new Intl.NumberFormat('es-CO', {
    style: 'currency', currency: 'COP', maximumFractionDigits: 0,
}).format(value);

export default function SelectorRemisiones({ remisiones, onUpdate, onManual, onClose }) {
    const [catalogo, setCatalogo] = useState([]);
    const [cargando, setCargando] = useState(true);
    const [error, setError] = useState('');
    const [intento, setIntento] = useState(0);
    const [busqueda, setBusqueda] = useState('');
    const [listaCompleta, setListaCompleta] = useState(false);
    const [seleccionadas, setSeleccionadas] = useState({});
    const [formulario, setFormulario] = useState(null);
    const listId = useId();

    useEffect(() => {
        const controller = new AbortController();
        setCargando(true);
        setError('');
        axios.get('/api/remisiones', { signal: controller.signal })
            .then(response => setCatalogo(response.data.data))
            .catch(errorCarga => {
                if (!axios.isCancel(errorCarga)) setError('No fue posible cargar las remisiones.');
            })
            .finally(() => { if (!controller.signal.aborted) setCargando(false); });
        return () => controller.abort();
    }, [intento]);

    const disponibles = filtrarRemisiones(catalogo, busqueda);
    const yaAgregada = remision => remisiones.some(item => item.cup === remision.cup);
    const remisionActual = catalogo.find(item => item.cup === formulario?.cup);
    const pendientes = Object.values(seleccionadas).filter(item => !yaAgregada(item));
    const nuevas = listaCompleta ? pendientes : formulario && !yaAgregada(formulario) ? [formulario] : [];
    const preciosValidos = nuevas.every(item => item.valor !== '' && Number.isFinite(Number(item.valor)) && Number(item.valor) >= 0);

    const cambiarBusqueda = valor => {
        setBusqueda(valor);
        const coincidencia = catalogo.find(item => item.cup === valor || `${item.cup} - ${item.nombre}` === valor);
        setFormulario(coincidencia?.valores.length ? crearRemisionSeleccionada(coincidencia, coincidencia.valores[0]) : null);
    };

    const guardar = event => {
        event.preventDefault();
        if (!nuevas.length || !preciosValidos) return;
        onUpdate([...remisiones, ...nuevas]);
        setSeleccionadas({});
        setFormulario(null);
        setBusqueda('');
        onClose();
    };

    return (
        <form onSubmit={guardar} className="mb-5 space-y-4 rounded-lg border border-borders bg-white p-4">
            <div className="flex flex-wrap items-center justify-between gap-2">
                <h3 className="font-semibold text-titles">Catálogo de remisiones</h3>
                <button type="button" className="text-sm text-primary underline" onClick={() => { setListaCompleta(prev => !prev); setBusqueda(''); setFormulario(null); }}>
                    {listaCompleta ? 'Buscar una remisión' : 'Ver lista completa de CUPS'}
                </button>
            </div>
            {cargando && <p role="status" className="text-sm text-gray-500">Cargando remisiones...</p>}
            {error && <div role="alert" className="text-sm text-red-600">{error} <button type="button" className="underline" onClick={() => setIntento(prev => prev + 1)}>Reintentar</button></div>}
            {!cargando && !error && (
                <>
                    <label className="block text-sm text-titles">
                        Buscar por nombre o CUPS
                        <input type="search" list={listaCompleta ? undefined : listId} autoComplete="off"
                            value={busqueda} onChange={event => cambiarBusqueda(event.target.value)}
                            placeholder="Nombre o código CUPS" className="mt-1 w-full rounded-md border border-borders px-3 py-2" />
                    </label>
                    <datalist id={listId}>
                        {catalogo.map(item => <option key={item.id} value={`${item.cup} - ${item.nombre}`} />)}
                    </datalist>
                    {listaCompleta ? (
                        <div className="max-h-96 overflow-auto rounded-md border border-borders">
                            <table className="w-full text-sm">
                                <thead className="sticky top-0 bg-secondary text-titles">
                                    <tr><th className="p-2 text-left">Elegir / CUPS / examen</th><th className="p-2 text-left">IPS / tarifa de lista</th><th className="p-2 text-left">Precio al paciente</th></tr>
                                </thead>
                                <tbody className="divide-y divide-borders">
                                    {disponibles.map(item => {
                                        const agregada = yaAgregada(item);
                                        const seleccion = remisiones.find(remision => remision.cup === item.cup) || seleccionadas[item.id];
                                        const tarifa = item.valores.find(valor => valor.id === seleccion?.valor_remision_id) || item.valores[0];
                                        return (
                                            <tr key={item.id}>
                                                <td className="p-2"><label className="flex items-start gap-2">
                                                    <input type="checkbox" checked={agregada || Boolean(seleccion)} disabled={agregada || !tarifa}
                                                        onChange={event => setSeleccionadas(prev => {
                                                            const siguientes = { ...prev };
                                                            if (event.target.checked) siguientes[item.id] = crearRemisionSeleccionada(item, tarifa);
                                                            else delete siguientes[item.id];
                                                            return siguientes;
                                                        })} />
                                                    <span><span className="font-mono text-xs">{item.cup}</span> — {item.nombre}{agregada && <span className="block text-xs text-gray-500">Agregada a la orden</span>}</span>
                                                </label></td>
                                                <td className="p-2">
                                                    <select aria-label={`IPS para ${item.nombre}`} value={tarifa?.id || ''} disabled={!seleccion || agregada}
                                                        onChange={event => setSeleccionadas(prev => ({ ...prev, [item.id]: crearRemisionSeleccionada(item, item.valores.find(valor => valor.id === Number(event.target.value))) }))}
                                                        className="w-full rounded-md border border-borders text-sm">
                                                        {!tarifa && <option value="">Sin tarifa</option>}
                                                        {item.valores.map(valor => <option key={valor.id} value={valor.id}>{valor.ips_externa.nombre} — {formatCOP(valor.precio)}</option>)}
                                                    </select>
                                                </td>
                                                <td className="p-2"><input type="number" min="0" step="0.01" required={Boolean(seleccion) && !agregada}
                                                    aria-label={`Precio para ${item.nombre}`} disabled={!seleccion || agregada} value={seleccion?.valor ?? tarifa?.precio ?? ''}
                                                    onChange={event => setSeleccionadas(prev => ({ ...prev, [item.id]: { ...prev[item.id], valor: event.target.value } }))}
                                                    className="w-32 rounded-md border border-borders text-sm" /></td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                            {disponibles.length === 0 && <p className="p-3 text-sm text-gray-500">No se encontraron remisiones.</p>}
                        </div>
                    ) : formulario && (
                        <div className="grid gap-3 md:grid-cols-2">
                            <label className="text-sm text-titles">CUPS<input readOnly value={formulario.cup} className="mt-1 w-full rounded-md border border-borders px-3 py-2" /></label>
                            <label className="text-sm text-titles">Nombre<input readOnly value={formulario.nombre} className="mt-1 w-full rounded-md border border-borders px-3 py-2" /></label>
                            <label className="text-sm text-titles">IPS / tarifa de lista
                                <select value={formulario.valor_remision_id} onChange={event => setFormulario(crearRemisionSeleccionada(remisionActual, remisionActual.valores.find(valor => valor.id === Number(event.target.value))))}
                                    className="mt-1 w-full rounded-md border border-borders px-3 py-2">
                                    {remisionActual.valores.map(valor => <option key={valor.id} value={valor.id}>{valor.ips_externa.nombre} — {formatCOP(valor.precio)}</option>)}
                                </select>
                            </label>
                            <label className="text-sm text-titles">Precio al paciente
                                <input required type="number" min="0" step="0.01" value={formulario.valor}
                                    onChange={event => setFormulario(prev => ({ ...prev, valor: event.target.value }))}
                                    className="mt-1 w-full rounded-md border border-borders px-3 py-2" />
                            </label>
                            <p className="text-sm text-gray-500 md:col-span-2">Valor remitido: {formulario.valor_remitido === null ? 'Sin definir' : formatCOP(formulario.valor_remitido)}</p>
                            {yaAgregada(formulario) && <p role="status" className="text-sm text-gray-500 md:col-span-2">Esta remisión ya está agregada a la orden.</p>}
                        </div>
                    )}
                    {catalogo.length === 0 && <p className="text-sm text-gray-500">No hay remisiones en el catálogo.</p>}
                    {!preciosValidos && <p role="alert" className="text-sm text-red-600">Completa los precios de las remisiones seleccionadas con valores mayores o iguales a cero.</p>}
                </>
            )}
            <div className="flex flex-wrap items-center justify-between gap-3">
                <button type="button" onClick={onManual} className="text-sm text-primary underline">Registrar otro CUP</button>
                <div className="flex gap-2">
                    <button type="button" onClick={onClose} className="px-3 py-2 text-sm text-gray-600">Cancelar</button>
                    <button type="submit" disabled={cargando || Boolean(error) || nuevas.length === 0 || !preciosValidos}
                        className="rounded-md bg-primary px-3 py-2 text-sm text-white disabled:opacity-50">
                        {listaCompleta ? `Agregar seleccionadas (${pendientes.length})` : 'Agregar remisión'}
                    </button>
                </div>
            </div>
        </form>
    );
}

import React, { useState } from 'react';
import useAxiosAuth from '../hooks/useAxiosAuth';

const inputClass = 'w-36 rounded-md border border-borders bg-white px-2 py-1.5 text-sm text-text focus:border-primary focus:ring-primary disabled:cursor-not-allowed disabled:bg-gray-100 disabled:opacity-70 dark:bg-slate-800 dark:text-white dark:disabled:bg-slate-700';

const ResultadoInput = ({ muestraId, parametro, value, onChange, disabled }) => {
    const id = `resultado-${muestraId}-${parametro.id}`;
    const opciones = parametro.opciones || [];

    if (parametro.tipo_dato === 'textarea') {
        return (
            <textarea
                id={id}
                rows={2}
                maxLength={255}
                value={value}
                onChange={onChange}
                disabled={disabled}
                className={`${inputClass} min-w-48`}
                aria-label={parametro.nombre}
            />
        );
    }

    if (parametro.tipo_dato === 'select') {
        return (
            <select
                id={id}
                value={value}
                onChange={onChange}
                disabled={disabled}
                className={inputClass}
                aria-label={parametro.nombre}
            >
                <option value="">Seleccione</option>
                {opciones.map(opcion => (
                    <option key={opcion} value={opcion}>{opcion}</option>
                ))}
            </select>
        );
    }

    if (parametro.tipo_dato === 'list') {
        const listId = `${id}-opciones`;
        return (
            <>
                <input
                    id={id}
                    type="text"
                    list={listId}
                    maxLength={255}
                    value={value}
                    onChange={onChange}
                    disabled={disabled}
                    className={inputClass}
                    aria-label={parametro.nombre}
                />
                <datalist id={listId}>
                    {opciones.map(opcion => <option key={opcion} value={opcion} />)}
                </datalist>
            </>
        );
    }

    return (
        <input
            id={id}
            type={parametro.tipo_dato === 'number' || parametro.tipo_dato === 'date' ? parametro.tipo_dato : 'text'}
            step={parametro.tipo_dato === 'number' ? 'any' : undefined}
            maxLength={parametro.tipo_dato === 'number' || parametro.tipo_dato === 'date' ? undefined : 255}
            value={value}
            onChange={onChange}
            disabled={disabled}
            className={inputClass}
            aria-label={parametro.nombre}
        />
    );
};

const ResultadosLote = ({ action, initialMuestras }) => {
    const { axiosInstance, csrfLoaded, error: csrfError } = useAxiosAuth();
    const [muestras, setMuestras] = useState(initialMuestras);
    const [seleccionadas, setSeleccionadas] = useState([]);
    const [resultados, setResultados] = useState(() => Object.fromEntries(
        initialMuestras.map(muestra => [
            muestra.id,
            Object.fromEntries(muestra.parametros.map(parametro => [parametro.id, parametro.default ?? ''])),
        ])
    ));
    const [guardando, setGuardando] = useState(false);
    const [error, setError] = useState('');

    const todosSeleccionados = muestras.length > 0 && seleccionadas.length === muestras.length;
    const parametros = muestras[0]?.parametros ?? [];
    const conResultados = seleccionadas.filter(id => Object.values(resultados[id] ?? {})
        .some(valor => valor !== null && valor !== undefined && String(valor).trim() !== ''));
    const vacias = seleccionadas.length - conResultados.length;

    const cambiarSeleccion = (id, checked) => {
        setSeleccionadas(actuales => checked
            ? [...actuales, id]
            : actuales.filter(muestraId => muestraId !== id));
    };

    const cambiarResultado = (muestraId, parametroId, value) => {
        setResultados(actuales => ({
            ...actuales,
            [muestraId]: {
                ...actuales[muestraId],
                [parametroId]: value,
            },
        }));
    };

    const guardar = async event => {
        event.preventDefault();
        if (conResultados.length === 0 || guardando) return;
        const avisoVacias = vacias > 0 ? ` Las ${vacias} muestras totalmente vacías permanecerán en proceso.` : '';
        if (!window.confirm(`¿Confirma guardar y finalizar las ${conResultados.length} muestras con resultados?${avisoVacias}`)) {
            return;
        }

        setGuardando(true);
        setError('');

        try {
            const response = await axiosInstance.post(action, {
                procedimientos: seleccionadas.map(id => ({
                    id,
                    resultados: resultados[id] ?? {},
                })),
            }, {
                headers: { Accept: 'application/json' },
            });

            if (!response.data.redirect) {
                throw new Error('El servidor no indicó la página para continuar.');
            }

            window.location.assign(response.data.redirect);
        } catch (saveError) {
            const errores = Object.values(saveError.response?.data?.errors ?? {}).flat();
            setError(errores.join(' ') || saveError.response?.data?.message || 'No se pudieron guardar los resultados del lote.');
        } finally {
            setGuardando(false);
        }
    };

    return (
        <section className="space-y-4">
            <header>
                <h2 className="text-xl font-semibold text-titles dark:text-white">Captura de resultados por lote</h2>
                <p className="mt-1 text-sm text-text dark:text-gray-300">
                    Seleccione las muestras de este examen y registre en una sola tabla los valores reportados.
                </p>
            </header>

            <div className="rounded-md border border-amber-300 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-100" role="note">
                Solo se finalizarán las muestras que tengan al menos un resultado. Las muestras totalmente vacías permanecerán en proceso.
            </div>

            {error && <p className="rounded-md bg-red-50 p-3 text-sm text-red-800 dark:bg-red-950 dark:text-red-200" role="alert">{error}</p>}
            {csrfError && !csrfLoaded && <p className="text-sm text-red-700" role="alert">No se pudo validar la sesión. Recargue la página e intente de nuevo.</p>}

            {muestras.length === 0 ? (
                <div className="rounded-md border border-borders bg-background p-6 text-center text-text dark:text-gray-300">
                    No hay muestras en proceso para este examen en la sede seleccionada.
                </div>
            ) : (
                <form onSubmit={guardar} className="space-y-4">
                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <label className="inline-flex items-center gap-2 text-sm font-medium text-text dark:text-gray-200">
                            <input
                                type="checkbox"
                                checked={todosSeleccionados}
                                disabled={guardando}
                                onChange={event => setSeleccionadas(event.target.checked ? muestras.map(muestra => muestra.id) : [])}
                                className="rounded border-borders text-primary focus:ring-primary"
                            />
                            Seleccionar todas las muestras de esta página
                        </label>
                        <span className="text-sm text-text dark:text-gray-300">
                            {seleccionadas.length} de {muestras.length} seleccionadas · {conResultados.length} con resultados
                        </span>
                    </div>

                    <div className="overflow-x-auto rounded-lg border border-borders">
                        <table className="w-full border-collapse text-sm">
                            <thead className="bg-secondary text-titles dark:bg-slate-800 dark:text-white">
                                <tr>
                                    <th className="sticky left-0 z-10 min-w-64 border-b border-borders bg-secondary px-3 py-3 text-left dark:bg-slate-800">
                                        Muestra / paciente
                                    </th>
                                    {parametros.map(parametro => (
                                        <th key={parametro.id} className="min-w-40 border-b border-borders px-3 py-3 text-left">
                                            {parametro.grupo && <span className="block text-xs font-normal text-text dark:text-gray-300">{parametro.grupo}</span>}
                                            <span>{parametro.nombre}</span>
                                            {parametro.unidades && <span className="block text-xs font-normal text-text dark:text-gray-300">{parametro.unidades}</span>}
                                        </th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {muestras.map(muestra => {
                                    const seleccionada = seleccionadas.includes(muestra.id);

                                    return (
                                        <tr key={muestra.id} className="border-b border-borders align-top last:border-b-0 hover:bg-secondary/50 dark:text-gray-100">
                                            <th scope="row" className="sticky left-0 z-10 min-w-64 bg-background px-3 py-3 text-left dark:bg-slate-900">
                                                <label className="flex items-start gap-2">
                                                    <input
                                                        type="checkbox"
                                                        checked={seleccionada}
                                                        disabled={guardando}
                                                        onChange={event => cambiarSeleccion(muestra.id, event.target.checked)}
                                                        className="mt-1 rounded border-borders text-primary focus:ring-primary"
                                                    />
                                                    <span>
                                                        <span className="block font-semibold">{muestra.paciente}</span>
                                                        <span className="block text-xs text-text dark:text-gray-300">{muestra.documento}</span>
                                                        <span className="block text-xs text-text dark:text-gray-300">Orden: {muestra.orden}</span>
                                                        <span className="block text-xs text-text dark:text-gray-300">{muestra.fecha}</span>
                                                    </span>
                                                </label>
                                                <a href={muestra.url_anulacion} target="_blank" rel="noopener noreferrer"
                                                    className="ml-6 mt-2 inline-block text-xs font-semibold text-primary hover:underline dark:text-cyan-300">
                                                    {muestra.anulacion_pendiente ? 'Ver solicitud de anulación' : 'Solicitar anulación'}
                                                    <span className="sr-only"> (abre otra pestaña)</span>
                                                </a>
                                            </th>
                                            {parametros.map(parametro => {
                                                const datosParametro = muestra.parametros.find(item => item.id === parametro.id) ?? parametro;
                                                const value = resultados[muestra.id]?.[parametro.id] ?? '';

                                                return (
                                                    <td key={parametro.id} className="px-3 py-3">
                                                        <ResultadoInput
                                                            muestraId={muestra.id}
                                                            parametro={datosParametro}
                                                            value={value}
                                                            onChange={event => cambiarResultado(muestra.id, parametro.id, event.target.value)}
                                                            disabled={!seleccionada || guardando}
                                                        />
                                                        {datosParametro.referencia && (
                                                            <span className="mt-1 block max-w-48 text-xs text-text dark:text-gray-300">
                                                                Ref: {datosParametro.referencia}
                                                            </span>
                                                        )}
                                                    </td>
                                                );
                                            })}
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>

                    <div className="flex justify-end">
                        <button
                            type="submit"
                            disabled={conResultados.length === 0 || guardando || !csrfLoaded}
                            className="rounded-md bg-primary px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-titles disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {guardando ? 'Guardando lote...' : `Guardar resultados (${conResultados.length})`}
                        </button>
                    </div>
                </form>
            )}
        </section>
    );
};

export default ResultadosLote;

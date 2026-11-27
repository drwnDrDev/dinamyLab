// ExamenesSection.jsx

import React, { useEffect, useRef, useState } from 'react';
import axios from 'axios';
import { useExamenes } from './hooks/useExamenes';
import TablaExamenes from './TablaExamenes';
import ModalExamenes from './ModalExamenes';
import SelectorRemisiones from './SelectorRemisiones';
import ModalRemisionExistente from './ModalRemisionExistente';
import { toast } from '../lib/toast';

const formatCOP = (value) =>
  new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(value);

const PlusIcon = () => (
  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
  </svg>
);

const DatosExamenes = ({
  formExamenes,
  onUpdate,
  remisiones,
  onRemisionesUpdate,
  persona,
  onChangeValores,
  onGuardandoRemisionChange,
}) => {
  const { examenes, loading } = useExamenes();
  const disponibles = examenes?.data || [];
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [sexoPaciente] = useState(persona?.sexo || 'A');
  const [mostrarCatalogoRemisiones, setMostrarCatalogoRemisiones] = useState(false);
  const [mostrarFormularioRemision, setMostrarFormularioRemision] = useState(false);
  const [guardandoRemision, setGuardandoRemision] = useState(false);
  const guardandoRemisionRef = useRef(false);
  const [errorRemision, setErrorRemision] = useState('');
  const [conflictoRemision, setConflictoRemision] = useState(null);
  const [busquedaCup, setBusquedaCup] = useState('');
  const [sugerenciasCup, setSugerenciasCup] = useState([]);
  const [cargandoSugerenciasCup, setCargandoSugerenciasCup] = useState(false);
  const [errorSugerenciasCup, setErrorSugerenciasCup] = useState('');
  const [mostrarSugerenciasCup, setMostrarSugerenciasCup] = useState(false);
  const [remisionForm, setRemisionForm] = useState({
    cup: '',
    nombre: '',
    valor: '',
    destinoExterno: '',
  });

  useEffect(() => {
    const termino = busquedaCup.trim();
    if (termino.length < 2) {
      setSugerenciasCup([]);
      setCargandoSugerenciasCup(false);
      setErrorSugerenciasCup('');
      return undefined;
    }

    const controller = new AbortController();
    setCargandoSugerenciasCup(true);
    setErrorSugerenciasCup('');
    const timer = setTimeout(() => {
      axios.get('/api/cups/buscar', {
        params: { query: termino },
        signal: controller.signal,
      })
        .then(response => setSugerenciasCup(response.data?.data || []))
        .catch(error => {
          if (!axios.isCancel(error)) {
            console.error('Error al buscar procedimientos CUP', error);
            setErrorSugerenciasCup('No fue posible buscar procedimientos. Intenta de nuevo.');
          }
        })
        .finally(() => {
          if (!controller.signal.aborted) setCargandoSugerenciasCup(false);
        });
    }, 300);

    return () => {
      clearTimeout(timer);
      controller.abort();
    };
  }, [busquedaCup]);

  const handleAgregar = (nuevoExamen) => {
    const yaExiste = formExamenes.some(e => e.id === nuevoExamen.id);
    if (!yaExiste) {
      onUpdate([...formExamenes, { ...nuevoExamen, cantidad: 1 }]);
    }
  };

  const handleAgregarGrupo = (examenesGrupo) => {
    const idsAgregados = new Set(formExamenes.map(examen => examen.id));
    const nuevosExamenes = examenesGrupo
      .filter(examen => !idsAgregados.has(examen.id))
      .map(examen => ({ ...examen, cantidad: 1 }));

    if (nuevosExamenes.length > 0) {
      onUpdate([...formExamenes, ...nuevosExamenes]);
    }
  };

  const handleRemove = (index) => {
    const nuevosExamenes = [...formExamenes];
    nuevosExamenes.splice(index, 1);
    onUpdate(nuevosExamenes);
  };

  const handleCantidadChange = (index, nuevaCantidad) => {
    if (nuevaCantidad < 1) return;
    const nuevosExamenes = [...formExamenes];
    nuevosExamenes[index] = { ...nuevosExamenes[index], cantidad: nuevaCantidad };
    onUpdate(nuevosExamenes);
  };

  const guardarRemision = async (datos, accion) => {
    if (guardandoRemisionRef.current) return;
    guardandoRemisionRef.current = true;
    setGuardandoRemision(true);
    onGuardandoRemisionChange?.(true);
    setErrorRemision('');

    try {
      const response = await axios.post('/api/remisiones', { ...datos, accion });
      const remision = response.data.seleccion;
      const yaAgregada = remisiones.some(item => item.cup === remision.cup);
      onRemisionesUpdate(yaAgregada
        ? remisiones.map(item => item.cup === remision.cup ? remision : item)
        : [...remisiones, remision]);
      setRemisionForm({ cup: '', nombre: '', valor: '', destinoExterno: '' });
      setBusquedaCup('');
      setMostrarSugerenciasCup(false);
      setMostrarFormularioRemision(false);
      setConflictoRemision(null);
      toast.success(accion === 'conservar'
        ? 'Remisión agregada con la información anterior.'
        : 'Remisión guardada en el catálogo y agregada a la orden.');
    } catch (error) {
      if (error.response?.status === 409) {
        setConflictoRemision({ nueva: datos, anterior: error.response.data.seleccion });
      } else {
        const mensajes = Object.values(error.response?.data?.errors || {}).flat();
        setErrorRemision(mensajes.join(' ') || error.response?.data?.message || 'No fue posible guardar la remisión. Intenta de nuevo.');
      }
    } finally {
      guardandoRemisionRef.current = false;
      setGuardandoRemision(false);
      onGuardandoRemisionChange?.(false);
    }
  };

  const handleAgregarRemision = (event) => {
    event.preventDefault();
    guardarRemision({
      cup: remisionForm.cup.trim(),
      nombre: remisionForm.nombre.trim(),
      valor: Number(remisionForm.valor),
      destino: remisionForm.destinoExterno.trim(),
    });
  };

  const handleEliminarRemision = (index) => {
    onRemisionesUpdate(remisiones.filter((_, itemIndex) => itemIndex !== index));
  };

  const handleSeleccionarCup = (cup) => {
    setRemisionForm(prev => ({ ...prev, cup: cup.codigo, nombre: cup.nombre }));
    setBusquedaCup(`${cup.codigo} - ${cup.nombre}`);
    setMostrarSugerenciasCup(false);
  };

  return (
    <section>
      <div className="flex flex-wrap items-center justify-between gap-2 mb-4">
        <h2 className="text-lg font-semibold text-titles">Exámenes y remisiones</h2>
        <div className="flex flex-wrap gap-2">
          <button
            type="button"
            disabled={guardandoRemision}
            className="inline-flex items-center gap-2 px-3 py-1.5 bg-primary text-white text-sm font-medium rounded-md hover:bg-titles transition-colors"
            onClick={() => setIsModalOpen(true)}
          >
            <PlusIcon />
            Agregar examen
          </button>
          <button
            type="button"
            disabled={guardandoRemision}
            className="inline-flex items-center gap-2 px-3 py-1.5 border border-primary text-primary text-sm font-medium rounded-md hover:bg-secondary transition-colors"
            onClick={() => { setMostrarCatalogoRemisiones(prev => !prev); setMostrarFormularioRemision(false); setErrorRemision(''); }}
          >
            <PlusIcon />
            Agregar remisión
          </button>
        </div>
      </div>

      {formExamenes.length === 0 && remisiones.length === 0 && (
        <div className="py-8 text-center text-gray-400 border border-dashed border-borders rounded-lg">
          <p className="text-sm">Aún no hay exámenes ni remisiones.</p>
          <p className="text-xs mt-1">Agrega un examen o registra una remisión.</p>
        </div>
      )}

      {mostrarCatalogoRemisiones && (
        <SelectorRemisiones remisiones={remisiones} onUpdate={onRemisionesUpdate}
          onClose={() => setMostrarCatalogoRemisiones(false)}
          onManual={() => { setMostrarCatalogoRemisiones(false); setMostrarFormularioRemision(true); setErrorRemision(''); }} />
      )}

      {mostrarFormularioRemision && (
        <form
          onSubmit={handleAgregarRemision}
          className="mb-5"
        >
          <fieldset disabled={guardandoRemision} className="grid min-w-0 grid-cols-1 md:grid-cols-2 gap-3 p-4 border border-secondary rounded-lg bg-white">
            <p className="text-sm text-gray-500 md:col-span-2">La remisión se guardará en el catálogo para reutilizarla en otras órdenes.</p>
            <div className="relative text-sm text-titles md:col-span-2">
              <label htmlFor="buscar-cup-remision">Buscar procedimiento por nombre o código CUP</label>
              <input
                id="buscar-cup-remision"
                type="search"
                autoComplete="off"
                value={busquedaCup}
                onChange={event => {
                  setBusquedaCup(event.target.value);
                  setSugerenciasCup([]);
                  setMostrarSugerenciasCup(true);
                }}
                onFocus={() => setMostrarSugerenciasCup(true)}
                className="mt-1 w-full border border-borders rounded-md px-3 py-2"
                placeholder="Escribe el nombre o código CUP"
                aria-autocomplete="list"
                aria-expanded={mostrarSugerenciasCup && sugerenciasCup.length > 0}
              />
              {mostrarSugerenciasCup && busquedaCup.trim().length >= 2 && (
                <div className="absolute z-10 mt-1 w-full overflow-hidden rounded-md border border-borders bg-white shadow-lg">
                  {cargandoSugerenciasCup && (
                    <p className="px-3 py-2 text-gray-500" role="status">Buscando procedimientos...</p>
                  )}
                  {!cargandoSugerenciasCup && errorSugerenciasCup && (
                    <p className="px-3 py-2 text-red-600" role="alert">{errorSugerenciasCup}</p>
                  )}
                  {!cargandoSugerenciasCup && !errorSugerenciasCup && sugerenciasCup.length === 0 && (
                    <p className="px-3 py-2 text-gray-500">No se encontraron procedimientos.</p>
                  )}
                  {sugerenciasCup.map(cup => (
                    <button
                      key={cup.codigo}
                      type="button"
                      onMouseDown={event => event.preventDefault()}
                      onClick={() => handleSeleccionarCup(cup)}
                      className="flex w-full items-center gap-3 px-3 py-2 text-left hover:bg-secondary"
                    >
                      <span className="shrink-0 font-mono text-xs">{cup.codigo}</span>
                      <span>{cup.nombre}</span>
                    </button>
                  ))}
                </div>
              )}
            </div>
            <label className="text-sm text-titles">
              CUP
              <input
                required
                maxLength={20}
                value={remisionForm.cup}
                onChange={event => setRemisionForm(prev => ({ ...prev, cup: event.target.value }))}
                className="mt-1 w-full border border-borders rounded-md px-3 py-2"
              />
            </label>
            <label className="text-sm text-titles">
              Nombre del examen o procedimiento
              <input
                required
                maxLength={255}
                value={remisionForm.nombre}
                onChange={event => setRemisionForm(prev => ({ ...prev, nombre: event.target.value }))}
                className="mt-1 w-full border border-borders rounded-md px-3 py-2"
              />
            </label>
            <label className="text-sm text-titles">
              Valor
              <input
                required
                type="number"
                min="0"
                max="99999999.99"
                step="0.01"
                value={remisionForm.valor}
                onChange={event => setRemisionForm(prev => ({ ...prev, valor: event.target.value }))}
                className="mt-1 w-full border border-borders rounded-md px-3 py-2"
              />
            </label>
            <label className="text-sm text-titles md:col-span-2">
              Nombre del profesional o IPS externa
              <input required maxLength={255} value={remisionForm.destinoExterno}
                onChange={event => setRemisionForm(prev => ({ ...prev, destinoExterno: event.target.value }))}
                className="mt-1 w-full border border-borders rounded-md px-3 py-2" />
            </label>
            {errorRemision && !conflictoRemision && <p role="alert" className="text-sm text-red-600 md:col-span-2">{errorRemision}</p>}
            <div className="md:col-span-2 flex justify-end gap-2">
              <button
                type="button"
                onClick={() => setMostrarFormularioRemision(false)}
                className="px-3 py-2 text-sm text-gray-600"
              >
                Cancelar
              </button>
              <button
                type="submit"
                className="px-3 py-2 bg-primary text-white text-sm rounded-md hover:bg-titles"
              >
                {guardandoRemision ? 'Guardando remisión...' : 'Guardar remisión'}
              </button>
            </div>
          </fieldset>
        </form>
      )}

      {remisiones.length > 0 && (
        <div className="mb-5 overflow-x-auto rounded-lg border border-borders">
          <table className="w-full text-sm">
            <thead className="bg-secondary text-titles">
              <tr>
                <th className="px-3 py-2 text-left">CUP</th>
                <th className="px-3 py-2 text-left">Examen / procedimiento</th>
                <th className="px-3 py-2 text-left">Destino</th>
                <th className="px-3 py-2 text-right">Valor</th>
                <th className="px-3 py-2"></th>
              </tr>
            </thead>
            <tbody className="divide-y divide-borders">
              {remisiones.map((remision, index) => {
                return (
                  <tr key={`${remision.cup}-${index}`}>
                    <td className="px-3 py-2 font-mono text-xs">{remision.cup}</td>
                    <td className="px-3 py-2">{remision.nombre}</td>
                    <td className="px-3 py-2">{remision.destino}</td>
                    <td className="px-3 py-2 text-right">{formatCOP(remision.valor)}</td>
                    <td className="px-3 py-2 text-right">
                      <button
                        type="button"
                        disabled={guardandoRemision}
                        onClick={() => handleEliminarRemision(index)}
                        className="text-red-500 hover:text-red-700"
                        aria-label={`Eliminar remisión ${remision.nombre}`}
                      >
                        Quitar
                      </button>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      )}

      {formExamenes.length > 0 && (
        <TablaExamenes
          examenes={formExamenes}
          remisiones={remisiones}
          onRemove={handleRemove}
          onCantidadChange={handleCantidadChange}
          onChangeValores={onChangeValores}
        />
      )}
      {formExamenes.length === 0 && remisiones.length > 0 && (
        <TablaExamenes examenes={[]} remisiones={remisiones} onChangeValores={onChangeValores} />
      )}

      {isModalOpen && (
        <ModalExamenes
          disponibles={disponibles}
          examenesActuales={formExamenes}
          onAgregar={handleAgregar}
          onAgregarGrupo={handleAgregarGrupo}
          onClose={() => setIsModalOpen(false)}
          sexoPaciente={sexoPaciente}
        />
      )}

      {conflictoRemision && (
        <ModalRemisionExistente
          anterior={conflictoRemision.anterior}
          nueva={conflictoRemision.nueva}
          guardando={guardandoRemision}
          error={errorRemision}
          onElegir={accion => guardarRemision(conflictoRemision.nueva, accion)}
          onClose={() => { setConflictoRemision(null); setErrorRemision(''); }}
        />
      )}
    </section>
  );
};

export default DatosExamenes;

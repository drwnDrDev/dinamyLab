import React from 'react';
import { useState } from 'react';

const ModalExamenes = ({ disponibles, onAgregar, onAgregarGrupo, examenesActuales, onClose, sexoPaciente }) => {
  const yaAgregados = new Set(examenesActuales.map(e => e.id));
  const [disponiblesFiltrados, setDisponiblesFiltrados] = useState(disponibles);

  // Filtrar exámenes según el sexo del paciente.
  // I/null/A no restringen: A en sexo_aplicable significa "ambos".
  React.useEffect(() => {
    if (sexoPaciente === 'M') {
      setDisponiblesFiltrados(disponibles.filter(ex => ex.sexo_aplicable !== 'F'));
    } else if (sexoPaciente === 'F') {
      setDisponiblesFiltrados(disponibles.filter(ex => ex.sexo_aplicable !== 'M'));
    } else {
      setDisponiblesFiltrados(disponibles);
    }
  }, [sexoPaciente, disponibles]);

  const grupos = new Map();
  disponiblesFiltrados.forEach(examen => {
    if (examen.categorias?.length) {
      examen.categorias.forEach(categoria => {
        if (!grupos.has(categoria.nombre)) grupos.set(categoria.nombre, []);
        grupos.get(categoria.nombre).push(examen);
      });
    } else {
      if (!grupos.has('Otros')) grupos.set('Otros', []);
      grupos.get('Otros').push(examen);
    }
  });

  const renderExamen = examen => (
    <li key={examen.id} className="flex items-center justify-between py-1.5 px-2 hover:bg-gray-50">
      <div className="flex items-center gap-2 text-sm min-w-0">
        <span className="text-gray-400 font-mono text-xs shrink-0">{examen.cup}</span>
        <span className="text-text truncate">{examen.nombre}</span>
      </div>
      <button
        type="button"
        disabled={yaAgregados.has(examen.id)}
        onClick={() => onAgregar(examen)}
        className={`text-xs px-2.5 py-1 rounded shrink-0 ml-3 ${
          yaAgregados.has(examen.id)
            ? 'bg-gray-100 text-gray-400 cursor-default'
            : 'bg-primary text-white hover:bg-titles'
        }`}
      >
        {yaAgregados.has(examen.id) ? 'Agregado' : 'Agregar'}
      </button>
    </li>
  );

  return (
    <div className="fixed inset-0 flex items-center justify-center z-50">
      <div className="modal border border-secondary shadow-lg p-4 bg-white rounded-lg w-full max-w-2xl">
        <h3 className="text-lg font-semibold mb-3">Seleccionar exámenes por grupo</h3>

        <div className="max-h-[60vh] overflow-y-auto">
          {Array.from(grupos, ([nombre, examenesGrupo]) => {
            const pendientes = examenesGrupo.filter(examen => !yaAgregados.has(examen.id));

            return (
              <section key={nombre} className="mb-3 rounded border border-borders">
                <div className="flex items-center justify-between gap-2 bg-secondary px-3 py-2">
                  <h4 className="text-sm font-semibold text-titles">
                    {nombre} <span className="font-normal text-gray-500">({examenesGrupo.length})</span>
                  </h4>
                  <button
                    type="button"
                    disabled={pendientes.length === 0}
                    onClick={() => onAgregarGrupo(pendientes)}
                    className="text-xs font-medium text-primary hover:underline disabled:text-gray-400 disabled:no-underline"
                  >
                    {pendientes.length > 0 ? `Agregar grupo (${pendientes.length})` : 'Grupo agregado'}
                  </button>
                </div>
                <ul className="divide-y divide-borders">
                  {examenesGrupo.map(renderExamen)}
                </ul>
              </section>
            );
          })}
          {disponiblesFiltrados.length === 0 && (
            <p className="py-6 text-center text-sm text-gray-500">No hay exámenes disponibles para este paciente.</p>
          )}
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

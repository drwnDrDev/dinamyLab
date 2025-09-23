import React, { useState, useEffect } from 'react';
import {
  fetchServiciosHabilitados,
  fetchViaIngreso,
  fetchDiagnosticos,
  fetchFinalidades,
  fetchCausasExternas,
  fetchTiposAtencion
} from "../../api";

const Setup = () => {
  // Estado principal
  const [categorias, setCategorias] = useState({
    serviciosHabilitados: { nombre: 'Servicios Habilitados', clases: 'bg-blue-500', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/servicios-habilitados' },
    viasIngreso:         { nombre: 'Vías de Ingreso', clases: 'bg-green-500', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/vias-ingreso' },
    diagnosticos:        { nombre: 'Diagnósticos', clases: 'bg-red-500', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/cie10' },
    finalidades:         { nombre: 'Finalidades', clases: 'bg-yellow-500', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/finalidades' },
    causasExternas:      { nombre: 'Causas Externas', clases: 'bg-purple-500', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/causa-atencion' },
    tiposAtencion:       { nombre: 'Modalidades de Atención', clases: 'bg-yellow-500', datos: [], visibles: [], activos: [], todos: [], activo: false, activeChange: 'api/modalidades-atencion' },
  });

  const [buscador, setBuscador] = useState('');

  // Map de funciones para inicializar
  const fetchers = {
    serviciosHabilitados: fetchServiciosHabilitados,
    viasIngreso: fetchViaIngreso,
    diagnosticos: fetchDiagnosticos,
    finalidades: fetchFinalidades,
    causasExternas: fetchCausasExternas,
    tiposAtencion: fetchTiposAtencion,
  };

  // Inicializar datos
  useEffect(() => {
    const initialize = async () => {
      try {
        const results = await Promise.all(Object.values(fetchers).map(fn => fn()));
        const keys = Object.keys(fetchers);

        setCategorias(prev => {
          const updated = { ...prev };
          keys.forEach((key, i) => {
            updated[key].datos = results[i];
            updated[key].todos = results[i];
            updated[key].visibles = results[i];
            updated[key].activos = results[i].filter(d => d.activo);
          });
          return updated;
        });
      } catch (error) {
        console.error('Error al inicializar los datos:', error);
      }
    };

    initialize();
  }, []);

  // Filtrado genérico
  const filtrar = (item, texto, esServicio) => {
    const buscado = texto.toLowerCase();
    return (
      (item.nombre && item.nombre.toLowerCase().includes(buscado)) ||
      (item.descripcion && item.descripcion.toLowerCase().includes(buscado)) ||
      (esServicio
        ? (item.grupo && item.grupo.toLowerCase().includes(buscado)) || item.codigo == parseInt(buscado)
        : item.codigo && item.codigo.toLowerCase().includes(buscado))
    );
  };

  // Obtener listado
  const obtenerListado = (key) => {
    const data = categorias[key].datos;
    const activos = data.filter(d => d.activo);

    let visibles = activos;
    if (buscador) {
      visibles = data.filter(item => filtrar(item, buscador, key === 'serviciosHabilitados'));
    }

    setCategorias(prev => ({
      ...prev,
      [key]: { ...prev[key], visibles, activos, todos: data }
    }));
  };

  // Toggle botón
  const toggleButtonState = (key) => {
    setCategorias(prev => {
      const updated = {};
      Object.keys(prev).forEach(k => {
        updated[k] = { ...prev[k], activo: k === key ? !prev[k].activo : false };
      });
      return updated;
    });

    if (!categorias[key].activo) {
      obtenerListado(key);
    }
  };

  // Activar/Desactivar ítem
const toggleActivarItem = async (item, key) => {
  const categoria = categorias[key];
  const url = `${window.location.origin}/${categoria.activeChange}/${item.codigo}/activar`;

  try {
    const response = await fetch(url, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json'}
    });

    if (!response.ok) throw new Error('Error en la respuesta del servidor');
    const data = await response.json();
    alert(data.message || `${item.nombre || item.descripcion} actualizado exitosamente.`);

    // ✅ actualizar localmente sin esperar refresh
    setCategorias(prev => {
      const updated = { ...prev };
      updated[key] = {
        ...prev[key],
        datos: prev[key].datos.map(d =>
          d.codigo === item.codigo ? { ...d, activo: !d.activo } : d
        ),
      };
      // recalcular visibles, activos y todos
      const nuevosDatos = updated[key].datos;
      updated[key].todos = nuevosDatos;
      updated[key].activos = nuevosDatos.filter(d => d.activo);

      let visibles = nuevosDatos.filter(d => d.activo);
      if (buscador) {
        visibles = nuevosDatos.filter(i =>
          filtrar(i, buscador, key === 'serviciosHabilitados')
        );
      }
      updated[key].visibles = visibles;

      return updated;
    });
  } catch (error) {
    console.error('Error:', error);
    alert('Ocurrió un error al procesar la solicitud.');
  }
};


  // Identificar la categoría activa
  const activeKey = Object.keys(categorias).find(key => categorias[key].activo);
  const activa = activeKey ? categorias[activeKey] : null;

  return (
    <div className="p-4">
      {/* Botones de categorías */}
      <div className="flex flex-wrap gap-2 mb-4">
        {Object.keys(categorias).map(key => (
          <button
            key={key}
            onClick={() => toggleButtonState(key)}
            className={`px-4 py-2 rounded ${categorias[key].activo ? categorias[key].clases : 'bg-gray-200'} text-white`}
          >
            {categorias[key].nombre}
          </button>
        ))}
      </div>

      {/* Buscador */}
      <input
        type="text"
        placeholder="Buscar..."
        className="w-full p-2 mb-4 border rounded"
        value={buscador}
        onChange={(e) => {
          setBuscador(e.target.value);
          if (activeKey) obtenerListado(activeKey);
        }}
      />

      {/* Estadísticas */}
      {activa && (
        <div className="mb-4">
          <button className="bg-cyan-500 text-white font-bold py-2 px-4 rounded mr-2">
            Activos ({activa.activos.length})
          </button>
          <button className="bg-yellow-500 text-white font-bold py-2 px-4 rounded mr-2">
            Inactivos ({activa.todos.length - activa.activos.length})
          </button>
          <button className="bg-purple-500 text-white font-bold py-2 px-4 rounded mr-2">
            Todos ({activa.todos.length})
          </button>
        </div>
      )}

      {/* Listado */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {activa?.visibles.map((item, index) => (
          <div
            key={index}
            className="border p-2 mb-2 bg-pink-50 dark:bg-gray-800 rounded shadow"
            onClick={() => console.log(item)}
          >
            <h3 className="font-bold col-span-4">{item.nombre || 'Sin nombre'}</h3>
            <p>{item.codigo || 'Sin código'}</p>
            <button
              className={`mt-2 px-4 py-2 rounded ${item.activo ? 'bg-red-500' : 'bg-green-500'} text-white font-bold`}
              onClick={(e) => {
                e.stopPropagation();
                toggleActivarItem(item, activeKey);
              }}
            >
              {item.activo ? 'Desactivar' : 'Activar'}
            </button>
          </div>
        ))}
      </div>
    </div>
  );
};

export default Setup;

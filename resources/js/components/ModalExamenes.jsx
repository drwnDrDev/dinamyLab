const ModalSeleccionExamenes = ({ disponibles, onAgregar, examenesActuales }) => {
  const yaAgregados = new Set(examenesActuales.map(e => e.codigo));

  return (
    <div className="modal">
      <h3>Seleccionar Exámenes</h3>
      <ul>
        {disponibles.map(ex => (
          <li key={ex.codigo}>
            {ex.nombre} (${ex.precio})
            <button disabled={yaAgregados.has(ex.codigo)} onClick={() => onAgregar(ex)}>
              {yaAgregados.has(ex.codigo) ? "Agregado" : "Agregar"}
            </button>
          </li>
        ))}
      </ul>
    </div>
  );
};
export default ModalSeleccionExamenes;
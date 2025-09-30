const ExamenesTable = ({ examenes, onRemove, onCantidadChange }) => {
  const total = examenes.reduce((acc, ex) => acc + ex.precio * ex.cantidad, 0);

  return (
    <>
      <table>
        <thead>
          <tr>
            <th>Código</th><th>Nombre</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th>
          </tr>
        </thead>
        <tbody>
          {examenes.map((ex, idx) => (
            <tr key={ex.codigo}>
              <td>{ex.codigo}</td>
              <td>{ex.nombre}</td>
              <td>${ex.precio.toFixed(2)}</td>
              <td>
                <input type="number" value={ex.cantidad} min={1}
                  onChange={e => onCantidadChange(idx, parseInt(e.target.value))} />
              </td>
              <td>${(ex.precio * ex.cantidad).toFixed(2)}</td>
              <td><button onClick={() => onRemove(idx)}>🗑️</button></td>
            </tr>
          ))}
        </tbody>
      </table>
      <div className="total">Total: ${total.toFixed(2)}</div>
    </>
  );
};
export default ExamenesTable;
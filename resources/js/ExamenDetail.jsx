import axios from "axios";
import React from "react";
import { createRoot } from "react-dom/client";

function ExamenDetail({ examen }) {

    const [ isEditingValor, setIsEditingValor] = React.useState(false);
    const [ nuevoValor, setNuevoValor] = React.useState(examen.valor);

    const examenValorInputToggle = () => {
        axios.post(`/api/examenes/${examen.id}/update`, { valor: nuevoValor })
            .then(response => {
                if (response.data.success) {
                    setNuevoValor(response.data.data.examen.valor);
                } else {
                    alert("Error al actualizar el valor");
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                alert("Ocurrió un error al actualizar el valor");
            });
        setIsEditingValor(!isEditingValor);
    };

    const handleValorChange = (e) => {
        setNuevoValor(e.target.value);
    };
    const ParametroCard = ({ parametro, totalParametros }) => {
        return (
            <div className="border p-4 my-2 rounded shadow-sm">
                <h4 className="text-md font-medium text-gray-800">
                    {parametro.nombre}
                </h4>
                <p className="text-sm text-gray-600">
                    {parametro.descripcion}
                </p>
                <p className="text-sm text-gray-600">
                    Valores de referencia: {parametro.valores_referencia}
                </p>
                {totalParametros > 1 && <hr className="my-2" />}
            </div>
        );
    };

  return (
    <>
    <h2>Exámenes de laboratorio</h2>
      <section>
        <div className="flex items-center justify-center gap-2">
          <div className="flex flex-col w-full md:w-2/5 items-center gap-4">
            <h3 className="text-lg font-light text-gray-700">
              CUP-{examen.cup}
            </h3>
            <h3 className="text-lg font-light text-green-700">
                {isEditingValor ? (
                    <>
                    <input
                        type="text"
                        value={nuevoValor}
                        onChange={handleValorChange}
                        className="border border-gray-300 rounded px-2 py-1 text-sm"
                    />
                    <button
                        onClick={examenValorInputToggle}
                        className="ml-2 px-2 py-1 bg-blue-500 text-white rounded text-sm"
                    >
                        Guardar
                    </button>
                     </>
                ) : (
                 <span> {nuevoValor}</span>
                 )
                }
              <span
              className="ml-2 text-sm text-blue-600 cursor-pointer"
              onClick={setIsEditingValor}
              >edit</span>
            </h3>
          </div>

          <div className="flex flex-col w-full md:w-2/5 items-center gap-4">
            {examen.activo ? (
              <span className="px-2 py-1 bg-green-200 text-green-800 rounded">
                Activo
              </span>
            ) : (
              <span className="px-2 py-1 bg-red-200 text-red-800 rounded">
                Inactivo
              </span>
            )}

            {examen.sexo_aplicable === "M" && (
              <span className="px-2 py-1 bg-blue-200 text-blue-800 rounded">
                Solo Masculino
              </span>
            )}

            {examen.sexo_aplicable === "F" && (
              <span className="px-2 py-1 bg-pink-200 text-pink-800 rounded">
                Solo Femenino
              </span>
            )}

            {(!examen.sexo_aplicable ||
              (examen.sexo_aplicable !== "M" &&
                examen.sexo_aplicable !== "F")) && (
              <span className="px-2 py-1 bg-gray-200 text-gray-800 rounded">
                Ambos Sexos
              </span>
            )}

            {examen.categoria && (
              <span className="px-2 py-1 bg-gray-200 text-gray-800 rounded">
                {examen.categoria.nombre}
              </span>
            )}
          </div>
        </div>

        <p className="max-w-11/12 p-4">
          {examen.descripcion?.charAt(0).toUpperCase() +
            examen.descripcion?.slice(1)}
        </p>
      </section>

      <section>
        {examen.parametros?.map((parametro) => (
          <ParametroCard key={parametro.id} parametro={parametro} totalParametros={examen.parametros.length} />
        ))}
      </section>
    </>
  );
}



export default ExamenDetail;
if (document.getElementById('examen-detail-root')) {
  const element = document.getElementById('examen-detail-root');
  const examen = JSON.parse(element.getAttribute('data-examen')) || null;
  const root = createRoot(element);
  root.render(<ExamenDetail examen={examen} />);
}

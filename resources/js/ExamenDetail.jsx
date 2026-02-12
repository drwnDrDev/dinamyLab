import axios from "axios";
import React from "react";
import { createRoot } from "react-dom/client";

function ExamenDetail({ examen }) {

    const [ isEditingValor, setIsEditingValor] = React.useState(false);
    const [ nuevoValor, setNuevoValor] = React.useState(examen.valor);
    const [ sexoAplicable, setSexoAplicable] = React.useState(examen.sexo_aplicable || null);
    const [ isActve, setIsActive] = React.useState(examen.activo);
    const [ nombreAlternativo, setNombreAlternativo] = React.useState(examen.nombre_alternativo || "");

    const opciones_sexo = ["F", "M", "A"];

    const handleSexoCarrusel = () => {
        const indiceActual = opciones_sexo.indexOf(sexoAplicable);
        const siguienteIndice = (indiceActual + 1) % opciones_sexo.length;
        const nuevoSexo = opciones_sexo[siguienteIndice];
        setSexoAplicable(nuevoSexo);

        // Enviar al servidor
        axios.post(`/api/examenes/${examen.id}/update`, { sexo_aplicable: nuevoSexo })
            .then(response => {
                if (!response.data.success) {
                    alert("Error al actualizar el sexo aplicable");
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                alert("Ocurrió un error al actualizar");
            });
    };


    const obtenerEtiquetaSexo = (sexo) => {
        switch(sexo) {
            case "F":
                return { texto: "Femenino", color: "px-2 py-1 bg-pink-200 text-pink-800 rounded" };
            case "M":
                return { texto: "Masculino", color: "px-2 py-1 bg-blue-200 text-blue-800 rounded" };
            case "A":
                return { texto: "Ambos Sexos", color: "px-2 py-1 bg-gray-200 text-gray-800 rounded" };
            default:
                return { texto: "Ambos Sexos", color: "px-2 py-1 bg-gray-200 text-gray-800 rounded" };
        }
    };

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

    const handleToggleEstado = () => {
        const nuevoEstado = !isActve;
        setIsActive(nuevoEstado);

        axios.post(`/api/examenes/${examen.id}/update`, { activo: nuevoEstado })
            .then(response => {
                if (!response.data.success) {
                    alert("Error al actualizar el estado");
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                alert("Ocurrió un error al actualizar el estado");
            });
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

    const handleNombreAlternativoChange = (e) => {

        

        setNombreAlternativo(e.target.value);
    };

  return (
    <>

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

              <span
              className="px-2 py-1 bg-green-200 text-green-800 rounded">
                {isActve ? "Activo" : "Inactivo"}
                <button
                    onClick={handleToggleEstado}
                    className={`ml-2 px-2 py-1 ${isActve ? "bg-red-500 text-white" : "bg-green-500 text-white"} rounded text-sm`}
                >
                    {isActve ? "Desactivar" : "Activar"}
                </button>
              </span>


            {examen.sexo_aplicable !== undefined && (
              <button
                onClick={handleSexoCarrusel}
                className={`${obtenerEtiquetaSexo(sexoAplicable).color} cursor-pointer hover:opacity-80 transition-opacity`}
              >
                {obtenerEtiquetaSexo(sexoAplicable).texto}
              </button>
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

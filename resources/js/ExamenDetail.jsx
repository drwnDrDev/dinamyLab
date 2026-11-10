import axios from "axios";
import React from "react";
import { createRoot } from "react-dom/client";
import { toast } from "./lib/toast";

function ExamenDetail({ examen, canEditExam, canEditDescription }) {

    const [ isEditingValor, setIsEditingValor] = React.useState(false);
    const [ descripcion, setDescripcion] = React.useState(examen.descripcion || "");
    const [ isEditingDescripcion, setIsEditingDescripcion] = React.useState(false);
    const [ nuevoValor, setNuevoValor] = React.useState(examen.valor);
    const [ sexoAplicable, setSexoAplicable] = React.useState(examen.sexo_aplicable || null);
    const [ isActve, setIsActive] = React.useState(examen.activo);
    const [ nombreAlternativo, setNombreAlternativo] = React.useState(examen.nombre_alternativo || "");
    const [ isEditingNombreAlternativo, setIsEditingNombreAlternativo] = React.useState(false);
    const opciones_sexo = ["F", "M", "A"];

    const handleSexoCarrusel = () => {
        const indiceActual = opciones_sexo.indexOf(sexoAplicable);
        const siguienteIndice = (indiceActual + 1) % opciones_sexo.length;
        const nuevoSexo = opciones_sexo[siguienteIndice];
        setSexoAplicable(nuevoSexo);

        // Enviar al servidor
        axios.patch(`/api/examenes/${examen.id}/update`, { sexo_aplicable: nuevoSexo })
            .then(response => {
                if (response.data.success) {
                    toast.success("Sexo aplicable actualizado.");
                } else {
                    toast.error("Error al actualizar el sexo aplicable");
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                toast.error("Ocurrió un error al actualizar");
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
        axios.patch(`/api/examenes/${examen.id}/update`, { valor: nuevoValor })
            .then(response => {
                if (response.data.success) {
                    setNuevoValor(response.data.data.examen.valor);
                    toast.success("Valor del examen actualizado.");
                } else {
                    toast.error("Error al actualizar el valor");
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                toast.error("Ocurrió un error al actualizar el valor");
            });
        setIsEditingValor(!isEditingValor);
    };

    const handleValorChange = (e) => {
        setNuevoValor(e.target.value);
    };

    const handleToggleEstado = () => {
        const nuevoEstado = !isActve;
        setIsActive(nuevoEstado);

        axios.patch(`/api/examenes/${examen.id}/update`, { activo: nuevoEstado })
            .then(response => {
                if (response.data.success) {
                    toast.success(nuevoEstado ? "Examen activado." : "Examen desactivado.");
                } else {
                    toast.error("Error al actualizar el estado");
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                toast.error("Ocurrió un error al actualizar el estado");
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
        const nuevoNombre = e.target.value;

         axios.patch(`/api/examenes/${examen.id}/update`, { nombre_alternativo: nuevoNombre })
            .then(response => {
                if (response.data.success) {
                    setNombreAlternativo(response.data.data.examen.nombre_alternativo);
                } else {
                    toast.error("Error al actualizar el nombre alternativo");
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                toast.error("Ocurrió un error al actualizar el nombre alternativo");
            });

            setNombreAlternativo(e.target.value);

    };

    const handleDescripcionSave = () => {
        axios.patch(`/api/examenes/${examen.id}/update`, { descripcion })
            .then(response => {
                if (response.data.success) {
                    setDescripcion(response.data.data.examen.descripcion || "");
                    setIsEditingDescripcion(false);
                    toast.success("Descripción actualizada.");
                } else {
                    toast.error("Error al actualizar la descripción");
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                toast.error("Ocurrió un error al actualizar la descripción");
            });
    };

  return (
    <>

      <section>

        <div className="flex items-center justify-center gap-2">
          <div className="flex flex-col w-full md:w-2/5 items-center gap-4">

            <h3 className="w-full">
                {isEditingNombreAlternativo ? (
                    canEditExam ? <>
                    <label htmlFor="nombre_alternativo"></label>
                    <input
                        type="text"
                        id="nombre_alternativo"
                        value={nombreAlternativo}
                        onChange={handleNombreAlternativoChange}
                        className="border border-gray-300 rounded px-2 py-1 text-sm"
                    />
                    <button
                        onClick={() => setIsEditingNombreAlternativo(false)}
                        className="ml-2 px-2 py-1 bg-blue-500 text-white rounded text-sm"
                    >
                        Guardar
                    </button>
                     </>
                    : examen.nombre_alternativo || "Sin nombre alternativo"
                ) : (
                 <div className={canEditExam ? "cursor-pointer flex flex-col" : "flex flex-col"} onClick={() => canEditExam && setIsEditingNombreAlternativo(true)}>
                <span className="bg-gray-200 text-balance">Nombre alternativo: </span>    {examen.nombre_alternativo || "Sin nombre alternativo"}
                 </div>
                 )
                }
            </h3>
            <h3 className="text-lg font-light text-green-700">
                {isEditingValor ? (
                    canEditExam ? <>
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
                    : <span>Valor: {nuevoValor}</span>
                ) : (
                 <span>Valor: {nuevoValor}</span>
                 )
                }
              {canEditExam && <span
              className="ml-2 text-sm text-blue-600 cursor-pointer"
              onClick={() => setIsEditingValor(true)}
              >edit</span>
              }
            </h3>
          </div>

          <div className="flex flex-col w-full md:w-2/5 items-center gap-4">

              <span
              className="px-2 py-1 bg-green-200 text-green-800 rounded">
                {isActve ? "Activo" : "Inactivo"}
                {canEditExam && <button
                    onClick={handleToggleEstado}
                    className={`ml-2 px-2 py-1 ${isActve ? "bg-red-500 text-white" : "bg-green-500 text-white"} rounded text-sm`}
                >
                    {isActve ? "Desactivar" : "Activar"}
                </button>}
              </span>


            {canEditExam && examen.sexo_aplicable !== undefined && (
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

        <div className="max-w-11/12 p-4">
          {isEditingDescripcion ? (
            <div className="space-y-2">
              <textarea
                value={descripcion}
                onChange={(event) => setDescripcion(event.target.value)}
                maxLength={2000}
                className="w-full rounded border border-gray-300 p-2"
                aria-label="Descripción del examen"
              />
              <button onClick={handleDescripcionSave} className="rounded bg-blue-500 px-3 py-1 text-white">
                Guardar descripción
              </button>
            </div>
          ) : (
            <div className="flex items-start gap-2">
              <p>
                {descripcion.charAt(0).toUpperCase() + descripcion.slice(1)}
              </p>
              {canEditDescription && (
                <button onClick={() => setIsEditingDescripcion(true)} className="text-sm text-blue-600">
                  Editar descripción
                </button>
              )}
            </div>
          )}
        </div>
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
  const canEditExam = element.getAttribute('data-can-edit-exam') === 'true';
  const canEditDescription = element.getAttribute('data-can-edit-description') === 'true';
  const root = createRoot(element);
  root.render(<ExamenDetail examen={examen} canEditExam={canEditExam} canEditDescription={canEditDescription} />);
}

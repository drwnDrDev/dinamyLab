import React from "react";
import { useState, useEffect } from "react";
import { usePersonaReferencias } from "./hooks/usePersonaReferencias";
import { useTablasRef } from "./hooks/useTablasRef";
import SelectField from "./SelectField";

const DatosPaciente = ({ pacienteId = null }) => {
    const [paciente, setPaciente] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const { tiposDocumento, paises, municipios, epsList } = usePersonaReferencias();
    const { tiposAfiliacion } = useTablasRef();


    // Estados para los datos del formulario
    const [formData, setFormData] = useState({
        tipo_documento: '',
        numero_documento: '',
        nombres: '',
        apellidos: '',
        fecha_nacimiento: '',
        sexo: '',
        pais_nacimiento: '',
        municipio_id: '',
        direccion: '',
        telefono: '',
        zona_residencia: '',
        pais_residencia: '',
        email: '',
        eps_id: '',
        tipo_afiliacion: ''
    });


    const handleInputChange = (e) => {
        const { name, value } = e.target;

        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const url = pacienteId
                ? `/api/personas/${pacienteId}`
                : '/api/personas';

            const method = pacienteId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) throw new Error('Error al guardar los datos');

            // Manejar respuesta exitosa
            const result = await response.json();
            // Aquí puedes agregar lógica adicional post-guardado

        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    console.log('Datos del state de tipos de documento:', tiposDocumento);

    return (

        <section className="paciente_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
            <form onSubmit={handleSubmit} className="max-w-screen-lg mx-auto">
                <input type="hidden" id="perfil" name="perfil" value=""></input>
                <div className="flex gap-4">
                    <h2 className="font-bold mb-4 text-xl text-titles">Datos del Paciente </h2>
                </div>

                <div className="inputs_container md:grid md:grid-cols-2 gap-8 w-full min-w-80 p-4">
                    <div className="flex flex-col gap-4 mb-4 md:m-0">
                        {/* Tipo de documento */}

                        <SelectField
                            label="Tipo de Documento"
                            name="tipo_documento"
                            value={formData.tipo_documento}
                            onChange={handleInputChange}
                            options={tiposDocumento.map(doc => ({ codigo: doc.id, nombre: doc.nombre }))}
                        />

                        <div>
                            <label className="block font-medium text-sm text-text">
                                Número de Documento
                            </label>
                            <input
                                type="text"
                                name="numero_documento"
                                value={formData.numero_documento}
                                onChange={handleInputChange}
                                className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary
                                rounded-md"
                            />
                        </div>

                        <SelectField
                            label="Pais de Nacimiento"
                            name="pais_nacimiento"
                            value={formData.pais_nacimiento}
                            onChange={handleInputChange}
                            options={paises.map(p => ({ codigo: p.codigo_iso, nombre: p.nombre }))}
                        />

                        <div>
                            <label className="block font-medium text-sm text-text">
                                Nombres
                            </label>
                            <input
                                type="text"
                                name="nombres"
                                value={formData.nombres}
                                onChange={handleInputChange}
                                className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary
                                rounded-md"
                            />
                        </div>

                        <div>
                            <label className="block font-medium text-sm text-text">
                                Apellidos
                            </label>
                            <input
                                type="text"
                                name="apellidos"
                                value={formData.apellidos}
                                onChange={handleInputChange}
                                className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary
                                rounded-md"
                            />
                        </div>
                        <div className="w-full pb-2 flex gap-2 items-center">
                            <label className="block font-medium text-sm text-text">
                                Fecha de Nacimiento
                            </label>
                            <input
                                type="date"
                                name="fecha_nacimiento"
                                value={formData.fecha_nacimiento}
                                onChange={handleInputChange}
                                className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary
                                rounded-md"
                            />
                        </div>
                        <div class="w-full pb-2 flex gap-2 items-center">
                            <span className="font-semibold">Sexo</span>
                            <label htmlFor="sexo_femenino" className="inline-flex items-center">F</label>
                            <input
                                type="radio"
                                id="sexo_femenino"
                                name="sexo"
                                value="F"
                                checked={formData.sexo === 'F'}
                                onChange={handleInputChange}
                                className="h-4 w-4 border-borders focus:border-primary focus:ring-primary checked:bg-primary"
                            />
                            <label htmlFor="sexo_masculino" className="inline-flex items-center">M</label>
                            <input
                                type="radio"
                                id="sexo_masculino"
                                name="sexo"
                                value="M"
                                checked={formData.sexo === 'M'}
                                onChange={handleInputChange}
                                className="h-4 w-4 border-borders focus:border-primary focus:ring-primary checked:bg-primary"
                            />
                            <label htmlFor="sexo_otro" className="inline-flex items-center">Intersexual</label>
                            <input
                                type="radio"
                                id="sexo_otro"
                                name="sexo"
                                value="I"
                                checked={formData.sexo === 'I'}
                                onChange={handleInputChange}
                                className="h-4 w-4 border-borders focus:border-primary focus:ring-primary checked:bg-primary"
                            />
                        </div>
                        <SelectField
                            label="EPS"
                            name="eps_id"
                            value={formData.eps_id}
                            onChange={handleInputChange}
                            options={epsList.map(e => ({ codigo: e.id, nombre: e.nombre }))}  // Asegurando que las opciones tengan 'codigo' y 'nombre'
                        />
                        <SelectField
                            label="Tipo de Afiliación"
                            name="tipo_afiliacion"
                            value={formData.tipo_afiliacion}
                            onChange={handleInputChange}
                            options={tiposAfiliacion.data}
                        />

                    </div>

                    {/*INFORMACION DE CONTACTO*/}

                    <div class="flex flex-col gap-4 mb-4 md:m-0">
                        <div>
                            <h3 className="font-medium text-normal text-titles my-4">Información de contacto</h3>
                        </div>
                        <div>
                            <label className="block font-medium text-sm text-text">
                                Dirección
                            </label>
                            <input
                                type="text"
                                name="direccion"
                                value={formData.direccion}
                                onChange={handleInputChange}
                                className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary
                                rounded-md"
                            />
                        </div>
                        <div>
                            <label className="block font-medium text-sm text-text">
                                Teléfono
                            </label>
                            <input
                                type="text"
                                name="telefono"
                                value={formData.telefono}
                                onChange={handleInputChange}
                                className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary
                                rounded-md"
                            />
                        </div>
                        <div>
                            <label className="block font-medium text-sm text-text">
                                Email
                            </label>
                            <input
                                type="email"
                                name="email"
                                value={formData.email}
                                onChange={handleInputChange}
                                className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary
                                rounded-md"
                            />
                        </div>
                        
                        <SelectField
                            label="Pais de Residencia"
                            name="pais_residencia"
                            value={formData.pais_residencia}
                            onChange={handleInputChange}
                            options={paises.map(p => ({ codigo: p.codigo_iso, nombre: p.nombre }))}
                        />
                        <SelectField
                            label="Municipio"
                            name="municipio_id"
                            value={formData.municipio_id}
                            onChange={handleInputChange}
                            options={municipios.map(mun => ({
                                codigo: mun.codigo,
                                nombre: `${mun.municipio} - ${mun.departamento}`
                            }))}
                        />
                        
                    </div>

                </div>

                <div className="flex justify-end space-x-3">
                    <button
                        type="button"  /*desactivando el evento por ahora*/
                        className="inline-flex justify-center rounded-md border border-transparent bg-primary py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-titles focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    /*disabled={loading}*/
                    >
                        {loading ? 'Guardando...' : 'Guardar'}
                    </button>
                </div>
            </form>
        </section>
    );
};

export default DatosPaciente;
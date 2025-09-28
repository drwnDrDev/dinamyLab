import React from "react";
import { useState, useEffect } from "react";
import { usePersonaReferencias } from "./hooks/usePersonaReferencias";
import { useTablasRef } from "./hooks/useTablasRef";
import SelectField from "./SelectField";

const DatosPaciente = ({ pacienteId = null }) => {
    const [personaExistente, setPersonaExistente] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const { tiposDocumento, paises, municipios, epsList } = usePersonaReferencias();
    const { tiposAfiliacion } = useTablasRef();


    // Estados para los datos del formulario
    const [formData, setFormData] = useState({
        id: null,
        perfil: 'Paciente',
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


    // Función para buscar persona por documento
    const buscarPersonaPorDocumento = async (numDoc) => {
        if (!numDoc.trim()) return;
        
        setLoading(true);
        try {
            const response = await fetch(`/api/personas/buscar/${numDoc}`);
            if (!response.ok) return null;
            
            const data = await response.json();
            if (data) {
                console.log('✅ Persona encontrada:', data);
                setPersonaExistente(data);
                return data;
            }
            setPersonaExistente(null);
            return null;
        } catch (err) {
            console.log('❌ Error al buscar persona:', err);
            setPersonaExistente(null);
            return null;
        } finally {
            setLoading(false);
        }
    };

    // Manejador del evento blur para el número de documento
    const handleDocumentoBlur = async (e) => {
        const numeroDocumento = e.target.value.trim();
        if (!numeroDocumento) return;

        const usuario = await buscarPersonaPorDocumento(numeroDocumento);
        if (usuario && usuario.data) {

            const persona = usuario.data;

            setFormData(prev => ({
                ...prev,
                id: persona.id || null,
                perfil: 'Paciente',
                tipo_documento: persona.tipo_documento || '',
                numero_documento: persona.numero_documento || '',
                nombres: persona.nombre || '',
                apellidos: persona.apellido || '',
                fecha_nacimiento: persona.fecha_nacimiento || '',
                sexo: persona.sexo || '',
                pais_nacimiento: persona.pais_origen || '',
                municipio_id: persona.municipio_id || '',
                direccion: persona.direccion || '',
                telefono: persona.telefono || '',
                zona_residencia: persona.zona || '',
                pais_residencia: persona.pais_residencia || '',
                email: persona.correo || '',
                eps_id: persona.eps_id || '',
                tipo_afiliacion: persona.tipo_afiliacion || ''
            }));
        }
    };

    const handleInputChange = (e) => {
        const { name, value } = e.target;

        setFormData(prev => {
            const newFormData = {
                ...prev,
                [name]: value
            };
            return newFormData;
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        try {
            // Determinar si es una actualización o creación
            const esActualizacion = personaExistente !== null;
            const url = esActualizacion 
                ? `/api/personas/${personaExistente.id}`
                : '/api/personas';
            
            const method = esActualizacion ? 'PUT' : 'POST';

            // Log pre-envío
            console.log('📋 ENVIANDO FORMULARIO:', {
                operacion: esActualizacion ? 'Actualización' : 'Nuevo Registro',
                metodo: method,
                url: url,
                datos: formData
            });

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const resultado = await response.json();
            
            if (!response.ok) {
                // Si el servidor devuelve errores de validación
                if (response.status === 422) {
                    const errores = Object.values(resultado.errors || {}).flat().join('\n');
                    throw new Error(`Errores de validación:\n${errores}`);
                }
                // Si es otro tipo de error
                throw new Error(resultado.message || `Error al ${esActualizacion ? 'actualizar' : 'crear'} la persona`);
            }
            console.log('✅ Operación exitosa:', resultado);

            // Aquí podrías agregar alguna notificación de éxito
            alert(`Persona ${esActualizacion ? 'actualizada' : 'creada'} exitosamente`);

            // Opcional: limpiar el formulario después de crear
            if (!esActualizacion) {
                setFormData({
                    perfil: 'Paciente',
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
                setPersonaExistente(null);
            }

        } catch (err) {
            console.error('❌ Error:', err);
            console.log('Estado del formulario al momento del error:', formData);
            setError(err.message);
            alert(err.message);
        } finally {
            setLoading(false);
        }
    };
   

    return (

        <section className="paciente_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
            <form onSubmit={handleSubmit} className="max-w-screen-lg mx-auto">
                
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
                            options={tiposDocumento.map(doc => ({ key: doc.id, codigo: doc.cod_rips, nombre: doc.nombre }))}
                        />

                        <div>
                            <label className="block font-medium text-sm text-text">
                                Número de Documento
                            </label>
                                                    <div className="relative">
                            <input
                                type="text"
                                name="numero_documento"
                                value={formData.numero_documento}
                                onChange={handleInputChange}
                                onBlur={handleDocumentoBlur}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            {loading && (
                                <div className="absolute right-2 top-1/2 transform -translate-y-1/2">
                                    <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"></div>
                                </div>
                            )}
                        </div>
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
                        <div className="w-full pb-2 flex gap-2 items-center">
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

                    <div className="flex flex-col gap-4 mb-4 md:m-0">
                        <div>
                            <h3 className="font-medium text-normal text-titles my-4">Información de contacto</h3>
                        </div>
                        <div className="w-full pb-2 flex gap-2 items-center">
                            <span className="font-semibold">Zona de Residencia</span>
                            <label htmlFor="zona_urbana" className="inline-flex items-center">Urbana</label>
                            <input
                                type="radio"
                                id="zona_urbana"
                                name="zona_residencia"
                                value="02"
                                checked={formData.zona_residencia === '02'}
                                onChange={handleInputChange}
                                className="h-4 w-4 border-borders focus:border-primary focus:ring-primary checked:bg-primary"
                            />
                            <label htmlFor="zona_rural" className="inline-flex items-center">Rural</label>
                            <input
                                type="radio"
                                id="zona_rural"
                                name="zona_residencia"
                                value="01"
                                checked={formData.zona_residencia === '01'}
                                onChange={handleInputChange}
                                className="h-4 w-4 border-borders focus:border-primary focus:ring-primary checked:bg-primary"
                            />

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
                        type="submit"
                        disabled={loading}
                        className="inline-flex items-center justify-center rounded-md border border-transparent bg-primary py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-titles focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {loading ? (
                            <>
                                <div className="animate-spin mr-2 h-4 w-4 border-b-2 border-white rounded-full"></div>
                                {personaExistente ? 'Actualizando...' : 'Guardando...'}
                            </>
                        ) : (
                            personaExistente ? 'Actualizar' : 'Guardar'
                        )}
                    </button>
                </div>
            </form>
        </section>
    );
};

export default DatosPaciente;
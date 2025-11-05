import React from "react";
import { useState, useEffect } from "react";
import { usePersonaReferencias } from "./hooks/usePersonaReferencias";
import { useTablasRef } from "./hooks/useTablasRef";
import SelectField from "./SelectField";
import { useValidacionNormativa } from "./hooks/useValidacionNormativa";
import axios from "axios";

// Configuración global de Axios para que funcione con las sesiones de Laravel
axios.defaults.withCredentials = true;

const FormPersona = ({ persona, setPersona, perfil }) => {
    const [personaExistente, setPersonaExistente] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [formData, setFormData] = useState({
        id: null,
        perfil: '',
        tipo_documento: 'CC',
        numero_documento: '',
        nombres: '',
        apellidos: '',
        fecha_nacimiento: '',
        sexo: '',
        pais_nacimiento: '170',
        municipio: '11001',
        direccion: '',
        telefono: '',
        zona: '02',
        pais_residencia: '170',
        correo: '',
        eps: '',
        tipo_afiliacion: ''
    });

    const { tiposDocumento, paises, municipios, epsList } = usePersonaReferencias();
    const { tiposAfiliacion } = useTablasRef();

    // 1. Obtener la cookie CSRF de Sanctum al montar el componente
    useEffect(() => {
        const getCsrfCookie = async () => {
            try {
                await axios.get('/sanctum/csrf-cookie');
                console.log('CSRF cookie obtained');
            } catch (error) {
                console.error('Could not get CSRF cookie', error);
            }
        };
        getCsrfCookie();
    }, []);

    const tipoDocConfig = tiposDocumento.find(doc => doc.cod_rips === formData.tipo_documento);
    const erroresValidacion = useValidacionNormativa(
        tipoDocConfig,
        formData.numero_documento,
        formData.fecha_nacimiento
    );

    useEffect(() => {
        setFormData(prev => ({
            ...prev,
            perfil: perfil || ''
        }));
    }, [perfil]);

    // Función para buscar persona por documento (sin lógica de token)
    const buscarPersonaPorDocumento = async (numDoc) => {
        if (!numDoc.trim()) return;

        setLoading(true);
        try {
            const response = await axios.get(`/api/personas/buscar/${numDoc}`);

            if (response.status !== 200) return null;

            const data = response.data;
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

    const handleDocumentoBlur = async (e) => {
        const numeroDocumento = e.target.value.trim();
        if (!numeroDocumento) return;

        const usuario = await buscarPersonaPorDocumento(numeroDocumento);
        if (usuario && usuario.data) {
            const persona = usuario.data;
            setFormData(prev => ({
                ...prev,
                id: persona.id || null,
                perfil: '',
                tipo_documento: persona.tipo_documento || '',
                numero_documento: persona.numero_documento || '',
                nombres: persona.nombre || '',
                apellidos: persona.apellido || '',
                fecha_nacimiento: persona.fecha_nacimiento || '',
                sexo: persona.sexo || '',
                pais_nacimiento: persona.pais_origen || '',
                municipio: persona.municipio || '',
                direccion: persona.direccion || '',
                telefono: persona.telefono || '',
                zona: persona.zona || '',
                pais_residencia: persona.pais_residencia || '',
                correo: persona.correo || '',
                eps: persona.eps || '',
                tipo_afiliacion: persona.tipo_afiliacion || ''
            }));
        }
    };

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    // handleSubmit (sin lógica de token, depende de la cookie de sesión)
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        try {
            const esActualizacion = personaExistente !== null;
            const url = esActualizacion
                ? `/api/personas/${personaExistente.data.id}`
                : '/api/personas';

            console.log('📋 ENVIANDO FORMULARIO (STATEFUL):', {
                operacion: esActualizacion ? 'Actualización' : 'Nuevo Registro',
                url: url,
                datos: formData
            });

            const response = await axios({
                method: esActualizacion ? 'PUT' : 'POST',
                url: url,
                data: formData,
            });

            const resultado = response.data;
            console.log('✅ Operación exitosa:', resultado);

            const personaActualizada = resultado.data;
            setPersonaExistente(null);
            setPersona(personaActualizada);

            alert(`Persona ${esActualizacion ? 'actualizada' : 'creada'} exitosamente`);

            if (!esActualizacion) {
                setFormData({
                    id: null,
                    perfil: '',
                    tipo_documento: 'CC',
                    numero_documento: '',
                    nombres: '',
                    apellidos: '',
                    fecha_nacimiento: '',
                    sexo: '',
                    pais_nacimiento: '170',
                    municipio: '11001',
                    direccion: '',
                    telefono: '',
                    zona: '02',
                    pais_residencia: '170',
                    correo: '',
                    eps: '',
                    tipo_afiliacion: ''
                });
                setPersonaExistente(null);
            }

        } catch (err) {
            console.error('❌ Error completo:', err);
            console.error('Respuesta del servidor:', err.response?.data);

            let mensajeError = 'Error al procesar la solicitud';

            if (err.response?.status === 422) {
                const errores = Object.values(err.response.data.errors || {}).flat().join('\n');
                mensajeError = `Errores de validación:\n${errores}`;
            } else if (err.response?.status === 401 || err.response?.status === 419) {
                mensajeError = 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.';
                // Opcional: Redirigir al login después de un tiempo
                // setTimeout(() => window.location.href = '/login', 2000);
            } else if (err.response?.data?.message) {
                mensajeError = err.response.data.message;
            }

            setError(mensajeError);
            alert(mensajeError);
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="max-w-screen-lg mx-auto">
            <div className="flex gap-4">
                <h2 className="font-bold mb-4 text-xl text-titles">Nuevo registro de {perfil} </h2>
            </div>

            {persona && (
                <div className="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
                    <strong>{perfil} seleccionado:</strong> {persona.data.primer_nombre} {persona.data.primer_apellido} - Documento: {persona.data.numero_documento}
                </div>
            )}

            <div className="inputs_container md:grid md:grid-cols-2 gap-8 w-full min-w-80 p-4">
                <div className="flex flex-col gap-4 mb-4 md:m-0">
                    <SelectField
                        label="Tipo de Documento"
                        name="tipo_documento"
                        value={formData.tipo_documento}
                        onChange={handleInputChange}
                        codigo={true}
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
                                className={erroresValidacion.numero_documento ? `h-9 w-full p-2 border-borders focus:border-red-500 focus:ring-red-500 rounded-md` : `h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary rounded-md`}
                            />
                            {loading && (
                                <div className="absolute right-2 top-1/2 transform -translate-y-1/2">
                                    <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"></div>
                                </div>
                            )}
                            {erroresValidacion.numero_documento && (
                                <div className="text-sm text-red-500 mt-1">
                                    {erroresValidacion.numero_documento}
                                </div>
                            )}
                        </div>
                    </div>
                    {perfil === 'Paciente' && (
                        <SelectField
                            label="Pais de Nacimiento"
                            name="pais_nacimiento"
                            value={formData.pais_nacimiento}
                            onChange={handleInputChange}
                            codigo={false}
                            options={paises.map(p => ({ codigo: p.codigo_iso, nombre: p.nombre }))}
                        />
                    )}
                    <div>
                        <label className="block font-medium text-sm text-text">
                            Nombres
                        </label>
                        <input
                            type="text"
                            name="nombres"
                            value={formData.nombres}
                            onChange={handleInputChange}
                            className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary rounded-md"
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
                            className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary rounded-md"
                        />
                    </div>
                    {perfil !== 'Pagador' && (
                    <div>
                        <div className="w-full pb-2 flex gap-2 items-center">
                            <label className="block font-medium text-sm text-text">
                                Fecha de Nacimiento
                            </label>
                            <input
                                type="date"
                                name="fecha_nacimiento"
                                value={formData.fecha_nacimiento}
                                onChange={handleInputChange}
                                className={erroresValidacion.fecha_nacimiento ? `h-9 w-full p-2 border-borders rounded-md focus:border-red-500 focus:ring-red-500` : `h-9 w-full p-2 border-borders rounded-md focus:border-primary focus:ring-primary`}
                            />
                        </div>
                        {erroresValidacion.fecha_nacimiento && (
                            <div className="text-sm text-red-500 mt-1">
                                {erroresValidacion.fecha_nacimiento}
                            </div>
                        )}
                    </div>
                    )}
                    {perfil === 'Paciente' && (
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
                    )}
                    {perfil === 'Paciente' && (
                        <div>
                            <label className="block font-medium text-sm text-text">
                                EPS
                            </label>
                            <input
                                list="eps_list"
                                name="eps"
                                value={formData.eps}
                                onChange={handleInputChange}
                                className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary rounded-md"
                            />
                            <datalist id="eps_list">
                                {epsList.map(e => (
                                    <option key={e.id} value={e.nombre} />
                                ))}
                            </datalist>
                        </div>
                    )}
                    {perfil === 'Paciente' && (
                        <SelectField
                            label="Tipo de Afiliación"
                            name="tipo_afiliacion"
                            value={formData.tipo_afiliacion}
                            onChange={handleInputChange}
                            codigo={true}
                            options={tiposAfiliacion.data}
                        />
                    )}
                </div>
                <div className="flex flex-col gap-4 mb-4 md:m-0">
                    <div>
                        <h3 className="font-medium text-normal text-titles my-4">Información de contacto</h3>
                    </div>
                    {perfil === 'Paciente' && (
                    <div className="w-full pb-2 flex gap-2 items-center">
                        <span className="font-semibold">Zona de Residencia</span>
                        <label htmlFor="zona_urbana" className="inline-flex items-center">Urbana</label>
                        <input
                            type="radio"
                            id="zona_urbana"
                            name="zona"
                            value="02"
                            checked={formData.zona === '02'}
                            onChange={handleInputChange}
                            className="h-4 w-4 border-borders focus:border-primary focus:ring-primary checked:bg-primary"
                        />
                        <label htmlFor="zona_rural" className="inline-flex items-center">Rural</label>
                        <input
                            type="radio"
                            id="zona_rural"
                            name="zona"
                            value="01"
                            checked={formData.zona === '01'}
                            onChange={handleInputChange}
                            className="h-4 w-4 border-borders focus:border-primary focus:ring-primary checked:bg-primary"
                        />
                    </div>
                    )}
                    {perfil === 'Paciente' && (
                    <div>
                        <label className="block font-medium text-sm text-text">
                            Dirección
                        </label>
                        <input
                            type="text"
                            name="direccion"
                            value={formData.direccion}
                            onChange={handleInputChange}
                            className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary rounded-md"
                        />
                    </div>
                    )}
                    <div>
                        <label className="block font-medium text-sm text-text">
                            Teléfono
                        </label>
                        <input
                            type="text"
                            name="telefono"
                            value={formData.telefono}
                            onChange={handleInputChange}
                            className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary rounded-md"
                        />
                    </div>
                    {perfil === 'Paciente' && (
                    <div>
                        <label className="block font-medium text-sm text-text">
                            Email
                        </label>
                        <input
                            type="email"
                            name="correo"
                            value={formData.correo}
                            onChange={handleInputChange}
                            className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary rounded-md"
                        />
                    </div>
                    )}
                    {perfil === 'Paciente' && (
                    <SelectField
                        label="Pais de Residencia"
                        name="pais_residencia"
                        value={formData.pais_residencia}
                        onChange={handleInputChange}
                        codigo={false}
                        options={paises.map(p => ({ codigo: p.codigo_iso, nombre: p.nombre }))}
                    />
                    )}
                    {perfil === 'Paciente' && (
                    <SelectField
                        label="Municipio"
                        name="municipio"
                        value={formData.municipio}
                        onChange={handleInputChange}
                        codigo={false}
                        options={municipios.map(mun => ({
                            codigo: mun.codigo,
                            nombre: `${mun.municipio} - ${mun.departamento}`
                        }))}
                    />
                    )}
                    {perfil === 'Acompaniante' && (
                        <div>
                            <label className="block font-medium text-sm text-text">
                                Parentesco
                            </label>
                            <input
                                list="parentesco_list"
                                name="Parentesco"
                                value={formData.parentesco}
                                onChange={handleInputChange}
                                className="h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary rounded-md"
                            />
                            <datalist id="parentesco_list">
                                <option value="Madre" />
                                <option value="Padre" />
                                <option value="Hermano(a)" />
                                <option value="Hijo(a)" />
                                <option value="Esposo(a)" />
                                <option value="Amigo(a)" />
                            </datalist>
                        </div>
                    )}
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
    );
};

export default FormPersona;
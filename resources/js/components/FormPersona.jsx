import React from "react";
import { useState, useEffect } from "react";
import { usePersonaReferencias } from "./hooks/usePersonaReferencias";
import { useTablasRef } from "./hooks/useTablasRef";
import SelectField from "./SelectField";
import Loader from "./Loader";
import CompletedCheck from "./CompletedCheck";
import { useValidacionNormativa } from "./hooks/useValidacionNormativa";
import { getCatalogs } from "../lib/catalogs";
import axios from "axios";
import { toast } from "../lib/toast";
import TextField from "./ui/TextField";
import FormSection from "./ui/FormSection";
import { inputClass, labelClass, errorClass, toErrorList } from "./ui/formStyles";

const TIPO_AFILIACION_PARTICULAR = '12';

const SEXOS_FALLBACK = [
    { codigo: 'F', nombre: 'Femenino' },
    { codigo: 'M', nombre: 'Masculino' },
    { codigo: 'I', nombre: 'Indeterminado' },
];

const IDS_SEXO = {
    F: 'sexo_femenino',
    M: 'sexo_masculino',
    I: 'sexo_otro',
};

// Configuración global de Axios para que funcione con las sesiones de Laravel
axios.defaults.withCredentials = true;

const FormPersona = ({ persona, setPersona, perfil, showTitle = true }) => {
    const [personaExistente, setPersonaExistente] = useState(null);
    const [loading, setLoading] = useState(false);
    const [fieldErrors, setFieldErrors] = useState({});
    const [formData, setFormData] = useState({
        id: null,
        perfil: '',
        tipo_documento: 'CC',
        numero_documento: '',
        nombres: '',
        apellidos: '',
        fecha_nacimiento: '',
        sexo: '',
        grupo_sanguineo: '',
        factor_rh: '',
        pais_nacimiento: '170',
        municipio: '11001',
        direccion: '',
        telefono: '',
        zona: '02',
        pais_residencia: '170',
        correo: '',
        eps: '',
        tipo_afiliacion: TIPO_AFILIACION_PARTICULAR
    });

    const { tiposDocumento, paises, municipios, epsList } = usePersonaReferencias();
    const { tiposAfiliacion } = useTablasRef();

    const sexosCatalogo = getCatalogs().sexos;
    const sexos = Array.isArray(sexosCatalogo) && sexosCatalogo.length > 0
        ? sexosCatalogo
        : SEXOS_FALLBACK;

    // 1. Obtener la cookie CSRF de Sanctum al montar el componente
    useEffect(() => {
        const getCsrfCookie = async () => {
            try {
                await axios.get('/sanctum/csrf-cookie');
            } catch (error) {
                // Sin acción requerida — el request fallará y mostrará el error al usuario
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
                setPersonaExistente(data);
                return data;
            }
            setPersonaExistente(null);
            return null;
        } catch (err) {
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
                grupo_sanguineo: persona.grupo_sanguineo || '',
                factor_rh: persona.factor_rh || '',
                pais_nacimiento: persona.pais_origen || '',
                municipio: persona.municipio || '',
                direccion: persona.direccion || '',
                telefono: persona.telefono || '',
                zona: persona.zona || '',
                pais_residencia: persona.pais_residencia || '',
                correo: persona.correo || '',
                eps: persona.eps || '',
                tipo_afiliacion: persona.tipo_afiliacion || TIPO_AFILIACION_PARTICULAR
            }));
        }
    };

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
        // Limpiar error del campo al modificarlo
        if (fieldErrors[name]) {
            setFieldErrors(prev => ({ ...prev, [name]: undefined }));
        }
    };

    // handleSubmit (sin lógica de token, depende de la cookie de sesión)
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setFieldErrors({});

        try {
            const esActualizacion = personaExistente !== null;
            const url = esActualizacion
                ? `/api/personas/${personaExistente.data.id}`
                : '/api/personas';

            const response = await axios({
                method: esActualizacion ? 'PUT' : 'POST',
                url: url,
                data: formData,
            });

            const resultado = response.data;
            const personaActualizada = resultado.data;
            setPersonaExistente(null);
            if (setPersona) {
                setPersona(personaActualizada);
            }

            toast.success(esActualizacion ? 'Persona actualizada exitosamente.' : 'Paciente registrado exitosamente.', { key: 'form-persona' });

            setFormData({
                id: null,
                perfil: '',
                tipo_documento: 'CC',
                numero_documento: '',
                nombres: '',
                apellidos: '',
                fecha_nacimiento: '',
                sexo: '',
                grupo_sanguineo: '',
                factor_rh: '',
                pais_nacimiento: '170',
                municipio: '11001',
                direccion: '',
                telefono: '',
                zona: '02',
                pais_residencia: '170',
                correo: '',
                eps: '',
                tipo_afiliacion: TIPO_AFILIACION_PARTICULAR
            });
            setPersonaExistente(null);

        } catch (err) {
            if (err.response?.status === 422) {
                setFieldErrors(err.response.data.errors || {});
                toast.error('Revisa los mensajes junto a los campos e inténtalo de nuevo.', { title: 'Hay errores en el formulario', key: 'form-persona' });
            } else if (err.response?.status === 401 || err.response?.status === 419) {
                toast.error('Tu sesión ha expirado. Por favor, inicia sesión nuevamente.', { key: 'form-persona' });
            } else if (err.response?.data?.message) {
                toast.error(err.response.data.message, { key: 'form-persona' });
            } else {
                toast.error('Error al procesar la solicitud', { key: 'form-persona' });
            }
        } finally {
            setLoading(false);
        }
    };

    // Helper para mostrar el primer error de un campo
    const fe = (name) => fieldErrors[name]?.[0];

    const esPaciente = perfil === 'Paciente';
    const muestraFecha = perfil !== 'Pagador';

    // Anchos (12 columnas en md+) según los campos visibles en cada perfil
    const spanTipoDoc = esPaciente ? 'md:col-span-3' : 'md:col-span-4';
    const spanNumeroDoc = esPaciente ? 'md:col-span-3' : (muestraFecha ? 'md:col-span-4' : 'md:col-span-8');
    const spanFecha = esPaciente ? 'md:col-span-3' : 'md:col-span-4';
    const spanTelefono = esPaciente ? 'md:col-span-3' : 'md:col-span-6';

    const radioClass = 'inline-flex items-center gap-1.5 cursor-pointer text-sm text-text';
    const erroresSangre = toErrorList([fe('grupo_sanguineo'), fe('factor_rh')]);

    return (
        <>
        {loading && <Loader />}

        <form onSubmit={handleSubmit} className="max-w-screen-lg mx-auto space-y-5">
            {showTitle && (
                <h2 className="font-bold text-lg text-titles">Nuevo registro de {perfil}</h2>
            )}

            {persona && (
                <div className="p-3 text-sm bg-green-100 border border-green-300 text-green-800 rounded-md">
                    <strong>{perfil} seleccionado:</strong> {persona.data.primer_nombre} {persona.data.primer_apellido} - Documento: {persona.data.numero_documento}
                </div>
            )}

            <FormSection title="Datos personales">
                <SelectField
                    className={spanTipoDoc}
                    label="Tipo de documento"
                    name="tipo_documento"
                    value={formData.tipo_documento}
                    onChange={handleInputChange}
                    codigo={true}
                    options={tiposDocumento.map(doc => ({ key: doc.id, codigo: doc.cod_rips, nombre: doc.nombre }))}
                />
                <TextField
                    className={spanNumeroDoc}
                    label="Número de documento"
                    name="numero_documento"
                    value={formData.numero_documento}
                    onChange={handleInputChange}
                    onBlur={handleDocumentoBlur}
                    error={[erroresValidacion.numero_documento, fe('numero_documento')]}
                    adornment={loading && (
                        <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
                    )}
                />
                {muestraFecha && (
                    <TextField
                        className={spanFecha}
                        type="date"
                        label="Fecha de nacimiento"
                        name="fecha_nacimiento"
                        value={formData.fecha_nacimiento}
                        onChange={handleInputChange}
                        error={[erroresValidacion.fecha_nacimiento, fe('fecha_nacimiento')]}
                    />
                )}
                {esPaciente && (
                    <SelectField
                        className="md:col-span-3"
                        label="País de nacimiento"
                        name="pais_nacimiento"
                        value={formData.pais_nacimiento}
                        onChange={handleInputChange}
                        codigo={false}
                        options={paises.map(p => ({ codigo: p.codigo_iso, nombre: p.nombre }))}
                        error={fe('pais_nacimiento')}
                    />
                )}

                <TextField
                    className="md:col-span-6"
                    label="Nombres"
                    name="nombres"
                    value={formData.nombres}
                    onChange={handleInputChange}
                    error={fe('nombres')}
                />
                <TextField
                    className="md:col-span-6"
                    label="Apellidos"
                    name="apellidos"
                    value={formData.apellidos}
                    onChange={handleInputChange}
                    error={fe('apellidos')}
                />

                {esPaciente && (
                    <div className="md:col-span-7">
                        <span id="sexo_label" className={labelClass}>Sexo</span>
                        <div role="radiogroup" aria-labelledby="sexo_label" className="flex flex-wrap items-center gap-x-4 gap-y-1 min-h-9">
                            {sexos.map(({ codigo, nombre }) => {
                                const id = IDS_SEXO[codigo] ?? `sexo_${String(codigo).toLowerCase()}`;

                                return (
                                    <label key={codigo} htmlFor={id} className={radioClass}>
                                        <input
                                            type="radio"
                                            id={id}
                                            name="sexo"
                                            value={codigo}
                                            checked={formData.sexo === codigo}
                                            onChange={handleInputChange}
                                            className="h-4 w-4 accent-primary"
                                        />
                                        {nombre}
                                    </label>
                                );
                            })}
                        </div>
                        {fe('sexo') && <p className={errorClass}>{fe('sexo')}</p>}
                    </div>
                )}
                <div className="md:col-span-5">
                    <span id="sangre_label" className={labelClass}>Grupo sanguíneo y Rh</span>
                    <div role="group" aria-labelledby="sangre_label" className="grid grid-cols-2 gap-2">
                        <select
                            id="grupo_sanguineo"
                            name="grupo_sanguineo"
                            aria-label="Grupo sanguíneo"
                            value={formData.grupo_sanguineo}
                            onChange={handleInputChange}
                            className={inputClass(fe('grupo_sanguineo'))}
                        >
                            <option value="">Sin especificar</option>
                            {['A', 'B', 'AB', 'O'].map(grupo => <option key={grupo} value={grupo}>{grupo}</option>)}
                        </select>
                        <select
                            id="factor_rh"
                            name="factor_rh"
                            aria-label="Factor Rh"
                            value={formData.factor_rh}
                            onChange={handleInputChange}
                            className={inputClass(fe('factor_rh'))}
                        >
                            <option value="">Sin especificar</option>
                            <option value="positivo">Positivo (+)</option>
                            <option value="negativo">Negativo (−)</option>
                        </select>
                    </div>
                    {erroresSangre.map((msg, i) => <p key={i} className={errorClass}>{msg}</p>)}
                </div>
            </FormSection>

            <FormSection title={esPaciente ? 'Afiliación y contacto' : 'Contacto'}>
                {esPaciente && (
                    <TextField
                        className="md:col-span-7"
                        label="EPS"
                        name="eps"
                        list="eps_list"
                        value={formData.eps}
                        onChange={handleInputChange}
                        error={fe('eps')}
                    >
                        <datalist id="eps_list">
                            {epsList.map(e => (
                                <option key={e.id} value={e.nombre} />
                            ))}
                        </datalist>
                    </TextField>
                )}
                {esPaciente && (
                    <SelectField
                        className="md:col-span-5"
                        label="Tipo de afiliación"
                        name="tipo_afiliacion"
                        value={formData.tipo_afiliacion}
                        onChange={handleInputChange}
                        codigo={true}
                        options={tiposAfiliacion}
                        error={fe('tipo_afiliacion')}
                    />
                )}

                <TextField
                    className={spanTelefono}
                    type="tel"
                    label="Teléfono"
                    name="telefono"
                    value={formData.telefono}
                    onChange={handleInputChange}
                    error={fe('telefono')}
                />
                {esPaciente && (
                    <TextField
                        className="md:col-span-5"
                        type="email"
                        label="Email"
                        name="correo"
                        value={formData.correo}
                        onChange={handleInputChange}
                        error={fe('correo')}
                    />
                )}
                {esPaciente && (
                    <div className="md:col-span-4">
                        <span id="zona_label" className={labelClass}>Zona de residencia</span>
                        <div role="radiogroup" aria-labelledby="zona_label" className="flex flex-wrap items-center gap-x-4 gap-y-1 min-h-9">
                            <label htmlFor="zona_urbana" className={radioClass}>
                                <input
                                    type="radio"
                                    id="zona_urbana"
                                    name="zona"
                                    value="02"
                                    checked={formData.zona === '02'}
                                    onChange={handleInputChange}
                                    className="h-4 w-4 accent-primary"
                                />
                                Urbana
                            </label>
                            <label htmlFor="zona_rural" className={radioClass}>
                                <input
                                    type="radio"
                                    id="zona_rural"
                                    name="zona"
                                    value="01"
                                    checked={formData.zona === '01'}
                                    onChange={handleInputChange}
                                    className="h-4 w-4 accent-primary"
                                />
                                Rural
                            </label>
                        </div>
                    </div>
                )}

                {esPaciente && (
                    <TextField
                        className="md:col-span-5"
                        label="Dirección"
                        name="direccion"
                        value={formData.direccion}
                        onChange={handleInputChange}
                        error={fe('direccion')}
                    />
                )}
                {esPaciente && (
                    <SelectField
                        className="md:col-span-4"
                        label="Municipio"
                        name="municipio"
                        value={formData.municipio}
                        onChange={handleInputChange}
                        codigo={false}
                        options={municipios.map(mun => ({
                            codigo: mun.codigo,
                            nombre: `${mun.municipio} - ${mun.departamento}`
                        }))}
                        error={fe('municipio')}
                    />
                )}
                {esPaciente && (
                    <SelectField
                        className="md:col-span-3"
                        label="País de residencia"
                        name="pais_residencia"
                        value={formData.pais_residencia}
                        onChange={handleInputChange}
                        codigo={false}
                        options={paises.map(p => ({ codigo: p.codigo_iso, nombre: p.nombre }))}
                        error={fe('pais_residencia')}
                    />
                )}

                {perfil === 'Acompaniante' && (
                    <TextField
                        className="md:col-span-6"
                        label="Parentesco"
                        id="parentesco"
                        name="Parentesco"
                        list="parentesco_list"
                        value={formData.parentesco}
                        onChange={handleInputChange}
                    >
                        <datalist id="parentesco_list">
                            <option value="Madre" />
                            <option value="Padre" />
                            <option value="Hermano(a)" />
                            <option value="Hijo(a)" />
                            <option value="Esposo(a)" />
                            <option value="Amigo(a)" />
                        </datalist>
                    </TextField>
                )}
            </FormSection>

            <div className="flex justify-end">
                <button
                    type="submit"
                    disabled={loading}
                    className="inline-flex items-center justify-center rounded-md border border-transparent bg-primary py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-titles focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
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
        </>
    );
};

export default FormPersona;

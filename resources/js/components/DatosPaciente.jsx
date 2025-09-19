import React from "react";
import { useState, useEffect } from "react";

const DatosPaciente = ({ pacienteId }) => {
    const [paciente, setPaciente] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Estados para los datos del formulario
    const [formData, setFormData] = useState({
        tipo_documento: '',
        numero_documento: '',
        nombres: '',
        apellidos: '',
        fecha_nacimiento: '',
        pais_id: '',
        municipio_id: '',
        direccion: '',
        telefono: '',
        email: '',
        eps_id: ''
    });

    // Estados para los datos estáticos
    const [documentosTipos, setDocumentosTipos] = useState([]);
    const [paises, setPaises] = useState([]);
    const [municipios, setMunicipios] = useState([]);
    const [epsList, setEpsList] = useState([]);

    useEffect(() => {
        // Cargar datos estáticos del localStorage
        const loadStaticData = () => {
            try {
                const documentosData = JSON.parse(localStorage.getItem('documentos_paciente_data')) || [];
                const paisesData = JSON.parse(localStorage.getItem('paises_data')) || [];
                const municipiosData = JSON.parse(localStorage.getItem('municipios_data')) || [];
                const epsData = JSON.parse(localStorage.getItem('eps_data')) || [];

                setDocumentosTipos(documentosData);
                setPaises(paisesData);
                setMunicipios(municipiosData);
                setEpsList(epsData);
            } catch (error) {
                console.error("Error al cargar datos estáticos:", error);
                setError("Error al cargar datos del formulario");
            }
        };

        loadStaticData();

        // Si hay un pacienteId, cargar los datos del paciente
        if (pacienteId) {
            fetchPacienteData();
        } else {
            setLoading(false);
        }
    }, [pacienteId]);

    const fetchPacienteData = async () => {
        try {
            const response = await fetch(`/api/pacientes/${pacienteId}`);
            if (!response.ok) throw new Error('Error al cargar datos del paciente');
            const data = await response.json();
            setPaciente(data);
            setFormData(data);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

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
                ? `/api/pacientes/${pacienteId}`
                : '/api/pacientes';

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

    if (loading) return <div>Cargando...</div>;
    if (error) return <div className="text-red-600">Error: {error}</div>;

    return (

        <section className="paciente_container max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 bg-background rounded-xl border border-secondary shadow-md mb-4">
            <form onSubmit={handleSubmit} className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {/* Tipo de documento */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Tipo de Documento
                        </label>
                        <select
                            name="tipo_documento"
                            value={formData.tipo_documento}
                            onChange={handleInputChange}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Seleccione...</option>
                            {documentosTipos.map(doc => (
                                <option key={doc.id} value={doc.id}>{doc.nombre}</option>
                            ))}
                        </select>
                    </div>

                    {/* Número de documento */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Número de Documento
                        </label>
                        <input
                            type="text"
                            name="numero_documento"
                            value={formData.numero_documento}
                            onChange={handleInputChange}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    {/* Nombres y Apellidos */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Nombres
                        </label>
                        <input
                            type="text"
                            name="nombres"
                            value={formData.nombres}
                            onChange={handleInputChange}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Apellidos
                        </label>
                        <input
                            type="text"
                            name="apellidos"
                            value={formData.apellidos}
                            onChange={handleInputChange}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    {/* Continuar con el resto de campos... */}
                </div>

                <div className="flex justify-end space-x-3">
                    <button
                        type=""  /*desactivando el evento por ahora*/
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
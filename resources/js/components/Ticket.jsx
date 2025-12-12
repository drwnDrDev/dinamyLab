import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import useAxiosAuth from './hooks/useAxiosAuth';
import BuscarModelo from './BuscarModelo.jsx';


export default function Ticket({ ordenId }) {
    const { axiosInstance, csrfLoaded, error: csrfError } = useAxiosAuth();
    const [totalOrden, setTotalOrden] = useState(0);
    const [orden, setOrden] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [procedimientos, setProcedimientos] = useState([]);
    const baseUrl = window.location.origin;

    const fetchOrden = async (id) => {
        try {

            const res = await axiosInstance.get(`/api/orden/${id}`);
            if (res.status === 200) {
                console.log('✅ Orden obtenida:', res.data);
                return res.data;
            } else {
                console.error('❌ Error al obtener la orden, estado:', res.status);
                setError(`Error al obtener la orden, estado: ${res.status}`);
                return null;
            }
        } catch (err) {
            console.error('❌ Error al obtener la orden:', err);
            setError('Error al obtener la orden');
            return null;
        }
    };


    useEffect(() => {
        // obtener id desde la ruta como en el código original
        const currentUrl = new URL(window.location.href);
        const idFromPath = currentUrl.pathname.split('/');
        const id = idFromPath[2];

        if (id) {
            fetchOrden(id)
                .then((orden) => {
                    if (orden) {
                        setOrden(orden);
                        setProcedimientos(orden.procedimientos || []);
                        setTotalOrden(orden.total || 0);
                        setLoading(false);

                    }
                })
                .catch((err) => {
                    console.error('Error al obtener la orden:', err);
                    setError('Error al obtener la orden');
                    setLoading(false);
                });
        }
    }, []);
    // estados para los catálogos estáticos
    const [lookups, setLookups] = useState({
        cies: {},
        finalidades: {},
        vias_ingreso: {},
        modalidades: {},
        servicios: {},
    });

    // helper para convertir array de {codigo|id, nombre|descripcion} a map
    const toMap = (arr) => {
        if (!Array.isArray(arr)) return {};
        return arr.reduce((acc, it) => {
            const key = it.codigo ?? it.code ?? it.id ?? it.value;
            const label = it.nombre ?? it.name ?? it.descripcion ?? it.label ?? it.descripcion_corta ?? it.descripcion_larga;
            if (key != null) acc[key] = label ?? String(key);
            return acc;
        }, {});
    };

    // cargar catálogos estáticos desde la API
    useEffect(() => {
        const base = window.location.origin;
        const endpoints = {
            cies: `${base}/api/cie10`,
            finalidades: `${base}/api/finalidades`,
            vias_ingreso: `${base}/api/via-ingreso`,
            modalidades: `${base}/api/modalidades-atencion`,
            servicios: `${base}/api/servicios-habilitados`,
        };

        const fetchAll = async () => {
            try {
                const responses = await Promise.all(
                    Object.values(endpoints).map((url) => fetch(url).then((r) => (r.ok ? r.json() : [])))
                );
                const [cies, finalidades, vias_ingreso, modalidades, servicios] = responses;
                setLookups({
                    cies: toMap(cies.data.diagnostico_principal || cies.data.codigoDiagnostico || []),
                    finalidades: toMap(finalidades.data.finalidades || []),
                    vias_ingreso: toMap(vias_ingreso.data || []),
                    modalidades: toMap(modalidades.data.modalidades_atencion || []),
                    servicios: toMap(servicios.data || []),
                });

            } catch (err) {
                console.error('Error cargando catálogos estáticos:', err);
            }
        };

        fetchAll();
        console.log('Catálogos estáticos cargados', lookups);
    }, []);


 const buscarCie = (e) => {
        const codigo = e.target.innerText;
        const descripcion = lookups.cies[codigo] ?? 'Descripción no encontrada';
        alert(<BuscarModelo />);
    }


    return (
    <div className='print:hidden p-6'>

        <h1 className="text-2xl font-bold mb-4">Orden N° {orden?.numero}  </h1>
        <h2>sede {orden?.sede_id} </h2>
        <p className="text-lg">Total de la Orden: ${totalOrden}</p>
        {loading && <p>Cargando...</p>}
        {error && <p className="text-red-500">Error: {error}</p>}
        {!loading && !error && (
            <table className="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr>
                        <th className="py-2 px-4 border-b border-gray-300" title='Nombre del examen o procedimiento'>Examen</th>
                        <th className="py-2 px-4 border-b border-gray-300" title='Estado actual del procedimiento'>Estado</th>
                        <th className="py-2 px-4 border-b border-gray-300" title='diagnostico principal'>Cie 1</th>
                        <th className="py-2 px-4 border-b border-gray-300" title='diagnostico relacionado'>Cie 2</th>
                        <th className="py-2 px-4 border-b border-gray-300" title='codigo finalidad'>Finalidad</th>
                        <th className="py-2 px-4 border-b border-gray-300" title='codigo modalidad'>Modalidad</th>
                        <th className="py-2 px-4 border-b border-gray-300" title='codigo servicio'>Servicio</th>
                        <th className="py-2 px-4 border-b border-gray-300" title='codigo via ingreso'>Vía</th>
                    </tr>
                </thead>
                <tbody>
                    {procedimientos.map((procedimiento) => (
                        <tr key={procedimiento.id} onClick={() => window.location.href = `/procedimientos/${procedimiento.id}`} className="hover:bg-gray-100 cursor-pointer">
                            <td className="py-2 px-4 border-b border-gray-300">{procedimiento.examen.nombre}</td>
                            <td className="py-2 px-4 border-b border-gray-300">{procedimiento.estado}</td>
                            <td className="py-2 px-4 border-b border-gray-300"
                            title={lookups.cies[procedimiento.diagnostico_principal]?? procedimiento.diagnostico_principal}
                            onClick={buscarCie}
                            >{
                                 procedimiento.diagnostico_principal
                            }</td>
                            <td className="py-2 px-4 border-b border-gray-300"

                            title={lookups.cies[procedimiento.diagnostico_relacionado]?? procedimiento.diagnostico_relacionado}
                            onClick={buscarCie}
                            >{
                             procedimiento.diagnostico_relacionado
                            }</td>
                            <td className="py-2 px-4 border-b border-gray-300"
                            title={lookups.finalidades[procedimiento.codigo_finalidad]}>{
                                procedimiento.codigo_finalidad
                            }</td>
                            <td className="py-2 px-4 border-b border-gray-300"
                            title={lookups.modalidades[procedimiento.codigo_modalidad] }>{
                            procedimiento.codigo_modalidad
                            }</td>
                            <td className="py-2 px-4 border-b border-gray-300"
                            title={lookups.servicios[procedimiento.codigo_servicio] }>{
                      procedimiento.codigo_servicio
                            }</td>
                            <td className="py-2 px-4 border-b border-gray-300"
                            title={lookups.vias_ingreso[procedimiento.codigo_via_ingreso]}>{
                            procedimiento.codigo_via_ingreso
                            }</td>

                        </tr>
                    ))}
                </tbody>
            </table>
        )}
        <div className="mt-4">
            <button className="bg-blue-500 text-white px-4 py-2 rounded">Imprimir Ticket</button>
        </div>
        <BuscarModelo />
</div>
);
}

if (document.getElementById('ticket')) {
    document.addEventListener('DOMContentLoaded', () => {
        const root = createRoot(document.getElementById('ticket'));
        root.render(<Ticket ordenId={document.getElementById('ticket').getAttribute('ordenId')} />);
    });
}

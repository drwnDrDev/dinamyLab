import React, { useEffect, useMemo, useState } from 'react';
import axios from 'axios';

const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
};

const apiClient = axios.create({
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
});

apiClient.interceptors.request.use(
    async (config) => {
        if (!document.cookie.includes('XSRF-TOKEN')) {
            await axios.get('/sanctum/csrf-cookie');
        }

        const token = getCsrfToken();
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token;
        }

        return config;
    },
    (error) => Promise.reject(error)
);

const normalizeExamenes = (response) => {
    const data = response?.data;
    if (Array.isArray(data)) {
        return data;
    }
    if (Array.isArray(data?.data)) {
        return data.data;
    }
    if (Array.isArray(data?.data?.examenes)) {
        return data.data.examenes;
    }
    return [];
};

const normalizeTarifas = (response) => {
    const data = response?.data;
    if (Array.isArray(data)) {
        return data;
    }
    if (Array.isArray(data?.data)) {
        return data.data;
    }
    return [];
};

const buildInitialPrecios = (examenes, tarifasMap) => {
    const initial = {};
    examenes.forEach((examen) => {
        initial[examen.id] = tarifasMap[examen.id]?.precio ?? examen.valor ?? '';
    });
    return initial;
};

const TarifasExamenesSede = ({ sedeId }) => {
    const [examenes, setExamenes] = useState([]);
    const [tarifasByExamen, setTarifasByExamen] = useState({});
    const [precios, setPrecios] = useState({});
    const [initialPrecios, setInitialPrecios] = useState({});
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const cambios = useMemo(() => {
        return Object.keys(precios).filter((id) => {
            const current = precios[id];
            const initial = initialPrecios[id];
            return String(current ?? '') !== String(initial ?? '');
        });
    }, [precios, initialPrecios]);

    const cargarDatos = async () => {
        if (!sedeId) {
            setError('No se encontro la sede.');
            setLoading(false);
            return;
        }

        setLoading(true);
        setError('');
        setSuccess('');

        try {
            const [examenesResponse, tarifasResponse] = await Promise.all([
                apiClient.get('/api/examenes'),
                apiClient.get('/api/tarifas', {
                    params: {
                        sede_id: sedeId,
                        tarifable_type: 'examen',
                    },
                }),
            ]);

            const examenesData = normalizeExamenes(examenesResponse);
            const tarifasData = normalizeTarifas(tarifasResponse);

            const tarifasMap = {};
            tarifasData.forEach((tarifa) => {
                if (tarifa?.tarifable_id) {
                    tarifasMap[tarifa.tarifable_id] = tarifa;
                }
            });

            const initial = buildInitialPrecios(examenesData, tarifasMap);

            setExamenes(examenesData);
            setTarifasByExamen(tarifasMap);
            setPrecios(initial);
            setInitialPrecios(initial);
        } catch (err) {
            console.error('Error cargando examenes o tarifas:', err);
            setError('No se pudieron cargar los examenes.');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        cargarDatos();
    }, [sedeId]);

    const handlePrecioChange = (examenId, value) => {
        setPrecios((prev) => ({
            ...prev,
            [examenId]: value,
        }));
    };

    const guardarCambios = async () => {
        if (cambios.length === 0) {
            return;
        }

        setSaving(true);
        setError('');
        setSuccess('');

        try {
            const requests = cambios.map((examenId) => {
                const precio = parseFloat(precios[examenId]);
                if (Number.isNaN(precio)) {
                    return null;
                }

                const tarifaActual = tarifasByExamen[examenId];

                if (tarifaActual?.id) {
                    return apiClient.put(`/api/tarifas/${tarifaActual.id}`, {
                        precio,
                        sede_id: sedeId,
                        tarifable_type: 'examen',
                        tarifable_id: examenId,
                    });
                }

                return apiClient.post('/api/tarifas', {
                    precio,
                    sede_id: sedeId,
                    tarifable_type: 'examen',
                    tarifable_id: examenId,
                });
            });

            await Promise.all(requests.filter(Boolean));
            await cargarDatos();
            setSuccess('Tarifas guardadas correctamente.');
        } catch (err) {
            console.error('Error guardando tarifas:', err);
            setError('No se pudieron guardar las tarifas.');
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="space-y-4">
            <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h3 className="text-lg font-semibold text-gray-900">Tarifas por examen</h3>
                <button
                    type="button"
                    onClick={guardarCambios}
                    disabled={saving || cambios.length === 0}
                    className="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {saving ? 'Guardando...' : 'Guardar cambios'}
                </button>
            </div>

            {error && <p className="text-sm text-red-600">{error}</p>}
            {success && <p className="text-sm text-green-600">{success}</p>}

            {loading ? (
                <p className="text-sm text-gray-600">Cargando examenes...</p>
            ) : (
                <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                    {examenes.map((examen) => {
                        const precioValue = precios[examen.id] ?? '';
                        const changed = String(precioValue ?? '') !== String(initialPrecios[examen.id] ?? '');

                        return (
                            <div
                                key={examen.id}
                                className={`grid grid-cols-2 items-center gap-2 rounded-md border px-4 py-2 ${
                                    changed ? 'border-primary' : 'border-gray-200'
                                }`}
                            >
                                <label className="text-sm font-medium text-gray-800">{examen.nombre}</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value={precioValue}
                                    onChange={(event) => handlePrecioChange(examen.id, event.target.value)}
                                    className="form-input w-full"
                                    placeholder="Precio"
                                />
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
};

export default TarifasExamenesSede;

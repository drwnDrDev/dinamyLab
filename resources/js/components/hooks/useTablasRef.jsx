import { useEffect, useState } from 'react';
import { getCatalogs, loadCatalogs } from '../../lib/catalogs';

const mapTablas = (data) => ({
    modalidades: data.modalidad_atencion ?? [],
    finalidades: data.finalidad_consulta ?? [],
    viasIngreso: data.via_ingreso ?? [],
    tiposAfiliacion: data.tipo_afiliacion ?? [],
    servicios: data.servicios_habilitados ?? [],
    causaAtencion: data.causa_externa ?? [],
});

export const useTablasRef = () => {
    const [tablas, setTablas] = useState(() => mapTablas(getCatalogs()));

    useEffect(() => {
        let cancelled = false;

        loadCatalogs()
            .then(({ data }) => {
                if (!cancelled) setTablas(mapTablas(data));
            })
            .catch(() => {});

        return () => { cancelled = true; };
    }, []);

    return tablas;
};

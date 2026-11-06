import { useEffect, useState } from 'react';
import { getCatalogs, loadCatalogs } from '../../lib/catalogs';

const mapReferencias = (data) => ({
    tiposDocumento: data.documentos_paciente ?? [],
    paises: data.paises ?? [],
    municipios: data.municipios ?? [],
    epsList: data.eps ?? [],
});

export const usePersonaReferencias = () => {
    const [referencias, setReferencias] = useState(() => mapReferencias(getCatalogs()));

    useEffect(() => {
        let cancelled = false;

        loadCatalogs()
            .then(({ data }) => {
                if (!cancelled) setReferencias(mapReferencias(data));
            })
            .catch(() => {});

        return () => { cancelled = true; };
    }, []);

    return referencias;
};

import React from "react";
import SelectField from "./SelectField";
import CieSearchInput from "./CieSearchInput";
import { useTablasRef } from './hooks/useTablasRef';

const DatosOrden = ({ formOrden, onUpdate, error }) => {
    const { modalidades, finalidades, viasIngreso, servicios } = useTablasRef();

    return (
        <section>
            <div className="mb-4">
                <h2 className="text-lg font-semibold text-titles">Datos clínicos de la orden</h2>
                <p className="text-sm text-gray-500 mt-0.5">
                    Información requerida para el reporte RIPS.
                </p>
            </div>
            <div className="grid grid-cols-2 gap-4">
                <SelectField
                    label="Modalidad de Atención"
                    name="modalidad"
                    value={formOrden.modalidad || ''}
                    codigo={true}
                    options={modalidades}
                    onChange={onUpdate}
                    error={error?.modalidad}
                />
                <SelectField
                    label="Servicio Habilitado"
                    name="cod_servicio"
                    value={formOrden.cod_servicio || ''}
                    codigo={true}
                    options={servicios}
                    onChange={onUpdate}
                    error={error?.cod_servicio}
                />
                <CieSearchInput
                    label="CIE Principal"
                    name="cie_principal"
                    value={formOrden.cie_principal || ''}
                    onChange={onUpdate}
                    error={error?.cie_principal}
                />
                <CieSearchInput
                    label="CIE Relacionado"
                    name="cie_relacionado"
                    value={formOrden.cie_relacionado || ''}
                    onChange={onUpdate}
                    error={error?.cie_relacionado}
                />
                <SelectField
                    label="Finalidad de Consulta"
                    name="finalidad"
                    value={formOrden.finalidad || ''}
                    codigo={true}
                    options={finalidades}
                    onChange={onUpdate}
                    error={error?.finalidad}
                />
                <SelectField
                    label="Vía de Ingreso"
                    name="via_ingreso"
                    value={formOrden.via_ingreso || ''}
                    codigo={true}
                    options={viasIngreso}
                    onChange={onUpdate}
                    error={error?.via_ingreso}
                />
            </div>
        </section>
    );
}
export default DatosOrden;


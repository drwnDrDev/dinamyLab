import React from "react";
import { useState, useEffect } from 'react';
import SelectField from "./SelectField";
import { useTablasRef } from './hooks/useTablasRef';
import { useCieCups } from './hooks/useCieCups';

const DatosOrden = ({ formOrden, onUpdate, error }) => {
    const { modalidades, finalidades, viasIngreso, servicios } = useTablasRef();
    const { cie10 } = useCieCups();

    return (
        <section>
            <h2>Datos de la Orden</h2>
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
                <SelectField
                    label="CIE Principal"
                    name="cie_principal"
                    value={formOrden.cie_principal || ''}
                    codigo={true}
                    options={cie10}
                    onChange={onUpdate}
                    error={error?.cie_principal}
                />
                <SelectField
                    label="CIE Relacionado"
                    name="cie_relacionado"
                    value={formOrden.cie_relacionado || ''}
                    codigo={true}
                    options={cie10}
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


import React from "react";
import { useState, useEffect } from 'react';
import SelectField from "./SelectField";
import { useTablasRef} from './hooks/useTablasRef';
import { useCieCups } from './hooks/useCieCups';

const DatosOrden = ({ formOrden, onUpdate}) => {
    const { modalidades, finalidades, viasIngreso, tiposAfiliacion, servicios } = useTablasRef();
    const { cups, cie10 } = useCieCups();


    return (
        <section>
            <h2>Datos de la Orden</h2>
            <div className="grid grid-cols-2 gap-4">
                <SelectField
                    label="Modalidad de Atención"
                    name="modalidad_atencion"
                    value={formOrden.modalidad || ''}
                    options={modalidades}
                    onChange={onUpdate}

                />
                <SelectField
                    label="Finalidad de Consulta"
                    name="finalidad_consulta"
                    value={formOrden.finalidad|| ''}
                    options={finalidades}
                    onChange={onUpdate}
                /> 

                <SelectField
                    label="Vía de Ingreso"
                    name="via_ingreso"
                    value={formOrden.via_ingreso || ''}
                    options={viasIngreso}
                    onChange={onUpdate}
                />
                <SelectField
                    label="Tipo de Afiliación"
                    name="tipo_afiliacion"
                    value={formOrden.tipo_afiliacion || ''}
                    options={tiposAfiliacion}
                    onChange={onUpdate}
                />
                <SelectField
                    label="Servicio Habilitado"
                    name="cod_servicio"
                    value={formOrden.cod_servicio || ''}
                    options={servicios}
                    onChange={onUpdate}
                />
                <SelectField
                    label="CIE Principal"
                    name="cie_principal"
                    value={formOrden.cie_principal || ''}
                    options={cie10}
                    onChange={onUpdate}
                />
                <SelectField
                    label="CIE Relacionado"
                    name="cie_relacionado"
                    value={formOrden.cie_relacionado || ''}
                    options={cie10}
                    onChange={onUpdate}
                />
            </div>
        </section>
    );
                    
}
export default DatosOrden;


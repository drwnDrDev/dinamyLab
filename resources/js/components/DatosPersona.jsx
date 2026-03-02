
import React from 'react';

const DatosPersona = ({ persona }) => {
    if (!persona) return null;

    const calcularEdad = (fechaNacimiento) => {
        try {
            if (!fechaNacimiento) return 'No especificada';
            const hoy = new Date();
            const fechaNac = new Date(fechaNacimiento);
            if (isNaN(fechaNac.getTime())) return 'Fecha inválida';
            let edad = hoy.getFullYear() - fechaNac.getFullYear();
            const mes = hoy.getMonth() - fechaNac.getMonth();
            if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) edad--;
            if (edad < 0 || edad > 150) return 'Edad fuera de rango';
            return `${edad} años`;
        } catch {
            return 'Error al calcular edad';
        }
    };

    const nombreCompleto = [
        persona.primer_nombre,
        persona.segundo_nombre,
        persona.primer_apellido,
        persona.segundo_apellido,
    ].filter(Boolean).join(' ');

    const sexos = { M: 'Masculino', F: 'Femenino', I: 'Intersexual' };

    return (
        <section className="print_paciente py-2 w-full">
            <a
                href={`/personas/${persona.id}`}
                className="group inline-flex items-center gap-1 mb-2 capitalize"
            >
                <h3 className="text-base font-bold text-titles group-hover:text-primary transition-colors">
                    {nombreCompleto || 'Sin nombre'}
                </h3>
                <svg className="w-3.5 h-3.5 text-gray-400 group-hover:text-primary transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>

            <div className="grid grid-cols-2 gap-x-6 gap-y-0.5 text-sm">
                <div className="flex gap-1.5">
                    <span className="text-gray-500 shrink-0">Identificación:</span>
                    <span className="text-text font-medium">
                        {`${persona.tipo_documento?.cod_rips || ''} ${persona.numero_documento || ''}`}
                    </span>
                </div>
                <div className="flex gap-1.5">
                    <span className="text-gray-500 shrink-0">Fecha de atención:</span>
                    <span className="text-text">{new Date().toLocaleDateString('es-CO')}</span>
                </div>
                <div className="flex gap-1.5">
                    <span className="text-gray-500 shrink-0">Sexo:</span>
                    <span className="text-text">{sexos[persona.sexo] || 'No especificado'}</span>
                </div>
                <div className="flex gap-1.5">
                    <span className="text-gray-500 shrink-0">Nacimiento:</span>
                    <span className="text-text">
                        {persona.fecha_nacimiento
                            ? new Date(persona.fecha_nacimiento).toLocaleDateString('es-CO')
                            : 'No especificada'}
                    </span>
                </div>
                <div className="flex gap-1.5">
                    <span className="text-gray-500 shrink-0">Edad:</span>
                    <span className="text-text">
                        {persona.fecha_nacimiento ? calcularEdad(persona.fecha_nacimiento) : 'No especificada'}
                    </span>
                </div>
            </div>
        </section>
    );
};

export default DatosPersona;

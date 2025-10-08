
import React from 'react';

const DatosPersona = ({ persona }) => {
    // Validación de props
    if (!persona) {
        console.warn('DatosPersona: No se proporcionó el objeto persona');
        return null;
    }

    // Función para calcular la edad
    const calcularEdad = (fechaNacimiento) => {
        try {
            if (!fechaNacimiento) return 'No especificada';
            
            const hoy = new Date();
            const fechaNac = new Date(fechaNacimiento);
            
            // Validar que la fecha sea válida
            if (isNaN(fechaNac.getTime())) {
                console.warn('Fecha de nacimiento inválida:', fechaNacimiento);
                return 'Fecha inválida';
            }

            let edad = hoy.getFullYear() - fechaNac.getFullYear();
            const mes = hoy.getMonth() - fechaNac.getMonth();
            
            if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
                edad--;
            }

            // Si la edad es negativa o mayor a 150, probablemente hay un error
            if (edad < 0 || edad > 150) {
                console.warn('Edad calculada fuera de rango:', edad);
                return 'Edad fuera de rango';
            }

            return `${edad} años`;
        } catch (error) {
            console.error('Error al calcular la edad:', error);
            return 'Error al calcular edad';
        }
    };

    // Verificamos que tengamos datos válidos
    console.log('Renderizando DatosPersona con:', persona);
    
    // Si persona es null o undefined, retornamos null
    if (!persona) {
        console.warn('DatosPersona: persona es null o undefined');
        return null;
    }

    return (
        <section className="print_paciente grid grid-cols-2 py-2 border-t-[0.2px] border-b-[0.2px] border-borders w-full">
            <div className="col-span-2 flex gap-2">
                <span className="font-normal">Paciente: </span>
                <h3 className="text-titles font-normal">
                    {`${persona.primer_nombre || ''} ${persona.primer_apellido || ''} ${persona.segundo_nombre || ''} ${persona.segundo_apellido || ''}`}
                </h3>
            </div>
            <div className="w-full grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                <span className="font-normal">Identificación: </span>
                <h3 className="text-titles font-light">
                    {`${persona.tipo_documento?.cod_rips || ''} ${persona.numero_documento || ''}`}
                </h3>
                <span className="font-normal">Sexo: </span>
                <h3 className="text-titles font-light">
                    {(() => {
                        const sexo = persona.sexo;
                        if (!sexo) return 'No especificado';
                        const sexos = {
                            'M': 'Masculino',
                            'F': 'Femenino',
                            'I': 'Intersexual'
                        };
                        return sexos[sexo] || 'Otro';
                    })()}
                </h3>
                <span className="font-normal">Edad: </span>
                <h3 className="text-titles font-light">
                    {persona.fecha_nacimiento ? calcularEdad(persona.fecha_nacimiento) : 'No especificada'}
                </h3>
            </div>
            <div className="grid grid-cols-[auto_1fr] gap-x-4 gap-y-0">
                <span className="font-normal">Fecha de atención: </span>
                <h3 className="text-titles font-light">{new Date().toLocaleDateString('es-CO')}</h3>
                <span className="font-normal">Fecha de Nacimiento: </span>
                <h3 className="text-titles font-light">
                    {persona.fecha_nacimiento ? new Date(persona.fecha_nacimiento).toLocaleDateString('es-CO') : 'No especificada'}
                </h3>
            </div>
        </section>
    );
};

export default DatosPersona;
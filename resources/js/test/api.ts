export type Persona = {
    id?: number;
    tipo_documento:string;
    numero_documento:string;
    nombre: string;
    apellidos: string;
    email: string;
    telefono?: string;
    direccion?: string;
    fecha_nacimiento?: Date;
    dni?: string;
    created_at?: string;
    updated_at?: string;
};

export type Examen = {
    id?: number;
    nombre: string;
    descripcion?: string;
    fecha?: Date;
    duracion?: number; // in minutes
    precio?: number;
    estado?: 'activo' | 'inactivo';
    tipo?: string;
    created_at?: string;
    updated_at?: string;
};

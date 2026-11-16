// Tokens de densidad y estilo compartidos por los campos de formulario.
// Cambiar aquí ajusta todos los formularios que usan TextField / SelectField / FormSection.

export const labelClass = 'block font-medium text-sm text-text mb-1';

export const errorClass = 'text-xs text-red-500 mt-1';

// Usa ring (box-shadow) para evitar el borde nativo del navegador
export const inputClass = (hasError = false) =>
    `h-9 w-full px-3 text-sm text-text bg-white rounded-md border-0 focus:outline-none transition-colors ${
        hasError
            ? 'ring-1 ring-red-400 focus:ring-2 focus:ring-red-500'
            : 'ring-1 ring-borders focus:ring-2 focus:ring-primary'
    }`;

// Normaliza error(es) a una lista de mensajes no vacíos
export const toErrorList = (error) => [].concat(error ?? []).filter(Boolean);

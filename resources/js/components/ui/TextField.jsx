import React from 'react';
import { labelClass, errorClass, inputClass, toErrorList } from './formStyles';

/**
 * Campo de texto con label y errores.
 * - `error` acepta un string o una lista de strings.
 * - `adornment` se posiciona dentro del input, a la derecha (p. ej. un spinner).
 * - `children` se renderiza después del input (p. ej. un <datalist>).
 * - `className` se aplica al contenedor (útil para col-span en grids).
 */
const TextField = ({
    label,
    name,
    id,
    error,
    adornment,
    className = '',
    children,
    ...inputProps
}) => {
    const inputId = id ?? name;
    const errores = toErrorList(error);

    return (
        <div className={className}>
            {label && (
                <label htmlFor={inputId} className={labelClass}>
                    {label}
                </label>
            )}
            <div className="relative">
                <input
                    id={inputId}
                    name={name}
                    className={`${inputClass(errores.length > 0)} ${adornment ? 'pr-9' : ''}`}
                    aria-invalid={errores.length > 0 || undefined}
                    {...inputProps}
                />
                {adornment && (
                    <div className="absolute right-2.5 top-1/2 -translate-y-1/2">{adornment}</div>
                )}
            </div>
            {children}
            {errores.map((msg, i) => (
                <p key={i} className={errorClass}>{msg}</p>
            ))}
        </div>
    );
};

export default TextField;

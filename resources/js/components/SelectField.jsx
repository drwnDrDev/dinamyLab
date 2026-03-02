import React from 'react';

const SelectField = ({
  name,
  label,
  value,
  options = [],
  onChange,
  required = false,
  disabled = false,
  error,
  codigo = false,
}) => {
  const selectedOption = options.find(opt => String(opt.codigo) === String(value));

  return (
    <div>
      <label htmlFor={name} className="block font-medium text-sm text-text mb-1">
        {label} {required && <span className="text-red-500">*</span>}
      </label>

      <div className={`
        flex items-stretch rounded-md border overflow-hidden transition-colors
        focus-within:border-primary focus-within:ring-1 focus-within:ring-primary
        ${error ? 'border-red-400 focus-within:border-red-400 focus-within:ring-red-400' : 'border-borders'}
        ${disabled ? 'opacity-60' : 'bg-white'}
      `}>
        {codigo && (
          <span className="flex items-center justify-center px-2.5 min-w-[2.75rem] bg-secondary border-r border-borders text-xs font-mono font-semibold text-titles shrink-0 select-none">
            {selectedOption?.codigo ?? '—'}
          </span>
        )}

        <select
          id={name}
          name={name}
          className="flex-1 h-9 px-2.5 text-sm text-text bg-transparent focus:outline-none disabled:cursor-not-allowed min-w-0 cursor-pointer capitalize"
          value={value ?? ''}
          onChange={onChange}
          disabled={disabled}
        >
          <option value="">Seleccione una opción</option>
          {Array.isArray(options) && options.map((opt) => (
            <option className="capitalize" key={opt.key ?? opt.codigo} value={opt.codigo}>
              {opt.nombre}
            </option>
          ))}
        </select>
      </div>

      {error && <p className="text-sm text-red-500 mt-1">{error}</p>}
    </div>
  );
};

export default SelectField;

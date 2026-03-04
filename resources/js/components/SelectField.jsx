import React from 'react';

const ChevronDown = () => (
  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M19 9l-7 7-7-7" />
  </svg>
);

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

  const containerClass = [
    'relative flex items-stretch rounded-md overflow-hidden transition-colors',
    error
      ? 'ring-1 ring-red-400 focus-within:ring-2 focus-within:ring-red-500'
      : 'ring-1 ring-borders focus-within:ring-2 focus-within:ring-primary',
    disabled ? 'opacity-60 bg-gray-50' : 'bg-white',
  ].join(' ');

  return (
    <div>
      <label htmlFor={name} className="block font-medium text-sm text-text mb-1">
        {label} {required && <span className="text-red-500">*</span>}
      </label>

      <div className={containerClass}>
        {codigo && (
          <span className="flex items-center justify-center px-2.5 min-w-[2.75rem] bg-secondary border-r border-borders text-xs font-mono font-semibold text-titles shrink-0 select-none">
            {selectedOption?.codigo ?? '—'}
          </span>
        )}

        <select
          id={name}
          name={name}
          className="appearance-none border-none flex-1 w-full h-9 px-2.5 pr-8 text-sm text-text bg-transparent focus:ring-0 cursor-pointer capitalize disabled:cursor-not-allowed min-w-0"
          value={value ?? ''}
          onChange={onChange}
          disabled={disabled}
        >
          <option value="">Seleccione una opción</option>
          {Array.isArray(options) && options.map((opt) => {
            // Truncar nombres largos para evitar que el dropdown se desborde en Windows
            const displayName = opt.nombre && opt.nombre.length > 44
              ? opt.nombre.substring(0, 44) + '…'
              : (opt.nombre || '');
            return (
              <option
                key={opt.key ?? opt.codigo}
                value={opt.codigo}
                title={opt.nombre}
                className="capitalize text-neutral-700 hover:bg-secondary"
              >
                {displayName}
              </option>
            );
          })}
        </select>

        {/* Flecha personalizada — reemplaza la del sistema operativo */}
        <span className="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
          <ChevronDown />
        </span>
      </div>

      {error && <p className="text-sm text-red-500 mt-1">{error}</p>}
    </div>
  );
};

export default SelectField;

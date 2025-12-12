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
  
  return (
    <div>
      <label htmlFor={name} className="block font-medium text-sm text-text">
        {label} {required && <span className="text-danger">*</span>}
      </label>
      
      <select
        id={name}
        name={name}
        className={`text-sm h-9 w-full p-1 border-borders focus:border-primary focus:ring-primary rounded-md uppercase ${error ? 'border-red-500' : ''}`}
        value={value ?? ''}
        onChange={onChange}
        disabled={disabled}
      >
        <option value="">Seleccione una opción</option>
        {Array.isArray(options) && options.map((opt) => (
          <option className='capitalize' key={opt.key ?? opt.codigo} value={opt.codigo}>
            {codigo ? `${opt.codigo} - ${opt.nombre}` : opt.nombre} 
          </option>
        ))}
      </select>
      {error && <div className="text-red-500">{error}</div>}
    </div>
  );
};

export default SelectField;
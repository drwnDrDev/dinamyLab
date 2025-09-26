import React from 'react';

const SelectField = ({
  name,
  label,
  value,
  options = [],
  onChange,
  required = false,
  disabled = false,
  error = null,
}) => {
  // Establecer el valor por defecto si no hay valor seleccionado y hay opciones disponibles
  React.useEffect(() => {
    if (!value && Array.isArray(options) && options.length > 0 && onChange) {
      const defaultOption = options[0];
      const event = {
        target: {
          name: name,
          value: defaultOption.codigo
        }
      };
      onChange(event);
    }
  }, [options, value, onChange, name]);

  return (
    <div>
      <label htmlFor={name} className="block font-medium text-sm text-text">
        {label} {required && <span className="text-danger">*</span>}
      </label>
      {console.log('Options received in SelectField:', options)}
      <select
        id={name}
        name={name}
        className={`text-sm h-9 w-full p-1 border-borders focus:border-primary focus:ring-primary rounded-md uppercase ${error ? 'is-invalid' : ''}`}
        value={value || (options[0]?.codigo || '')}
        onChange={onChange}
        disabled={disabled}
      >
        {Array.isArray(options) && options.map((opt) => (
          <option className='capitalize' key={opt.codigo} value={opt.codigo}>
            {opt.nombre}
          </option>
        ))}
      </select>
      {error && <div className="invalid-feedback">{error}</div>}
    </div>
  );
};

export default SelectField;
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
  codigo = false,
}) => {
  // Solo establecemos el valor por defecto una vez al montar el componente
  React.useEffect(() => {
    if (!value && Array.isArray(options) && options.length > 0 && onChange && !disabled) {
      const defaultOption = options[0];
      const event = {
        target: {
          name: name,
          value: defaultOption.codigo
        }
      };
      // Usamos setTimeout para evitar actualizaciones durante el render
      setTimeout(() => {
        onChange(event);
      }, 0);
    }
  }, []); // Solo se ejecuta al montar el componente

  return (
    <div>
      <label htmlFor={name} className="block font-medium text-sm text-text">
        {label} {required && <span className="text-danger">*</span>}
      </label>
      
      <select
        id={name}
        name={name}
        className={`text-sm h-9 w-full p-1 border-borders focus:border-primary focus:ring-primary rounded-md uppercase ${error ? 'is-invalid' : ''}`}
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
      {error && <div className="invalid-feedback">{error}</div>}
    </div>
  );
};

export default SelectField;
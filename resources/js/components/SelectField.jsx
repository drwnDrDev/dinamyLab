import React from 'react';

const SelectField = ({
  name,
  label,
  value,
  options = [],
  onChange,
  required = false,
  disabled = false,
  placeholder = 'Seleccione una opción...',
  error = null,
}) => {
  return (
    <div className="form-group mb-3">
      <label htmlFor={name} className="form-label">
        {label} {required && <span className="text-danger">*</span>}
      </label>
      <select
        id={name}
        name={name}
        className={`form-select ${error ? 'is-invalid' : ''}`}
        value={value || ''}
        onChange={onChange}
        disabled={disabled}
      >
        <option value="">{placeholder}</option>
        {options.map((opt) => (
          <option key={opt.codigo} value={opt.codigo}>
            {opt.descripcion}
          </option>
        ))}
      </select>
      {error && <div className="invalid-feedback">{error}</div>}
    </div>
  );
};

export default SelectField;
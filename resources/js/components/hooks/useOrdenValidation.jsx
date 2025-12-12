// hooks/useOrderValidation.js
import { useState } from 'react';

// Reglas de validación COMPLETAS para tu laboratorio
const validationRules = {
  numero_orden: {
    required: true,
    validate: (value) => /^[1-9]\d{0,4}$/.test(value),
    message: "Debe ser un número positivo de 1 a 5 dígitos (sin ceros al inicio)"
  },
  
  paciente_id: {
    required: true,
    validate: (value) => value !== null && value > 0,
    message: "Debe seleccionar un paciente válido"
  },
  
  examenes: {
    required: true,
    validate: (examenes) => {
      if (examenes.length === 0) return false;
      
      return true;
    },
    message: "Debe seleccionar al menos un examen"
  },
  
  cod_servicio: {
    required: true,
    message: "Debe seleccionar un servicio válido"
  },

  via_ingreso: {
    required: true,
    message: "Debe seleccionar una vía de ingreso válida"
  },
  
  cie_principal: {
    required: true,
    message: "El diagnóstico principal es obligatorio",
  },

  finalidad: {
    required: true,
    message: "Debe seleccionar una finalidad válida"
  },

  modalidad: {
    required: true,
    message: "Debe seleccionar una modalidad válida"
  },

  abono: {
    required: false,
    validate: (value) => value <= total,
    message: "El abono no puede exceder el total"
  },
  
  total: {
    required: true,
    validate: (value) => value >= 0,
    message: "El total no puede ser negativo"
  }
};

export function useOrderValidation() {
  const [errors, setErrors] = useState({});

  const validateField = (fieldName, value) => {
    const rule = validationRules[fieldName];
    if (!rule) return true;
    
    if (rule.required) {
      if (rule.validate) {
        return rule.validate(value);
      }
      return value !== null && value !== '' && value !== undefined;
    }
    
    return true;
  };

  const validateForm = (formData) => {
    const newErrors = {};
    let isValid = true;

    Object.keys(validationRules).forEach(fieldName => {
      const value = formData[fieldName];
      const rule = validationRules[fieldName];
      
      if (!validateField(fieldName, value)) {
        newErrors[fieldName] = rule.message;
        isValid = false;
      }
    });

    setErrors(newErrors);
    return isValid;
  };

  const clearError = (fieldName) => {
    setErrors(prev => ({ ...prev, [fieldName]: '' }));
  };

  return { errors, validateForm, validateField, clearError };
}

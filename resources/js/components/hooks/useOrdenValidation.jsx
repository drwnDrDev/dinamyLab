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
      
      const examenesValidos = examenes.every(examen => {
        const nombreValido = examen.nombre && examen.nombre.trim().length > 0;
        const precioValido = examen.precio > 0 && examen.precio <= 5000;
        return nombreValido && precioValido;
      });
      
      const dentroDeLimite = examenes.length <= 15;
      const totalValido = examenes.reduce((sum, exam) => sum + exam.precio, 0) <= 10000;
      
      return examenesValidos && dentroDeLimite && totalValido;
    },
    message: "Mínimo 1 examen, precios $1-$5000, máx 15 exámenes, total ≤ $10,000"
  },
  
  cod_servicio: {
    required: true,
    validate: (value) => /^[A-Z]{3}[0-9]{3}$/.test(value) && 
                         (value.startsWith('SER') || value.startsWith('LAB')),
    message: "Formato: SER001 o LAB123"
  },

  via_ingreso: {
    required: true,
    validate: (value) => ['01', '02', '03', '04', '05'].includes(value),
    message: "Debe seleccionar una vía de ingreso válida"
  },
  
  cie_principal: {
    required: true,
    validate: (value) => /^[A-Z][0-9]{2}(\.[0-9])?$/.test(value),
    message: "Formato CIE-10 inválido (ej: A01, B20.9)"
  },

  cie_relacionado: {
    required: false,
    validate: (value) => {
      if (!value) return true; // No es obligatorio
      return /^[A-Z][0-9]{2}(\.[0-9])?$/.test(value);
    },
    message: "Formato CIE-10 inválido (ej: A01, B20.9)"
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

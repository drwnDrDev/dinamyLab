import { useEffect, useState } from 'react';

export const useValidacionNormativa = ({ tipoDocConfig, numeroDocumento, fechaNacimiento }) => {
  const [errores, setErrores] = useState({
    numero_documento: null,
    fecha_nacimiento: null,
  });

  useEffect(() => {
    if (!tipoDocConfig || !numeroDocumento) return;

    const regex = new RegExp(tipoDocConfig.regex_validacion);
    const esValido = regex.test(numeroDocumento);

    setErrores(prev => ({
      ...prev,
      numero_documento: esValido ? null : 'Formato inválido para este tipo de documento',
    }));
  }, [numeroDocumento, tipoDocConfig]);

  useEffect(() => {
    if (!tipoDocConfig || !fechaNacimiento) return;

    const edad = calcularEdad(fechaNacimiento);
    const { edad_minima, edad_maxima } = tipoDocConfig;

    const esValida =
      (!edad_minima || edad >= edad_minima) &&
      (!edad_maxima || edad <= edad_maxima);

    setErrores(prev => ({
      ...prev,
      fecha_nacimiento: esValida
        ? null
        : `Edad no válida para ${tipoDocConfig.nombre} (${edad} años)`,
    }));
  }, [fechaNacimiento, tipoDocConfig]);

  const calcularEdad = (fecha) => {
    const hoy = new Date();
    const nacimiento = new Date(fecha);
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const m = hoy.getMonth() - nacimiento.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
      edad--;
    }
    return edad;
  };

  return errores;
};
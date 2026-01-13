/**
 * Exportaciones de componentes del Sistema de Pre-registro de Citas
 *
 * Importa estos componentes en tu aplicación según necesites:
 *
 * COMPONENTES PÚBLICOS (sin autenticación):
 * - FormPreRegistroCita: Formulario individual
 * - PreRegistroListaCitas: Formulario múltiple (lista)
 * - ConsultarCita: Consulta de estado
 *
 * COMPONENTES AUTENTICADOS (recepción):
 * - RecepcionPreRegistros: Lista de pendientes
 * - ConfirmarPreRegistro: Modal de confirmación
 * - RecepcionCitas: Integrador completo
 *
 * COMPONENTE DEMO:
 * - EjemploSistemaCitas: Sistema completo con navegación
 */

// Componentes públicos
export { default as FormPreRegistroCita } from './FormPreRegistroCita';
export { default as PreRegistroListaCitas } from './PreRegistroListaCitas';
export { default as ConsultarCita } from './ConsultarCita';

// Componentes de recepción
export { default as RecepcionPreRegistros } from './RecepcionPreRegistros';
export { default as ConfirmarPreRegistro } from './ConfirmarPreRegistro';
export { default as RecepcionCitas } from './RecepcionCitas';

// Componente demo/ejemplo
export { default as EjemploSistemaCitas } from './EjemploSistemaCitas';

/**
 * EJEMPLO DE USO EN TU APLICACIÓN:
 *
 * // En tu archivo de rutas o app.jsx
 * import {
 *     FormPreRegistroCita,
 *     PreRegistroListaCitas,
 *     ConsultarCita,
 *     RecepcionCitas,
 *     EjemploSistemaCitas
 * } from './components/citas';
 *
 * import FormPersona from './components/FormPersona';
 *
 * // Rutas públicas
 * <Route path="/pre-registro" element={<FormPreRegistroCita />} />
 * <Route path="/pre-registro-multiple" element={<PreRegistroListaCitas />} />
 * <Route path="/consultar-cita" element={<ConsultarCita />} />
 *
 * // Ruta de recepción (protegida)
 * <Route path="/recepcion/citas" element={
 *     <RecepcionCitas FormPersona={FormPersona} />
 * } />
 *
 * // Demo completa
 * <Route path="/demo-citas" element={
 *     <EjemploSistemaCitas FormPersona={FormPersona} />
 * } />
 */

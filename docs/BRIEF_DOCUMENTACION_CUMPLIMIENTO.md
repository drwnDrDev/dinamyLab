# Brief: paquete documental de cumplimiento de DinamyLAB

**Para:** Claude Cowork · **Fecha:** 2026-11-17 · **Estado técnico de referencia:** rama `desarrollo` (PR #86) y [CHECKLIST_DEUDA_TECNICA.md](./CHECKLIST_DEUDA_TECNICA.md)

## 1. Qué te pedimos

Redacta el paquete de documentos que la Secretaría de Salud y el cliente (laboratorio clínico) exigen antes de poner DinamyLAB en producción. Son **9 documentos** (sección 5). Cada uno debe poder entregarse como archivo independiente (Word o PDF), con portada, control de versiones y espacio para firmas.

Reglas para todo el paquete:

1. **No afirmes capacidades que la aplicación no tiene.** La sección 4 dice qué está implementado, qué está en proceso y qué no existe. Lo que está en proceso se redacta como compromiso con fecha (`[FECHA COMPROMISO]`) o como control compensatorio manual, nunca como hecho. Ante la Secretaría, un documento que promete algo que no se puede demostrar es peor que uno que reconoce una brecha con plan.
2. **Marca todo dato que falte** con `[COMPLETAR: qué falta]` y reúnelos al final de cada documento en una tabla «Datos por completar». La sección 7 lista los que ya sabemos que faltan.
3. **Idioma:** español de Colombia, registro formal pero claro. Los usuarios del laboratorio no son técnicos: el manual y la contingencia deben entenderse sin saber de sistemas.
4. **Normas:** cita solo las que apliquen y **verifica su vigencia** antes de citarlas (sección 3). Si una norma te genera duda, márcala `[VALIDAR CON ASESOR LEGAL]`. Los documentos legales (contrato, política de datos) deben llevar la nota «Borrador sujeto a revisión jurídica».
5. **Coherencia entre documentos:** los mismos tiempos (SLA, RPO, RTO, retención), roles y nombres en todos. Define esos valores una vez (sección 6) y reutilízalos.
6. **Plantilla por laboratorio:** el proveedor tiene varios clientes (una instalación por laboratorio). Redacta cada documento como plantilla con los datos del laboratorio en variables (`{{LABORATORIO_RAZON_SOCIAL}}`, `{{LABORATORIO_NIT}}`, `{{SEDES}}`, `{{DOMINIO}}`…) y entrega además la versión diligenciada para el primer cliente si te damos sus datos.
7. **Proveedor y firmas también en plantilla:** no preguntes por la naturaleza jurídica ni por quién firma. Usa `{{PROVEEDOR_NOMBRE_O_RAZON_SOCIAL}}`, `{{PROVEEDOR_TIPO_PERSONA}}` (natural o jurídica), `{{PROVEEDOR_NIT_O_CC}}`, `{{PROVEEDOR_REPRESENTANTE}}`, `{{PROVEEDOR_DIRECCION}}`, `{{PROVEEDOR_CORREO}}` y un bloque de firmas con nombre, documento, cargo y fecha para cada parte. Redacta el contrato de forma que sirva para persona natural o para sociedad (p. ej. «EL PROVEEDOR» como parte definida) sin reescribir cláusulas.

## 2. El producto

**DinamyLAB** es una aplicación web (SaaS) para laboratorios clínicos pequeños en Colombia. Registra pacientes, órdenes de exámenes, resultados y genera el reporte de resultados (pantalla, impresión y PDF) y los RIPS.

| Aspecto | Hecho verificado en el código |
|---|---|
| Tecnología | Laravel 13 (PHP 8.5), vistas Blade con componentes React, MySQL 8.4 |
| Modelo de despliegue | **Una instalación, una base de datos y un almacenamiento por laboratorio** (no se mezclan datos de clientes). Cada instancia rechaza peticiones a dominios ajenos (middleware `EnsureInstance`, responde 503) y tiene su propia cookie de sesión |
| Hosting | Hosting compartido con cPanel, despliegue por Git con versión etiquetada. `[COMPLETAR: proveedor de hosting, país del centro de datos]` |
| Acceso | Usuario y contraseña (bcrypt, 12 rondas), sesiones guardadas en base de datos, protección CSRF, bloqueo temporal tras 5 intentos fallidos de inicio de sesión. **No** hay doble factor |
| HTTPS | Certificado AutoSSL del hosting |
| Multi-sede | Un laboratorio puede tener varias sedes; cada usuario solo opera en las sedes asignadas |

### Módulos

| Módulo | Qué hace |
|---|---|
| Personas | Registro de pacientes, pagadores y acompañantes: identificación, sexo, fecha de nacimiento, grupo sanguíneo y Rh, contacto, dirección, afiliación (EPS, tipo de usuario). Registro compartido entre sedes |
| Órdenes | Orden de exámenes por paciente, con número consecutivo por sede, diagnósticos CIE-10, tarifas por convenio o sede y remisiones a otros laboratorios (con estado). Casilla «pendiente por muestra» |
| Procedimientos | Un procedimiento por examen de la orden, con estado: pendiente, en proceso, terminado, entregado, anulado |
| Resultados | Captura por examen o por lotes (un examen para varios pacientes), validación por tipo de dato, valores de referencia por sexo y edad con alerta de fuera de rango |
| Corrección de resultados | Solo el bacteriólogo (rol `prestador`). Exige motivo y guarda valor anterior, valor nuevo, usuario y fecha en un historial (`resultado_cambios`) visible en pantalla |
| Reporte | Reporte por orden, por examen o historia del paciente; PDF descargable con logo, datos del paciente, resultados con referencia y firma del bacteriólogo |
| RIPS | Exportación JSON de RIPS por procedimiento |
| Catálogos | CUPS, CIE-10, EPS, municipios, exámenes con parámetros y valores de referencia, categorías, convenios, tarifas |
| Administración | Sedes, empleados y roles, convenios, tarifas, resoluciones de facturación, activación de catálogos |

### Roles

| Acción | `admin` | `prestador` (bacteriólogo) | `agente` (recepción) | `contable` |
|---|:-:|:-:|:-:|:-:|
| Ver y buscar pacientes | Sí | Sí | Sí | No |
| Crear, editar y borrar (lógico) pacientes | Sí | Sí | No | No |
| Ver órdenes | Sí | Sí | Sí | No |
| Crear y editar órdenes | Sí | Sí | Sí | No |
| Borrar (lógico) órdenes | Sí | Sí | No | No |
| Cambiar estado de procedimientos | Sí | Sí | No | No |
| Ver, imprimir y descargar resultados | Sí | Sí | Sí | No |
| **Registrar y corregir resultados** | **No** | **Sí** | **No** | No |
| Reportes y descarga RIPS de su sede | Sí | Sí | Sí | No |
| Ver facturas de la sede | Sí | No | No | Sí |
| Configuración del sistema | Sí | No | No | No |

Fuente completa: [MATRIZ_ROLES_PERMISOS.md](./MATRIZ_ROLES_PERMISOS.md). Los borrados son lógicos (el registro se conserva).

## 3. Marco normativo a considerar

Verifica la vigencia y el alcance de cada una antes de citarla:

| Norma | Para qué documento |
|---|---|
| Ley 1581 de 2012 (protección de datos personales) y Decreto 1377 de 2013, compilado en el Decreto 1074 de 2015 | Política de datos, autorización, contrato (transmisión de datos), seguridad |
| Datos de salud como **datos sensibles** (art. 5 y 6 de la Ley 1581): autorización explícita, informar que es facultativo responder | Política de datos y formato de autorización |
| Registro Nacional de Bases de Datos (SIC): obligación del **laboratorio** como responsable | Política de datos (nota para el laboratorio) |
| Transmisión de datos responsable → encargado (Decreto 1377, art. 25, hoy en el Decreto 1074) y transferencia internacional si el servidor está fuera de Colombia (Ley 1581, art. 26) | Contrato (anexo de transmisión) |
| Resolución 1995 de 1999 (historia clínica) y Resolución 839 de 2017 (retención y disposición final) | Ficha técnica (bloqueo del registro), backup (retención) |
| Ley 2015 de 2020 (historia clínica electrónica interoperable) | Ficha técnica (alcance: DinamyLAB no es una HCE completa) |
| Resolución 3100 de 2019 (habilitación de servicios de salud): estándar de historia clínica y registros | Ficha técnica, contingencia |
| Ley 527 de 1999 y Decreto 2364 de 2012 (mensajes de datos y firma electrónica) | Ficha técnica (flujo de firma) |
| Resolución vigente de RIPS (verificar: Resolución 2275 de 2023 y sus modificaciones) | Ficha técnica |
| Ley 1273 de 2009 (delitos informáticos) | Seguridad, gestión de incidentes |
| Ley 1480 de 2011 (Estatuto del Consumidor) y Código de Comercio | Contrato (en lo que aplique entre empresas) |

**Roles legales:** el **laboratorio** es el *Responsable del tratamiento* de los datos de sus pacientes. El **proveedor de DinamyLAB** es el *Encargado del tratamiento*: trata los datos solo por cuenta del laboratorio y según sus instrucciones. Esta distinción tiene que ser la misma en el contrato, la política y el documento de seguridad.

## 4. Estado real de la aplicación frente a cada exigencia

Usa esta tabla para decidir qué se afirma como hecho y qué se redacta como compromiso. Los IDs remiten al checklist técnico.

| Exigencia | Implementado | En proceso o con brecha |
|---|---|---|
| Control de acceso | Roles y permisos por acción; control por sede; aislamiento por instancia; bloqueo tras intentos fallidos; borrado lógico | Sin doble factor. Sin política de contraseñas configurada más allá del mínimo de Laravel. `[COMPLETAR: confirmar si se exige cambio periódico]` |
| Firma del resultado | El resultado lo registra y valida un `prestador`; el reporte y el PDF muestran nombre, documento y **firma escaneada (imagen)** del bacteriólogo | No es firma digital certificada ni firma electrónica con verificación. El «Registro médico» del reporte hoy muestra una cédula, no el registro profesional (DT-20, decisión D-09) |
| Bloqueo del registro | Toda corrección exige motivo y queda en el historial con valor anterior y nuevo; nada se borra físicamente | **No hay bloqueo**: un resultado se puede corregir en cualquier momento, incluso después de entregado. Decisión D-11 abierta (ver checklist) |
| Bitácora de auditoría | Solo las correcciones de resultados (`resultado_cambios`) y el registro de descarga de PDF en el log | **No** se registran accesos a historias, consultas, creación o edición de pacientes y órdenes, exportaciones RIPS, inicios de sesión ni cambios de rol (AU-01, AU-02, AU-04) |
| Autorización de datos (Habeas Data) | — | **No existe** la casilla ni el registro de la autorización en el alta del paciente (AU-05). No hay páginas de política de privacidad (AU-06). Hasta que exista, el control es el **formato en papel** firmado por el paciente |
| Derechos del titular (consulta, rectificación, supresión) | El laboratorio puede consultar y corregir los datos del paciente en la aplicación | No hay canal ni flujo en la aplicación: se atiende por correo o en el laboratorio, con procedimiento manual |
| Cifrado | HTTPS en tránsito; contraseñas con hash | Datos de pacientes **sin cifrado en reposo** en la base (AU-08) |
| Backup | Runbook con `mysqldump` diario | Hoy se guarda en el mismo servidor, sin cifrar, sin copia externa, sin incluir archivos (firmas, logos) y sin pruebas de restauración (DT-23) |
| Gestión de incidentes | — | No hay procedimiento escrito ni monitoreo automático |
| Contingencia (digitación posterior) | Se puede crear la orden y capturar resultados después | La fecha de atención del procedimiento se guarda como la fecha de digitación, no la real (DT-24). Hasta corregirlo, el formato en papel es el soporte de la fecha real |
| Notificación al paciente por correo | — | No implementado (frente NT) |

Lo que está en proceso tiene fecha objetivo `[COMPLETAR]`. En cada documento, la brecha se presenta con: control compensatorio mientras tanto, responsable y fecha.

## 5. Documentos a producir

### 5.1 Ficha técnica

Una o dos páginas para la Secretaría y el cliente: nombre y versión, proveedor, tipo (SaaS web), módulos, arquitectura (diagrama simple: navegador → HTTPS → servidor de aplicación → base de datos por laboratorio), requisitos del cliente (navegador actualizado, conexión a internet `[COMPLETAR: ancho de banda mínimo]`, impresora), roles, normas que atiende, hosting y ubicación de los datos, soporte.

### 5.2 Manual de usuario y de operación

Dirigido al personal del laboratorio. Por rol: iniciar sesión y elegir sede, registrar paciente, crear orden (con exámenes, remisiones y «pendiente por muestra»), capturar resultados (uno y por lotes), **validar y firmar**, corregir un resultado (motivo obligatorio, historial), imprimir y descargar el reporte, entregar, descargar RIPS. Sección propia: **flujo de firma y bloqueo del registro** tal como funciona hoy (ver sección 4) y cómo cambiará cuando se decida D-11. Para el administrador: sedes, empleados, roles, tarifas y convenios. Dejar marcadores `[CAPTURA: pantalla X]` donde vayan imágenes.

### 5.3 Contrato o licencia de uso SaaS y términos de servicio

Contrato entre el proveedor y el laboratorio. Debe cubrir: objeto (licencia de uso, no venta del software; propiedad intelectual del proveedor), alcance de módulos, precio y forma de pago `[COMPLETAR]`, duración y renovación, obligaciones de las partes (el laboratorio responde por la exactitud de los datos clínicos, los valores de referencia y la validación profesional de los resultados; el proveedor responde por la disponibilidad y seguridad de la plataforma), niveles de servicio (remite al documento 5.4), limitación de responsabilidad, confidencialidad, **propiedad de los datos del laboratorio**, **anexo de transmisión de datos personales** (encargado del tratamiento: finalidades, instrucciones, medidas de seguridad, subencargados como el hosting, notificación de incidentes, devolución y borrado al terminar), entrega de los datos al terminar el contrato (formato y plazo `[COMPLETAR]`), terminación, ley aplicable y solución de controversias. Términos de servicio cortos para los usuarios finales (uso aceptable, cuentas personales e intransferibles).

### 5.4 Soporte y mantenimiento (SLA)

Canales, horarios, clasificación de incidentes por severidad con tiempos de respuesta y de solución, disponibilidad objetivo mensual, ventanas de mantenimiento, política de actualizaciones (versiones etiquetadas, primero una instancia y luego las demás, aviso previo), qué no cubre el soporte, escalamiento. Valores en la sección 6.

### 5.5 Plan de contingencia

Dos partes:

- **Para el laboratorio:** cómo detectar la caída, a quién avisar, operar en papel con los formatos que debes diseñar (registro de paciente, orden, hoja de resultados, entrega de resultados y **autorización de tratamiento de datos**), cómo entregar resultados urgentes durante la caída, y la **digitación posterior**: quién digita, cómo se marca que el registro viene de contingencia, cómo se conserva el papel como soporte y cómo se concilian las cantidades. Incluir la nota de DT-24 sobre la fecha.
- **Para el proveedor:** qué hace ante una falla del servidor, de la base de datos, del hosting o de un despliegue fallido; restauración desde backup; comunicación al laboratorio; registro posterior del incidente.

### 5.6 Política de backup y recuperación

Qué se respalda (base de datos y archivos del laboratorio: firmas, logos), frecuencia, retención, lugar (fuera del servidor de producción), cifrado, quién tiene acceso, pruebas periódicas de restauración con registro, RPO y RTO (sección 6). Describe el procedimiento objetivo y declara en una nota lo que todavía no está implementado (DT-23).

### 5.7 Política de seguridad de la información

Control de acceso (alta, cambio y baja de usuarios, cuentas personales, roles mínimos necesarios, revisión periódica de usuarios), contraseñas, gestión de sesiones, seguridad en el desarrollo y el despliegue (secretos fuera del código, revisión de cambios, versiones etiquetadas), **bitácora de auditoría** (qué registra hoy y qué registrará, retención, quién la consulta), **gestión de incidentes** (detección, clasificación, contención, notificación al laboratorio en `[N horas]`, notificación a la SIC cuando corresponda, lecciones aprendidas, formato de reporte de incidente), uso aceptable para el personal del laboratorio.

### 5.8 Política de tratamiento de datos personales (Ley 1581)

Dos piezas:

- **Política del laboratorio** como Responsable (plantilla que el laboratorio adopta): responsable y datos de contacto, finalidades (atención, resultados, facturación, RIPS y reportes obligatorios), datos sensibles de salud, derechos del titular, procedimiento y plazos para consultas (10 días hábiles) y reclamos (15 días hábiles) `[VALIDAR CON ASESOR LEGAL]`, área responsable, vigencia, menores de edad.
- **Formato de autorización** para el paciente (y para el representante del menor), en versión papel y en texto corto para la pantalla de alta cuando exista AU-05. Indicar versión y fecha del aviso: la aplicación guardará qué versión aceptó cada paciente.
- Nota para el proveedor como Encargado: sus obligaciones y cómo apoya al laboratorio a responder derechos del titular.

### 5.9 Evidencia de capacitación

Por laboratorio: temario por rol (con duración), acta de capacitación (fecha, lugar o medio, capacitador, temas, compromisos), lista de asistencia (nombre, documento, cargo, rol en el sistema, firma), evaluación corta de comprensión y constancia de entrega del manual y de la política de seguridad. El temario debe incluir manejo de datos sensibles y el plan de contingencia.

## 6. Valores base a usar (confirmados el 2026-11-17)

| Concepto | Valor propuesto | Comentario |
|---|---|---|
| Horario de soporte | Lunes a viernes 7:00–18:00 y sábados 7:00–12:00 (hora de Colombia) | `[COMPLETAR: confirmar]` |
| Canales | Correo `[COMPLETAR]`, WhatsApp `[COMPLETAR]`, teléfono para severidad 1 | |
| Severidad 1 (sistema caído o resultados no accesibles) | Respuesta 1 h, solución o contingencia 4 h, en horario de soporte | |
| Severidad 2 (función importante falla, hay alternativa) | Respuesta 4 h, solución 2 días hábiles | |
| Severidad 3 (consulta o falla menor) | Respuesta 1 día hábil, solución en la siguiente versión | |
| Disponibilidad objetivo | 99 % mensual, sin contar mantenimientos avisados | Hosting compartido: no prometer más |
| Mantenimiento | Fuera de horario, con aviso de 48 h | |
| Backup | Base de datos diario (y antes de cada actualización), archivos semanal | |
| Retención de backups | 7 diarios, 4 semanales, 12 mensuales | La retención de la historia clínica (Res. 839/2017) es responsabilidad del laboratorio y se cumple con la base activa más la entrega de datos al terminar el contrato `[VALIDAR CON ASESOR LEGAL]` |
| RPO | 24 horas | Pérdida máxima: lo digitado desde el último backup; el papel de contingencia lo cubre |
| RTO | 8 horas hábiles | |
| Notificación de incidentes de seguridad al laboratorio | 24 horas desde la detección | |
| Bitácora de auditoría (cuando exista) | Retención `[COMPLETAR: decisión D-04]` | |

## 7. Datos que debes pedirnos

| Dato | Para |
|---|---|
| Correo, WhatsApp y teléfono de soporte | SLA, ficha |
| Proveedor de hosting, ubicación del centro de datos y si hay subencargados (correo, almacenamiento de backup) | Contrato, backup, datos |
| Laboratorio: razón social, NIT, código de prestador (REPS), sedes, representante legal, dominio | Todos |
| Precio, forma de pago, duración del contrato | Contrato |
| Plazo y formato de entrega de datos al terminar el contrato | Contrato |
| Fechas compromiso de AU-02, AU-05, DT-23, DT-24 y D-11 | Seguridad, datos, backup, contingencia, manual |
| Fecha, lugar y asistentes de la capacitación | Evidencia de capacitación |
| Logo del proveedor y del laboratorio | Portadas |

## 8. Entregables y orden sugerido

1. Tabla de valores de la sección 6 en una página (ya confirmada) — todo lo demás la referencia.
2. Contrato con anexo de transmisión de datos y términos de servicio.
3. Política de tratamiento de datos y formato de autorización.
4. Política de seguridad (incluye incidentes y bitácora).
5. Backup y recuperación.
6. Contingencia con formatos en papel.
7. SLA de soporte y mantenimiento.
8. Ficha técnica.
9. Manual de usuario.
10. Paquete de capacitación (temario, acta, lista de asistencia, evaluación).

Al final, una **matriz de cumplimiento**: cada exigencia de la Secretaría → documento y sección que la atiende → estado (cumple / cumple con control compensatorio / compromiso con fecha).

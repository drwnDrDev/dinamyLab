# Informe: caché de datos en el frontend (localStorage)

**Fecha:** 2026-11-05
**Alcance:** `resources/js/**` y el endpoint `/api/static-data-for-frontend`
**Método:** lectura del código, medición de tamaños sobre una BD sembrada y prueba de los flujos de escritura/lectura.

---

> **Nota de lectura:** las secciones 1 a 9 describen el estado **previo** a la
> implementación de la ruta A + B + D. El estado actual está en la sección 10.

## 1. Resumen ejecutivo

La aplicación usa **localStorage como caché de catálogos** (países, municipios, CUPS, CIE10,
EPS, tipos de documento, finalidades, etc.). No cachea datos de pacientes: solo catálogos.

Funciona, pero hoy conviven **tres mecanismos distintos** sobre las **mismas claves**, sin
versión de esquema y sin invalidación real:

| Mecanismo | Dónde | Cómo decide refrescar |
|---|---|---|
| Cargador central con TTL | `obtenerStaticos.js` | 24 h desde `frontend_data_last_update` |
| Caché perezosa por hook | `useTablasRef.jsx`, `useCieCups.jsx` | solo si la clave está vacía (nunca refresca) |
| Lectura al importar el módulo | `variables.js`, `convenioCreate.jsx`, `crearOrdenes.js` | nunca; lee una vez al cargar el bundle |

El resultado es un caché **inconsistente**: la misma clave puede guardar un array o un sobre
JSON (`{data: ...}`) según quién la escribió, hay claves duplicadas por errores de tipeo, y
un componente **sobrescribe el catálogo CIE10 con 10 elementos**.

**Nada de esto afecta el aislamiento multi-instancia:** `localStorage` es por origen
(esquema + host + puerto), así que dos instancias en dominios distintos no se ven entre sí.
El riesgo es de **consistencia y rendimiento**, no de fuga entre clientes.

---

## 2. Inventario de claves

### 2.1 Escritores

| Clave | La escribe | Contenido |
|---|---|---|
| `frontend_data_last_update` | `obtenerStaticos.js:30` | ISO timestamp |
| `documentos_paciente_data` | `obtenerStaticos.js:39` | array |
| `documentos_pagador_data` | `obtenerStaticos.js:39` | array |
| `paises_data` | `obtenerStaticos.js:39` | array |
| `municipios_data` | `obtenerStaticos.js:39` | array |
| `eps_data` | `obtenerStaticos.js:39` | array |
| `cups_data` | `obtenerStaticos.js:39`, `useCieCups.jsx:18` | array **o** `{data:[...]}` |
| `default_cies_data` | `obtenerStaticos.js:39`, `Setup.jsx:122`, `useCieCups.jsx:18`, **`CieSearchInput.jsx:16`** | array completo **o** 10 recientes |
| `servicios_habilitados_data` | `obtenerStaticos.js:39`, `Setup.jsx`, `useTablasRef` | array / sobre |
| `via_ingreso_data` | `obtenerStaticos.js:39`, `useTablasRef` | array / sobre |
| **`vias_ingreso_data`** | `Setup.jsx:6` | duplicado con nombre distinto de `via_ingreso_data` |
| `modalidad_atencion_data` | `obtenerStaticos.js:39`, `useTablasRef` | array / sobre |
| **`tipos_atencion_data`** | `Setup.jsx:10` | duplicado de `modalidad_atencion_data` |
| `finalidad_consulta_data` | `obtenerStaticos.js:39`, `Setup.jsx`, `useTablasRef` | array / sobre |
| `causas_externas_data` | `Setup.jsx:9` | **nadie más la escribe ni la lee** |
| `tipos_afiliacion_data` | `Setup.jsx:11`, `useTablasRef` | nunca la escribe el cargador central |
| `tipos_documento_data` | `api.js:94` | solo bajo demanda |
| `paises` | `utils/paisesLocalStorage.js` (código muerto) | formato legacy |

### 2.2 Lectores

| Clave leída | Quién | Clave real escrita | ¿Coincide? |
|---|---|---|---|
| `paises_data` | `usePersonaReferencias`, `ConvenioForm`, `convenioCreate`, `crearOrdenes` | `paises_data` | ✅ |
| `paises` | `variables.js:32` | `paises_data` | ❌ **siempre `[]`** |
| `municipios_data` | `usePersonaReferencias`, `ConvenioForm`, `convenioCreate`, `crearOrdenes` | `municipios_data` | ✅ |
| `municipios` | `variables.js:31` | `municipios_data` | ❌ **siempre `[]`** |
| `documentos_paciente_data` | `usePersonaReferencias` | `documentos_paciente_data` | ✅ |
| `documentos_paciente` | `variables.js:30` | `documentos_paciente_data` | ❌ **siempre `[]`** |
| `eps_data` | `usePersonaReferencias`, `crearOrdenes` | `eps_data` | ✅ |
| `eps` | `variables.js:33` | `eps_data` | ❌ **siempre `[]`** |
| `tipos_documento_data` | `crearOrdenes.js:21` | `tipos_documento_data` (solo `api.js`) | ⚠️ puede no existir → **crash** |
| `default_cies_data` | `crearExamenes.js:11`, `CieSearchInput`, `Setup` | colisión de significado | ⚠️ ver H-01 |

---

## 3. Cómo funciona hoy

```
dashboard / sedes/select
        │  @vite('resources/js/obtenerStaticos.js')
        ▼
DOMContentLoaded → checkAndLoadData()
        │
        ├─ ¿hay frontend_data_last_update y tiene < 24 h?  → no hace nada
        │
        └─ si no → fetch('/api/static-data-for-frontend')
                   ├─ guarda el timestamp        (ANTES de los datos)
                   └─ guarda 11 claves *_data
```

Puntos clave:

- El cargador **solo se incluye en 2 vistas** (`dashboard.blade.php:277`,
  `sedes/select.blade.php:19`). `ordenes/create.blade.php` monta el formulario de orden
  pero **no** incluye el cargador.
- El endpoint devuelve **13 consultas completas** en una sola respuesta y **no tiene caché
  de servidor** (`FrontendDataController.php:34-93`).
- El endpoint está **fuera del grupo autenticado** (`routes/api.php:16`) y **sin rate limit**.
- No hay `ETag`, `Last-Modified`, `Cache-Control` ni versión de esquema.

---

## 4. Mediciones

Sobre una BD sembrada con el catálogo real (`migrate` + `db:seed`):

| Catálogo | Filas | JSON |
|---|---:|---:|
| CUPS (`codigo_cups`) | 10.015 | **3,15 MB** |
| CIE10 (`codigo_diagnosticos`) | 12.634 | **5,38 MB** |
| Municipios | 1.123 | 73 KB |
| Países | 249 | 14 KB |
| EPS | 49 | 2 KB |
| **Total si todo estuviera activo** | ~24.000 | **≈ 8,6 MB** |

- Presupuesto típico de `localStorage`: **≈ 5 MB por origen** (Chrome, Safari, Firefox).
- Con el seed de demo (19 CUPS y 9 CIE activos) el payload es **≈ 0,1 MB**: hoy no se
  nota el problema.
- El endpoint devuelve **todos los registros con `activo = true`, sin límite**. Si el
  cliente activa el catálogo completo desde *Setup*, el navegador intentará guardar
  ~8,6 MB → `QuotaExceededError`.
- Cada fila de CUPS pesa ~330 bytes y cada CIE ~446 bytes **porque se serializa el modelo
  completo** (todas las columnas + timestamps). Un `select('codigo','nombre')` reduciría
  el payload entre 60 % y 70 %.

---

## 5. Hallazgos

| # | Severidad | Hallazgo | Ubicación | Escenario que lo sufre el usuario |
|---|---|---|---|---|
| **H-01** | 🔴 Alta | `CieSearchInput` usa `default_cies_data` como "recientes" y la trunca a 10 elementos, **sobrescribiendo el catálogo CIE10** | `CieSearchInput.jsx:5,16` | Un usuario elige un CIE en una orden; el formulario de exámenes (`crearExamenes.js:11`) pasa a mostrar solo 10 diagnósticos durante 24 h |
| **H-02** | 🔴 Alta | El timestamp se guarda **antes** de escribir los datos | `obtenerStaticos.js:30` | Si una escritura falla, el caché queda marcado como "fresco" 24 h con datos incompletos |
| **H-03** | 🔴 Alta | `crearOrdenes.js` hace `JSON.parse(...).pacientes` sobre una clave que puede no existir | `crearOrdenes.js:21` | `TypeError: Cannot read properties of null` al cargar la página en un navegador limpio |
| **H-04** | 🟠 Media | `variables.js` lee las claves sin el sufijo `_data` | `variables.js:30-33` | `appState.tiposDocumento/paises/municipios/eps` siempre vacíos |
| **H-05** | 🟠 Media | Los hooks guardan el **sobre** de la respuesta (`{message, data:{...}}`) en claves que el cargador guarda como array | `useTablasRef.jsx:22`, `useCieCups.jsx:18` | El mismo catálogo tiene dos formas distintas según quién lo escribió; un consumidor hace `.map` sobre un objeto |
| **H-06** | 🟠 Media | Claves duplicadas por nombre: `vias_ingreso_data` vs `via_ingreso_data`, `tipos_atencion_data` vs `modalidad_atencion_data`; `causas_externas_data` y `tipos_afiliacion_data` no las escribe el cargador | `Setup.jsx:6,9,10,11` | *Setup* y los formularios leen listas vacías o desactualizadas |
| **H-07** | 🟠 Media | No hay manejo de `QuotaExceededError` ni límite de tamaño; el `try/catch` global deja datos parciales | `obtenerStaticos.js:19-47` | Con el catálogo completo activo, la caché queda a medias y nadie se entera |
| **H-08** | 🟠 Media | El endpoint es **público, sin autenticación y sin rate limit**, y ejecuta 13 consultas completas | `routes/api.php:16`, `FrontendDataController.php` | Cualquiera puede descargar el catálogo y consumir CPU/BD de la instancia |
| **H-09** | 🟠 Media | Sin versión de esquema: tras un despliegue, los navegadores conservan datos con el formato viejo hasta 24 h | `obtenerStaticos.js:54-68` | Un cambio de forma en una clave rompe la UI hasta que expire el TTL |
| **H-10** | 🟡 Baja | El cargador solo se incluye en 2 vistas; el formulario de orden depende de una visita previa al dashboard | `ordenes/create.blade.php:5` vs `dashboard.blade.php:277` | Un usuario con acceso directo al formulario trabaja con caché vacía |
| **H-11** | 🟡 Baja | `useCieCups`, `utils/paisesLocalStorage.js` y `ejemplos/*` son código muerto | — | Mantenimiento: confunden qué mecanismo es el vigente |
| **H-12** | 🟡 Baja | Las "recientes" de CIE persisten tras cerrar sesión, en un equipo compartido | `CieSearchInput.jsx` | Un segundo usuario ve los diagnósticos que buscó el anterior (no es PII de paciente, pero sí contexto clínico) |

---

## 6. Impacto en el despliegue multi-instancia

**Ninguno sobre el aislamiento.** `localStorage` está particionado por origen:

- `https://lab-a.cliente.com` y `https://lab-b.cliente.com` → **dos almacenes distintos**.
- El diseño actual ya usa dominios separados, así que no hay fuga entre clientes.
- Lo que sí cruza instancias son las **cookies**, y eso ya está mitigado con
  `SESSION_DOMAIN` vacío, `SESSION_COOKIE` por instancia y `APP_KEY` distinta.

La única precaución: **no alojar las dos instancias bajo el mismo origen** (mismo dominio y
puerto, distinto path). Si algún día se hiciera, compartirían `localStorage` y cookies.

---

## 7. Alternativas

### A. Endurecer el `localStorage` actual (cambio mínimo)

Un único módulo `cache.js` con: versión de esquema, TTL, escritura atómica (timestamp al
final), manejo de `QuotaExceededError`, claves unificadas y borrado de código muerto.

| | |
|---|---|
| **Coste** | 1–2 días |
| **Pros** | No cambia la UX ni el backend; corrige H-01 a H-07 y H-11 |
| **Contras** | Sigue sin invalidación real por cambio de catálogo; sigue limitado a ~5 MB |
| **Cuándo** | Si se quiere parar el sangrado ya y decidir la arquitectura después |

### B. `localStorage` solo para listas pequeñas + búsqueda en servidor para CUPS/CIE

Sacar CUPS y CIE10 del payload (son el **99 % del tamaño**) y resolverlos con los endpoints
de búsqueda que **ya existen** (`/api/cups/buscar`, `/api/cie10/buscar`) con debounce.
`localStorage` queda para países, municipios, documentos, EPS y finalidades (≈ 100 KB).

| | |
|---|---|
| **Coste** | 2–4 días |
| **Pros** | Elimina el riesgo de cuota de raíz; catálogo siempre fresco; primera carga mucho más liviana; sin infraestructura nueva |
| **Contras** | La búsqueda requiere red (hoy la requiere igual para guardar la orden) |
| **Cuándo** | **Recomendada como objetivo**, porque el problema de tamaño y frescura desaparece |

### C. `ETag` + `IndexedDB` para el catálogo grande

El endpoint devuelve `ETag`/`Last-Modified` y el cliente guarda el JSON en IndexedDB (sin
límite de 5 MB) y revalida con `If-None-Match` (304 barato).

| | |
|---|---|
| **Coste** | 3–5 días |
| **Pros** | Mantiene el catálogo completo en el cliente; siempre fresco; sin cuota |
| **Contras** | IndexedDB es asíncrono y añade complejidad; sigue siendo caché por navegador |
| **Cuándo** | Si se quiere conservar la UX actual de catálogo completo offline |

### D. Caché de servidor + versión de catálogo

`Cache::remember('frontend.catalogs.v1', 3600, ...)` en el backend y un `catalog_version`
en la respuesta. El cliente solo reescribe si cambió la versión.

| | |
|---|---|
| **Coste** | 1–2 días backend + 0,5 día frontend |
| **Pros** | Baja la carga de BD (13 consultas por visita → 1 cada hora); menos tráfico; invalida por versión y no por reloj |
| **Contras** | No resuelve la cuota del cliente por sí solo |
| **Cuándo** | **Complemento ideal de B**: quita la presión del servidor y da invalidación correcta |

### E. Service Worker (Workbox)

Precarga versionada por build y soporte offline real.

| | |
|---|---|
| **Coste** | 3–5 días + disciplina de versionado |
| **Pros** | Mejor rendimiento y offline genuino |
| **Contras** | Sobredimensionado: la app es de escritorio con red; añade el modo de fallo más difícil de depurar |
| **Cuándo** | No ahora |

---

## 8. Recomendación

**Combinación B + D, con A como paso inmediato:**

1. **A hoy (1–2 días):** arreglar H-01 (colisión `default_cies_data`), H-02 (timestamp),
   H-03 (crash), H-04/H-05/H-06 (claves y formas) y añadir versión de esquema. Son bugs
   concretos, con tests de regresión posibles.
2. **B como objetivo (2–4 días):** dejar CUPS/CIE fuera de `localStorage`.
3. **D en paralelo (1–2 días):** `Cache::remember` + `ETag` en el endpoint, y moverlo dentro
   de `auth:sanctum` con `throttle` (H-08).

Coste total estimado: **5–7 días** y desaparecen los riesgos de cuota, frescura y exposición.

Si se prefiere conservar el catálogo completo en el cliente, la alternativa es **C + D**
(5–7 días) en lugar de B.

---

## 9. Límites de este análisis

- Los tamaños se midieron sobre una BD **sembrada con los catálogos reales**, pero con una
  selección de `activo` de demo. El escenario de 8,6 MB asume activación masiva, que hoy
  se hace ítem por ítem desde *Setup*.
- No se probó en dispositivos reales (Safari iOS, Android de gama baja); la cuota exacta
  varía por navegador.
- No se auditó el resto de endpoints de catálogo (`/api/cie10`, `/api/cups`) en cuanto a
  rendimiento; solo su forma de respuesta.

---

## 10. Estado de implementación (2026-11-05)

Se implementó la ruta **A + B + D**:

- **A:** módulo único `resources/js/lib/catalogs.js` con versión de esquema,
  TTL de revalidación, escritura atómica y manejo de cuota.
- **B:** CUPS y CIE10 ya no viajan en `/api/static-data-for-frontend` ni se
  guardan en `localStorage`. CIE10 se resuelve con `/api/cie10/buscar`; CUPS
  mantiene su flujo actual en Blade y `/api/cups/buscar` queda disponible para
  cuando el frontend lo consuma.
- **D:** `Cache::remember` + `ETag`/304 + endpoint dentro de `auth:sanctum`
  con `throttle:60,1`.

Hallazgos cerrados: H-01 a H-12, con la salvedad de H-12 (los recientes de
CIE quedan separados por usuario, no se borran al cerrar sesión).

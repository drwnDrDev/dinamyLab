# Informe: valores de referencia en resultados

**Fecha:** 2026-11-10
**Alcance:** almacenamiento, selección y presentación de valores de referencia; cambio de `tipo_dato` de un parámetro; cobertura de pruebas.
**Método:** lectura del código, ejecución de una prueba puntual en Tinker y revisión de los tests existentes. No se modificó código de producción.

---

## 1. Resumen ejecutivo

Hoy un valor de referencia es **una fila de texto libre** (`valor_referencias.salida`) asociada a un parámetro, con dos números opcionales (`min`, `max`) y una **única etiqueta demográfica** (`demografia`). Esa etiqueta vale `adultos`, `menores`, `hombres` o `mujeres`, pero **no se pueden combinar** edad y sexo en una misma fila.

La selección se hace en `App\Services\EscogerReferencia` comparando esa etiqueta contra dos valores calculados del paciente: `etario` (`menores` si edad < 5, `adultos` en el resto) y `sexo` (`hombres` solo si `persona.sexo === 'M'`; cualquier otro valor cae en `mujeres`). El algoritmo filtra primero por edad y luego por sexo, pero **reemplaza la colección en cada paso**; si el primer filtro deja cero filas, el segundo ya no puede recuperarlas.

Consecuencias principales:

- Las referencias **solo por sexo** (por ejemplo HDL con `hombres`/`mujeres`) **nunca se seleccionan** para un adulto: el filtro `adultos` deja la colección vacía y el método devuelve `null`. Verificado en Tinker.
- Las referencias **solo por edad** sí funcionan, pero cualquier persona de 5 años o más usa la referencia de `adultos`, incluyendo adolescentes.
- No existe una operación soportada para cambiar `tipo_dato`: `ParametroController::update` está vacío y no hay ruta de actualización de parámetros. El cambio solo es posible por base de datos, seeder o código.
- Si se cambia el tipo directamente, **los resultados existentes no se convierten ni se revalidan**. La captura inicial (`ResultadosController::store` y `Api\ResultadosController::store`) **no valida por tipo**; la edición y el lote sí. Un valor viejo puede volverse inválido y bloquear la edición completa.
- El cálculo de `es_normal` **no distingue el tipo**: convierte cualquier resultado con `floatval()`. Un texto como `"positivo"` se vuelve `0.0`, pasa `is_numeric()` y se compara contra `min`/`max` como si fuera un número. Verificado: `floatval("positivo") === 0.0` y `is_numeric(0.0) === true`.
- La referencia mostrada **no se congela** en el resultado. Se recalcula al abrir el reporte con los datos actuales del paciente, del procedimiento y del parámetro. Corregir el sexo o la fecha de nacimiento, o editar una referencia, cambia reportes históricos.

---

## 2. Dónde y cómo se almacenan

### 2.1 Tabla `parametros`

`database/migrations/2025_06_13_152347_create_parametros_table.php:17-29`

| Columna | Tipo | Uso |
|---|---|---|
| `nombre`, `grupo`, `slug` | string | Identificación y agrupación visual |
| `posicion` | unsignedTinyInteger | Orden dentro del examen |
| **`tipo_dato`** | **string, sin enum ni check** | `text`, `number`, `date`, `select`, `list`, `textarea` |
| `default` | string nullable | Valor inicial del formulario |
| `unidades`, `metodo` | string nullable | Presentación |
| `examen_id` | FK a `examenes`, cascade | Pertenencia |

No hay historial de cambios de parámetro ni versión de esquema.

### 2.2 Tabla `valor_referencias`

`database/migrations/2025_06_13_154357_create_valor_referencias_table.php:14-23`

| Columna | Tipo | Uso |
|---|---|---|
| `parametro_id` | FK, cascade | Dueño de la referencia |
| **`demografia`** | **string NOT NULL** | `adultos`, `menores`, `hombres`, `mujeres` (texto libre) |
| **`salida`** | **string NOT NULL** | Texto que se imprime como “V. de referencia” |
| `min`, `max` | decimal(10,4) nullable | Solo para decidir `es_normal` |
| `optimo` | string nullable | **Nunca se usa en el código** |

No hay restricción de unicidad por `(parametro_id, demografia)`, ni validación del vocabulario de `demografia`, ni columnas separadas de sexo/edad.

### 2.3 Tabla `resultados`

`database/migrations/2025_06_13_180502_create_resultados_table.php:14-22`

| Columna | Tipo | Uso |
|---|---|---|
| **`resultado`** | **string nullable** | Valor capturado, siempre como texto |
| `posicion` | unsignedTinyInteger | Orden |
| `parametro_id`, `procedimiento_id` | FK | Único por pareja |

No se guarda el tipo vigente al momento de capturar, ni la referencia aplicada, ni las unidades. Por eso el reporte histórico se reinterpreta con la configuración actual.

### 2.4 Cómo se siembran las referencias

`database/seeders/ExamenSeeder.php:106-121` lee un JSON con esta forma:

```json
"referencia": {
  "adultos": { "salida": "13.5-16.5", "minimo": 13.5, "maximo": 16.5 },
  "menores": { "salida": "11.0-13.5", "minimo": 11, "maximo": 13.5 }
}
```

`minimo` se guarda en `min`, `maximo` en `max`, `salida` en `salida`; `optimo` no se siembra. La clave del objeto (`adultos`, `menores`, `hombres`, `mujeres`) se guarda tal cual en `demografia`.

En los datos sembrados conviven ambos estilos:

- Referencias por edad: hemograma, glicemia, triglicéridos, colesterol total/LDL.
- Referencias por sexo: colesterol HDL y ácido úrico (`ExamenSeeder.php:300,340`).

Esa mezcla es la que expone el fallo del selector.

---

## 3. Cómo se almacenan sexo y edad

### 3.1 Sexo

- `personas.sexo` es `string(1)` nullable (`database/migrations/2025_05_15_010758_create_personas_table.php:25`).
- Los validadores aceptan códigos distintos según el punto:
  - `StorePersonaRequest` y `UpdatePersonaRequest`: `H`, `M`, `I`, `F`.
  - `PersonaController::index`: `M`, `F`, `I`.
  - `ExamenController::index`: `M`, `F`, `A` (aquí `A` es aplicabilidad del examen, no sexo de la persona).
- `EscogerReferencia::datosDemograficos()` solo reconoce `M` como hombre:

```php
'sexo' => $paciente->sexo === 'M' ? 'hombres' : 'mujeres',
```

Por tanto `H` (el código RIPS habitual de hombre), `I`, `F` y `null` se tratan todos como `mujeres`.

- El reporte repite la misma regla: `resources/views/resultados/reporte_orden.blade.php:77` imprime `Masculino` solo si `sexo === 'M'`; `H` e `I` se muestran como `Femenino`.

### 3.2 Edad

- `personas.fecha_nacimiento` es fecha; `Persona::edad()` (`app/Models/Persona.php:118-135`) devuelve un texto con años, meses o días.
- Para referencias se calcula la edad en años al momento del procedimiento:

```php
$edad = optional($paciente->fecha_nacimiento)->diffInYears($fecha);
```

- La edad se reduce a **dos franjas**: `menores` si `< 5`, `adultos` si `>= 5` (`EscogerReferencia.php:27`).
- Si no hay fecha de nacimiento, o la edad es negativa, se devuelve `[]` y todas las referencias quedan en `null`, aunque los resultados se siguen mostrando.

### 3.3 Limitación estructural

`valor_referencias.demografia` es **una sola cadena**. No existe una fila que signifique “adulto hombre” o “menor mujer”. Para expresar ambos criterios harían falta varias filas y un algoritmo que sepa combinarlas, cosa que hoy no ocurre.

---

## 4. Cómo se selecciona la referencia

`app/Services/EscogerReferencia.php:34-51`:

1. Si el parámetro no tiene referencias, devuelve `null`.
2. Si tiene **exactamente una**, la devuelve sin mirar demografía.
3. Recorre `['etario', 'sexo']`:
   - filtra la colección por `demografia === valor`;
   - si queda exactamente una, la devuelve;
   - si no, continúa con la colección ya filtrada.
4. Devuelve la primera de lo que quede (posiblemente `null`).

### Fallos verificados

| Caso | Datos | Resultado |
|---|---|---|
| Solo sexo, adulto | refs `hombres`, `mujeres` | `null` (el filtro `adultos` vacía la colección) |
| Solo edad, adulto | refs `adultos`, `menores` | `ADULTOS` |
| Edad + sexo mezclados | refs `adultos`, `hombres`, `mujeres` | `adultos` (el primer filtro acierta y ya no mira el sexo) |
| Una sola referencia | cualquier demografía | se devuelve esa |
| Duplicados de la misma demografía | dos filas `adultos` | tras el filtro de sexo queda vacío → `null` |

La prueba en Tinker confirmó los dos primeros casos y la coerción de texto a número.

### Agravantes

- El umbral de 5 años es fijo y no configurables.
- No hay franjas pediátricas (neonato, lactante, escolar, adolescente).
- `demografia` no tiene enum/check; un typo (`"adulto"`, `"Hombres"`) falla en silencio.
- `optimo` se almacena pero no se usa.
- La selección no es determinista si hay varias filas válidas: depende del orden de la colección.

---

## 5. Cómo se reportan los valores en resultados

### 5.1 Flujo

| Momento | Servicio / controlador | Qué arma |
|---|---|---|
| Captura individual | `EscogerReferencia::recorrerParametrosExamen()` (`:56-89`) | `referencia` = `salida` |
| Captura por lote | `ExamenController::lote()` (`:97-128`) | `referencia` = `salida` |
| Ver / editar / reporte / historial | `EscogerReferencia::obtenerResultados()` (`:120-209`) | `referencia` = `salida` y `es_normal` |
| Impresión de orden | `ResultadosController::reporteOrden()` (`:67-85`) | usa `obtenerResultados()` |

En todos los casos se devuelve la **misma clave** `referencia` (texto de `salida`), más `tipo_dato`, `unidades`, `resultado`, `opciones` y `es_normal`.

### 5.2 Vistas que la muestran

- `resources/views/components/parametro-input.blade.php` (captura): muestra `referencia` en la cuarta columna.
- `resources/views/components/parametro-edit.blade.php` (edición): igual.
- `resources/views/components/parametro-print-table.blade.php` (reporte de orden): muestra `referencia`; si `es_normal` es falso agrega un `*` junto al resultado.
- `resources/views/components/parametro-print.blade.php` (impresión individual): igual.
- `resources/js/components/resultados/ResultadosLote.jsx:233-235`: muestra `Ref: {referencia}`.

En `parametro-print-table`, un parámetro `textarea` o llamado `observaciones` se renderiza como bloque de observaciones y **no muestra referencia ni marca de anormalidad**.

### 5.3 Cálculo de `es_normal`

`EscogerReferencia.php:160-191`:

```php
$valorResultado = floatval($resultado->resultado);
...
$isNormal = $referencia->max ? $valorResultado <= $referencia->max : true;
$isNormal = $isNormal && ($referencia->min ? $valorResultado >= $referencia->min : true);
```

Problemas:

1. **No mira `tipo_dato`.** Texto, fecha y selección también pasan por `floatval`.
2. **Texto no numérico se vuelve 0.0** y `is_numeric(0.0)` es `true`; por eso no se detecta como inválido.
3. `try/catch` alrededor de `floatval` es código muerto: `floatval` no lanza excepción.
4. `min = 0` o `max = 0` se interpretan como “sin límite” por el operador ternario de verdad.
5. Si el valor es `NaN`/`Inf` o falla la conversión, se hace `continue`: **el parámetro desaparece del reporte**, no solo se marca.
6. Solo se usan `min`/`max`; `optimo` nunca participa.
7. `salida` y `min`/`max` pueden contradecirse: el texto impreso dice una cosa y el `*` otra, porque nadie valida su coherencia.

### 5.4 Estabilidad histórica

La referencia se recalcula en cada lectura. No hay snapshot en `resultados`. Si mañana se corrige el sexo del paciente, se cambia una referencia o se modifica `tipo_dato`, el reporte de una muestra ya terminada puede cambiar. Para un laboratorio clínico esto es un riesgo de auditoría.

---

## 6. Qué pasa cuando cambia `tipo_dato`

### 6.1 Hoy no existe una vía soportada

- `ParametroController::update()` está vacío (`app/Http/Controllers/ParametroController.php:57-60`).
- No hay ruta `parametros.update`; solo `parametros.index` (`routes/web.php:132`).
- `Api\ExamenesController::update()` solo actualiza campos del examen, no de sus parámetros (`:55-95`).
- El checklist ya registra que el CRUD de parámetros y referencias no existe: `docs/CHECKLIST_DEUDA_TECNICA.md` EX-06.

Por tanto, “cambiar el tipo” hoy significa editar la base directamente, volver a sembrar o escribir código. No hay validación, auditoría ni migración de datos.

### 6.2 Efectos si se cambia directamente

| Área | Efecto |
|---|---|
| **Render de captura** | `parametro-input.blade.php` y `parametro-edit.blade.php` hacen `@switch` sobre `tipo_dato`; un tipo desconocido cae al `@default` (input de texto). `textarea` cambia a bloque de 3 columnas. |
| **Render de lote** | `ResultadosLote.jsx` decide input, `type`, `step` y `maxLength` según el tipo. |
| **Validación al crear** | `ResultadosController::store()` y `Api\ResultadosController::store()` **no validan por tipo**; solo omiten vacíos y comprueban que el parámetro exista. |
| **Validación al editar** | `ActualizarResultados.php:50-64` valida `number`, `date` y `select`; el resto solo string ≤ 255. |
| **Validación en lote** | `GuardarResultadosLote.php:68-83` replica la misma lógica. |
| **Datos existentes** | `resultados.resultado` sigue siendo string; no se convierte, no se revalida y no se marca como legado. |
| **Edición posterior** | El formulario envía todos los resultados. Si un valor viejo no cumple el tipo nuevo, **toda la actualización falla** hasta corregirlo; corregirlo genera un registro en `resultado_cambios`. |
| **`number`** | Exige `numeric` y finito. Valores con coma (`12,5`), texto o unidades pegadas fallan. |
| **`date`** | Exige `Y-m-d`. Valores viejos en otro formato fallan y el input `date` puede mostrarse vacío. |
| **`select`** | Exige `Rule::in(opciones)`. Si no se cargaron opciones, **ningún valor es válido**. Valores viejos fuera del catálogo fallan. |
| **`text` / `list`** | Solo string ≤ 255; es el cambio más tolerante. |
| **`textarea`** | Se oculta en la tabla de impresión y se trata como observación; la referencia deja de mostrarse. |
| **Referencia y `es_normal`** | No dependen de `tipo_dato`: si hay `min`/`max`, se siguen aplicando con `floatval`. Un parámetro convertido a texto conserva el rango numérico y sigue marcando `*`; uno convertido a número empieza a interpretar textos viejos como `0.0`. |
| **Reporte histórico** | Cambia la forma de imprimir y la marca de anormalidad de resultados ya emitidos. |
| **Consumidores API** | Reciben el `tipo_dato` actual; no hay contrato versionado. |

### 6.3 Duplicación que agrava el cambio

La validación por tipo está escrita dos veces (`ActualizarResultados` y `GuardarResultadosLote`) y **falta** en las dos rutas de captura inicial. La presentación está escrita cuatro veces (dos Blade de captura, dos Blade de impresión) más el componente React de lote. Cada tipo nuevo obliga a tocar todos esos lugares.

---

## 7. Cobertura de pruebas

### 7.1 Lo que sí está cubierto

| Test | Qué prueba | Relación con referencias |
|---|---|---|
| `tests/Feature/ResultadosEdicionTest.php` | Motivo obligatorio, historial de cambios, parámetro ajeno, sede, número inválido | Solo `tipo_dato = number`; **no crea ni verifica referencias** |
| `tests/Feature/ResultadosLoteTest.php` | Lote atómico, campos vacíos, número inválido | `number` y `text`; **no verifica referencias** |
| `tests/Feature/OrdenEdicionReporteTest.php` | El reporte muestra orden, examen, parámetro y resultado | **No verifica** `referencia`, `es_normal`, `*`, sexo ni edad |
| `tests/Feature/ControlSedeTest.php` | Control de sede al guardar resultados | Sin referencias |

### 7.2 Lo que no está cubierto

- `EscogerReferencia::datosDemograficos()`: sexo `H`/`M`/`I`/`F`/`null`, edad 0-4 vs 5+, fecha nula, fecha futura.
- `EscogerReferencia::estableceReferencia()`: una referencia, sin referencias, solo sexo, solo edad, combinadas, duplicados, `demografia` con typo.
- `es_normal`: número dentro/fuera de rango, límites exactos, `min = 0`, `max = 0`, texto, fecha, select, `NaN`.
- Cambio de `tipo_dato`: compatibilidad con resultados existentes, bloqueo o conversión, auditoría del cambio.
- Estabilidad del reporte histórico tras editar paciente o referencias.
- Render de `referencia` y `*` en `parametro-print-table` y en el reporte de orden.

---

## 8. Estrategias para que el cambio de tipo sea flexible

### Opción A — Registro central de tipos + snapshot en el resultado (recomendada como primer paso)

**Idea:** una sola definición de cada tipo (validación, normalización, componente de entrada, visibilidad de impresión, comparación) y congelar en cada resultado el tipo y la referencia con los que se capturó.

Componentes:

1. `app/Services/TiposParametro/` o `config/parametros.php` con un registro por tipo: reglas, `normalizar()`, `formatear()`, `soportaReferencia()`, `visibleEnImpresion()`.
2. `Resultado` guarda `tipo_dato_snapshot`, `referencia_snapshot`, `unidades_snapshot` y, opcionalmente, `min_snapshot`/`max_snapshot`.
3. `resultado_cambios` se mantiene; se agrega `parametro_cambios` para auditar el cambio de tipo (antes, después, usuario, motivo, fecha).
4. Al cambiar tipo, un comando/servicio revisa los resultados existentes:
   - compatibles → permite el cambio;
   - incompatibles → bloquea o exige decisión explícita (“convertir”, “dejar como legado”, “cancelar”).
5. La validación se llama desde **las cuatro puertas**: store web, store API, lote y edición.
6. `es_normal` solo compara si el tipo es numérico y si hay `min`/`max`; para texto/select/fecha devuelve “no aplica” en vez de `true` silencioso.
7. La impresión usa el snapshot, no la configuración actual.

**Ventajas:** corrige los huecos más graves sin rehacer el modelo; los reportes históricos quedan estables; el cambio de tipo pasa a ser una operación explícita y auditada.

**Costos:** migración para las columnas de snapshot; backfill de resultados existentes; tocar los cuatro puntos de validación y las vistas.

**Riesgo:** si no se hace el snapshot, el problema histórico persiste aunque el registro central quede limpio.

### Opción B — Resultado tipado con columnas o JSON

**Idea:** dejar de guardar todo como string. `resultados` gana `valor_numerico`, `valor_texto`, `valor_fecha`, `valor_opcion_id` (o un `valor` JSON con esquema versionado) y un `tipo_dato` de registro.

**Ventajas:** consultas numéricas y rangos en SQL; validación fuerte; conversiones explícitas; menos coerción accidental.

**Costos:** migración y backfill de todos los resultados; adaptar reportes, exportaciones, RIPS y consultas; convivencia con datos viejos. Es un cambio de modelo, no solo de validación.

**Cuándo conviene:** si se van a hacer estadísticas, controles de calidad o integraciones que necesiten filtrar por valor numérico.

### Opción C — Versionado inmutable del parámetro

**Idea:** `parametro_versiones` guarda tipo, unidades, opciones y referencias. `parametros` apunta a la versión vigente; cada `resultado` guarda `parametro_version_id`. Cambiar el tipo crea una versión nueva; nunca se muta la anterior.

**Ventajas:** es el modelo más sólido para auditoría clínica; un resultado siempre se interpreta con la versión que estaba vigente; se pueden comparar versiones.

**Costos:** más tablas y joins; hay que migrar parámetros y resultados existentes; la UI de administración crece; el reporte debe resolver la versión del resultado, no la actual.

**Cuándo conviene:** si el laboratorio necesita trazabilidad regulatoria fuerte o si los parámetros cambian con frecuencia.

### Recomendación

Empezar por **A** y dejar **C** como evolución si la auditoría lo exige. A corrige los fallos actuales, hace explícito el cambio de tipo y no obliga a rehacer consultas ni reportes. B y C resuelven problemas más profundos, pero cuestan mucho más y hoy no hay CRUD de parámetros ni volumen que los justifique.

### Diseño específico para sexo y edad

Independientemente de la opción elegida, las referencias necesitan criterios estructurados:

- Reemplazar `demografia` por columnas explícitas: `sexo` (nullable / `A` = cualquiera), `edad_min_meses`, `edad_max_meses` (rango semiabierto `[min, max)`), `prioridad` opcional.
- Alternativa intermedia: `criterios` JSON validado, pero las columnas explícitas son más fáciles de consultar y de validar.
- Normalizar el sexo a un solo código canónico (idealmente el de RIPS: `H`, `M`, `I`) y mapear en presentación; `A`/`null` = ambos.
- Calcular la edad en meses, no en años, para pediatría; usar la fecha del procedimiento.
- Seleccionar por **todos** los criterios no nulos, con desempate determinista por especificidad (sexo definido gana a cualquiera; rango más estrecho gana al amplio) y `prioridad`.
- Agregar unicidad `(parametro_id, sexo, edad_min_meses, edad_max_meses)` y validación del vocabulario.
- Sembrar/migrar los textos actuales: `hombres` → `sexo=H`, `mujeres` → `sexo=M`, `adultos` → `edad_min=60 meses`, `menores` → `edad_max=60 meses` (o la franja que decida el laboratorio).
- Congelar en el resultado la referencia seleccionada (`salida`, `min`, `max`, `unidades`) para que el reporte no cambie después.

---

## 9. Plan de pruebas propuesto

### Unitarias (sin base de datos, con colecciones en memoria)

`tests/Unit/EscogerReferenciaTest.php`:

1. Sin referencias → `null`.
2. Una sola referencia → se devuelve aunque la demografía no coincida.
3. Solo edad, adulto → `adultos`.
4. Solo edad, menor → `menores`.
5. Solo sexo, hombre → `hombres` (hoy falla; es el caso que debe quedar rojo primero).
6. Solo sexo, mujer → `mujeres`.
7. Edad + sexo: gana la referencia más específica.
8. Sexo `H`, `M`, `I`, `F`, `null`.
9. Edad 4 vs 5 (límite).
10. Fecha de nacimiento nula o futura.
11. Duplicados de demografía → resultado determinista o `null` explícito.
12. `demografia` con typo → no rompe y no elige una fila ajena.

### Feature

1. `ResultadosEdicionTest`: referencia y `es_normal` para número dentro/fuera de rango; límite exacto; `min = 0`; `max = 0`.
2. Nuevo `ResultadosReferenciaTest`: texto/select/fecha no se convierten a `0.0`; no reciben `*`; no desaparecen del reporte.
3. `OrdenEdicionReporteTest`: el reporte imprime `referencia`; muestra `*` solo cuando corresponde; un `textarea` no imprime referencia.
4. Nuevo `ParametroTipoCambioTest`: cambiar `number` → `text` conserva el resultado y el reporte histórico; `text` → `number` con valores viejos no numéricos se bloquea o se marca como legado; el cambio queda auditado.
5. Nuevo `ReporteHistoricoTest`: editar la referencia o el sexo del paciente después de guardar no cambia el reporte ya emitido (si se implementa snapshot).
6. `ResultadosLoteTest`: validación por tipo en lote, incluida la captura inicial que hoy no valida.

---

## 10. Decisiones abiertas

1. **¿El cambio de tipo debe aplicar a parámetros con resultados históricos?** Si la respuesta es sí, el snapshot o el versionado son obligatorios. Si es no, basta con un registro de tipos y una auditoría.
2. **¿Qué pasa con los valores viejos incompatibles?** ¿Se bloquea el cambio, se convierten, o se conservan como legado con su tipo original?
3. **¿Se normaliza el sexo a RIPS (`H`/`M`/`I`) o se mantiene `M`/`F`?** Afecta la migración de referencias y el reporte.
4. **¿Las franjas de edad deben ser configurables por laboratorio?** Hoy el corte es 5 años fijo.
5. **¿El rango de referencia debe ser parte del resultado emitido (snapshot) o siempre recalcularse?** Para auditoría clínica se recomienda snapshot.

---

## 11. Archivos clave

| Archivo | Papel |
|---|---|
| `app/Services/EscogerReferencia.php` | Selección, captura y reporte |
| `app/Models/ValorReferencia.php` | Modelo de referencia |
| `app/Models/Parametro.php` | Modelo de parámetro |
| `app/Models/Persona.php` | Sexo, fecha de nacimiento y edad |
| `database/migrations/2025_06_13_154357_create_valor_referencias_table.php` | Esquema de referencias |
| `database/migrations/2025_06_13_152347_create_parametros_table.php` | Esquema de parámetros |
| `database/seeders/ExamenSeeder.php` | Formato de siembra de referencias |
| `app/Services/ActualizarResultados.php` | Validación por tipo al editar |
| `app/Services/GuardarResultadosLote.php` | Validación por tipo en lote |
| `app/Http/Controllers/ResultadosController.php` | Captura, ver, editar y reporte |
| `app/Http/Controllers/ExamenController.php` | Captura por lote |
| `resources/views/components/parametro-*.blade.php` | Presentación de referencia y resultado |
| `resources/views/resultados/reporte_orden.blade.php` | Reporte imprimible |
| `tests/Feature/ResultadosEdicionTest.php` | Cobertura de edición |
| `tests/Feature/ResultadosLoteTest.php` | Cobertura de lote |
| `tests/Feature/OrdenEdicionReporteTest.php` | Cobertura del reporte |

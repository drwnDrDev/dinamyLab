# Firma compuesta con marca de agua en el reporte de resultados

**Fecha:** 2026-11-20
**Estado:** propuesto (pendiente de aprobación)
**Tamaño:** cambio medio (servicio nuevo, layout del pie y paginación, helper de código, pruebas)

## Intención

Que la firma escaneada del bacteriólogo que aparece en el reporte de resultados no se pueda extraer y reutilizar en otro documento. Es **disuasión**, no una firma con valor probatorio: lo que se copie queda atado a ese reporte (orden, paciente y un código de verificación) y una alteración de los resultados se nota porque el código deja de coincidir.

Además, el reporte debe salir firmado en **todas las hojas**, con **una sola firma por hoja** en el caso normal: un solo bacteriólogo valida toda la orden.

## Objetivos

- Componer en el servidor una imagen por reporte: firma aplanada sobre fondo blanco, con una marca diagonal tenue que lleva la referencia del reporte, el documento del paciente y un código corto.
- **Caso normal (un solo profesional):** una firma en el pie fijo, que DomPDF repite en cada hoja. Exactamente una por hoja, en todas.
- **Caso excepcional (varios profesionales):** una firma al cierre de cada grupo de exámenes del mismo profesional, para no perder la atribución.
- Calcular un código de verificación reproducible (HMAC-SHA256 truncado) y mostrarlo en el pie.
- Usar GD, que ya está instalado. Sin librerías nuevas.
- No romper la generación del reporte si la firma falta o la composición falla.

## No objetivos

- Firma digital con certificado X.509 / PAdES.
- Endpoint público de verificación ni QR.
- Guardar los códigos emitidos.
- Ofuscar el logo.
- Subida o gestión de firmas desde la interfaz: sigue siendo manual (archivo en `storage/app/private/firmas/`).
- Garantizar «una por hoja» cuando el reporte mezcla profesionales.

## Anti-objetivos

- Que la marca haga ilegible la firma o los datos del reporte.
- Que un fallo de GD tumbe la generación del PDF.
- Devolver la imagen original cuando falle la composición: anularía la protección.

## Restricciones

- PHP 8.5 con GD y FreeType; `vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf` ya está disponible.
- DomPDF 3.1: la imagen debe ser un data URI PNG/JPEG; nada de flex ni grid en la plantilla.
- El pie del PDF ya es `position: fixed` y se repite en todas las páginas; el encabezado también.
- En la vista web, encabezado y pie son estáticos: salen una vez por documento (Chrome no repite `position: fixed` al imprimir).
- La misma plantilla sirve la vista web y el PDF (`$enPdf`).
- No instalar dependencias ni hacer commit sin confirmación explícita.

## Enfoques considerados

- **A. Firma solo en el PDF.** La vista web muestra un texto («Firma en el PDF») y la imagen solo se dibuja en DomPDF. Es el cambio más pequeño, pero la vista previa pierde la firma y el PDF sigue entregando la imagen limpia.
- **B. Firma compuesta con marca de agua (elegida).** El servidor aplana la firma sobre blanco y le sobrepone una marca por documento. Lo extraído queda atado al reporte. Usa GD, ya instalado.
- **C. Ofuscación en el cliente** (overlay CSS, `<canvas>`, bloquear clic derecho). Descartada: se salta con las herramientas del desarrollador o una captura, y afecta accesibilidad.

## Diseño

### 0. Una firma por hoja

`ConstruirReporteResultados` cuenta los profesionales distintos (`empleado_id`) entre los exámenes del reporte:

- **Un solo profesional:** arma `$reporte['firma'] = ['src' => data URI compuesto, 'nombre', 'documento']` y deja `firma_src = null` en cada examen. La plantilla dibuja esa firma en el `<footer class="pie">` fijo, con el nombre y el cargo. Como DomPDF repite el pie, cada hoja del PDF queda firmada una sola vez.
- **Varios profesionales:** `$reporte['firma'] = null`. Cada examen conserva su `firma_src` compuesto y la plantilla firma al cierre de cada grupo consecutivo del mismo profesional (`cierra_grupo`). Es el caso excepcional: alguna hoja puede quedar sin firma o llevar dos, y se acepta.

El pie crece en el caso normal: se sube el margen inferior de `@page` (de 20 mm a unos 32 mm), se recalibra la paginación y se elimina la reserva `$ALTO_FIRMA` de cada bloque de examen. En el caso excepcional se mantienen los márgenes y la reserva actuales.

En la vista web el pie es estático: la firma se ve una vez. La garantía de una por hoja es del PDF.

### 1. Servicio `App\Services\FirmaReporte`

`componer(?string $ruta, string $marca): ?string`

- Lee la firma del disco `local` con la misma validación que hoy hace `ConstruirReporteResultados::imagenComoDataUri()`: sin rutas absolutas ni `..`, solo PNG/JPEG, acepta `nombre.png` o `firmas/nombre.png`.
- Aplana la imagen sobre fondo blanco (elimina la transparencia) y añade un margen interior de unos 6 px.
- Genera una capa de texto con `imagettftext()` (DejaVu Sans), la rota unos -30° y la repite en mosaico cada ~220 px, en gris y con opacidad baja (15–20 %).
- Texto de la marca: `ORDEN 12345 · CC 1.234.567 · 3F2A-9C11-7B4D`. Cuando el reporte mezcla órdenes (historia): `HISTORIA · CC ... · 3F2A-...`.
- Devuelve `data:image/png;base64,...`.
- Memoiza por petición con clave `sha1(bytes) . sha1(marca)`. En el caso normal se compone una sola vez por reporte; en el excepcional, una por profesional.
- Si el archivo falta, no es PNG/JPEG o GD falla: devuelve `null` y registra `Log::warning`. El reporte sale sin firma, nunca con la original.

### 2. Código de verificación

`App\Services\CodigoVerificacionReporte::generar(array $reporte): string`

- Canoniza un JSON con: `v = 1`, documento del paciente, números de orden únicos y, por examen, `procedimiento_id`, nombre, `fecha_validacion` y `[nombre, resultado, unidades, referencia]` de cada parámetro. Excluye `fecha_emision`, `logo_src` y `firma_src`.
- Código: `strtoupper(substr(hash_hmac('sha256', $json, $clave), 0, 12))`, agrupado como `XXXX-XXXX-XXXX`.
- Clave: `config('reportes.clave_verificacion')`, tomada de `REPORTE_VERIFICACION_KEY` y con respaldo en `APP_KEY`. Se añade la variable comentada a `.env.example`.
- Es reproducible sin almacenar nada: el laboratorio puede recalcularlo desde los datos del reporte; si cambia un resultado, cambia el código.

### 3. `ConstruirReporteResultados`

- Deja de devolver la firma cruda. Guarda la ruta de la firma por examen, calcula `codigo_verificacion` sobre el contenido ya armado y después compone las firmas con la referencia del reporte (`$reporte['orden']['numero'] ?? 'HISTORIA'`).
- Calcula `cierra_grupo` por examen comparando el `empleado_id` con el siguiente (y `true` en el último).
- Agrega la clave raíz `firma` (caso normal) o deja las firmas por examen (caso excepcional).
- El logo sigue como data URI crudo (no aplica la marca).

Contrato resultante:

```php
$reporte = [
    // ...
    'codigo_verificacion' => '3F2A-9C11-7B4D',
    'firma' => ['src' => ?string, 'nombre' => string, 'documento' => string] | null,
    'examenes' => [[
        // ...
        'cierra_grupo' => bool,
        'bacteriologo' => ['nombre', 'documento', 'firma_src' => ?string],
    ]],
];
```

### 4. Plantilla

- `resultados/reporte/_examen.blade.php`: muestra el bloque de firma solo cuando `$bloque['ultimo'] && $examen['cierra_grupo'] && filled($examen['bacteriologo']['firma_src'])`. En el caso normal no se dibuja nada por examen.
- `resultados/reporte.blade.php`:
  - En el pie, a la derecha o al centro, la firma del reporte con nombre y cargo cuando `$reporte['firma']` exista. El contacto sigue a la izquierda y el número de página a la derecha.
  - En el pie, junto al contacto, `Verificación {{ $reporte['codigo_verificacion'] }}` cuando exista.
  - `@page` con margen inferior mayor solo en el caso normal (sin `size`, para no descartar la regla en DomPDF).
- `resultados/reporte/ejemplo.php`: añade `codigo_verificacion` y `firma` ficticios para la vista sin datos reales.

### 5. Pruebas

- `OrdenEdicionReporteTest`:
  - Un solo profesional: `firma` con PNG válido y opaco; `firma_src` nulo en los exámenes; la vista muestra «Verificación» y la imagen en el pie.
  - Dos profesionales: `firma` nula; `firma_src` presente por examen; `cierra_grupo` solo en el último de cada grupo.
  - El logo conserva el data URI original.
- Test unitario del código: mismo reporte → mismo código; un resultado cambiado → código distinto; formato `XXXX-XXXX-XXXX`.
- Test del compositor: un PNG transparente de entrada produce un PNG con fondo blanco; una firma inexistente devuelve `null`.
- Verificación manual del PDF: una orden de 10+ exámenes y el examen más largo, comprobando que cada hoja lleva exactamente una firma en el caso normal.
- Copiar `DejaVuSans.ttf` a `resources/fonts/` para que la composición no dependa de la ruta interna de DomPDF.

### 6. Operación y documentación

- Sin migraciones.
- `.env.example`: `REPORTE_VERIFICACION_KEY=` con nota de que, si se omite, se usa `APP_KEY`.
- `docs/CHECKLIST_DEUDA_TECNICA.md`: actualizar el contrato `$reporte` y registrar la tarea (IM-05).

## Llamadas que hago

- Regla de decisión: un `empleado_id` distinto entre los exámenes → firma en el pie; más de uno → firmas por grupo.
- Agrupar por profesional consecutivo; si el mismo profesional vuelve más adelante, se firma otra vez al cerrar su grupo.
- Texto exacto, opacidad y densidad de la marca; se ajustan con una firma real.
- Clave dedicada con respaldo en `APP_KEY`, en lugar de usar solo `APP_KEY`, para que rotar la clave de la aplicación no invalide los códigos de reportes ya emitidos.
- Fuente copiada a `resources/fonts/` en lugar de referenciar `vendor/`.
- Sin caché persistente: en el caso normal se compone una sola imagen por reporte.

## Lo que queda al implementador

- Medidas exactas del pie y recalibración de `@page` y de las alturas de paginación.
- Manejo de firmas muy pequeñas o muy grandes.
- Texto exacto del aviso en el log cuando falla la composición.
- Actualizar el contrato en la documentación.

## Riesgos

- En reportes con varios profesionales, alguna hoja puede quedar sin firma o con dos. Es el caso excepcional aceptado.
- El pie más alto reduce el área de contenido; hay que recalibrar y probar con el examen más largo y con 10+ exámenes.
- La marca puede quitar legibilidad si la firma es muy tenue; hay que probar con una muestra real.
- Si se rota `APP_KEY` sin definir `REPORTE_VERIFICACION_KEY`, los códigos de reportes anteriores dejan de poder recalcularse.
- No impide una captura de pantalla ni una firma dibujada a mano: es disuasión, no un control de seguridad.

## Esfuerzo estimado

10–14 h (servicio, código, layout del pie, paginación, pruebas y documentación).

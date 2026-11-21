# Firma compuesta y una firma por hoja — Plan de implementación

> **Para agentes:** SUB-SKILL OBLIGATORIA: usar `superpowers:executing-plans` (o `superpowers:subagent-driven-development`) para implementar este plan tarea por tarea. Los pasos usan checkbox (`- [ ]`).

**Objetivo:** Que cada hoja del PDF de resultados lleve exactamente una firma compuesta con marca de agua y un código de verificación reproducible, sin librerías nuevas.

**Arquitectura:** Un servicio compone la firma (GD) y otro calcula el HMAC del reporte. `ConstruirReporteResultados` decide entre firma en el pie (un solo profesional) y firma por grupo (varios), y entrega el contrato ampliado a la plantilla, que mueve la firma al pie fijo del PDF.

**Stack:** PHP 8.5, Laravel 13, GD/FreeType, DomPDF 3.1, Pest 4.

**Spec:** `docs/superpowers/specs/2026-11-20-firma-compuesta-reporte-design.md`

## Restricciones globales

- No instalar librerías ni dependencias nuevas. GD y DejaVu Sans ya están.
- Solo PNG y JPEG como firma; cualquier otro formato, archivo ausente o fallo de GD devuelve `null`, nunca la imagen original.
- La misma plantilla sirve vista web y PDF; la garantía de una firma por hoja es del PDF.
- Nada de `git commit` ni `git push` sin confirmación explícita.
- Los tests corren con `php artisan test` (SQLite en memoria, PHP 8.5 local).
- Mantener el estilo del proyecto: servicios en `app/Services`, pruebas Pest, textos en español.

## Review Focus

1. **Firma ausente o archivo inválido:** el reporte se genera igual, el pie muestra nombre y cargo sin imagen y no aparece ningún error.
2. **Reporte con varios profesionales no consecutivos:** cada cierre de grupo lleva su firma y ninguno de los grupos intermedios se queda con una firma ajena.
3. **Examen más largo con el pie alto:** el contenido no se desborda ni se solapa con el pie; la paginación se recalibra y el PDF sigue saliendo.
4. **Firma 1×1 o muy grande:** la composición no agota memoria ni deforma la imagen; se respeta el tope de 600×240.
5. **Reporte sin exámenes o sin grupos:** el cálculo del código no falla y el controlador mantiene su redirección actual.

---

### Task 1: Servicio `FirmaReporte`

**Files:**
- Create: `app/Services/FirmaReporte.php`
- Create: `tests/Unit/FirmaReporteTest.php`
- Create: `resources/fonts/DejaVuSans.ttf` (copiar de `vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf`)
- Modify: `tests/Pest.php` (helper `pngTransparente()` compartido con el test de feature)

**Interfaces:**
- Produces: `FirmaReporte::componer(?string $ruta, string $marca): ?string` — devuelve un data URI PNG o `null`.

- [ ] **Step 1: Copiar la fuente**

```bash
mkdir -p resources/fonts
cp vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf resources/fonts/DejaVuSans.ttf
```

- [ ] **Step 2: Agregar el helper compartido y escribir el test que falla**

En `tests/Pest.php`, junto a los helpers existentes:

```php
function pngTransparente(int $ancho = 80, int $alto = 30): string
{
    $img = imagecreatetruecolor($ancho, $alto);
    imagesavealpha($img, true);
    imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
    imageline($img, 5, $alto - 5, $ancho - 5, 5, imagecolorallocate($img, 0, 0, 0));
    ob_start();
    imagepng($img);
    $png = ob_get_clean();
    imagedestroy($img);

    return $png;
}
```

En `tests/Unit/FirmaReporteTest.php`:

```php
<?php

use App\Services\FirmaReporte;
use Illuminate\Support\Facades\Storage;

it('compone la firma sobre fondo blanco y devuelve un PNG', function () {
    Storage::fake('local');
    Storage::disk('local')->put('firmas/profesional.png', pngTransparente());

    $dataUri = app(FirmaReporte::class)->componer('profesional.png', 'ORDEN 872 · CC 123 · A1B2-C3D4-E5F6');

    expect($dataUri)->toStartWith('data:image/png;base64,');
    $png = base64_decode(substr($dataUri, strlen('data:image/png;base64,')));
    $info = getimagesizefromstring($png);
    expect($info['mime'])->toBe('image/png');
    expect($info[0])->toBeGreaterThan(80);

    $img = imagecreatefromstring($png);
    $rgb = imagecolorsforindex($img, imagecolorat($img, 0, 0));
    expect($rgb['alpha'])->toBe(0);
    expect($rgb['red'])->toBe(255);
    imagedestroy($img);
});

it('compone una firma de 1x1 sin errores', function () {
    Storage::fake('local');
    Storage::disk('local')->put('firmas/minima.png', pngTransparente(1, 1));

    expect(app(FirmaReporte::class)->componer('minima.png', 'X'))
        ->toStartWith('data:image/png;base64,');
});

it('limita el tamaño de una firma muy grande', function () {
    Storage::fake('local');
    Storage::disk('local')->put('firmas/grande.png', pngTransparente(1200, 500));

    $dataUri = app(FirmaReporte::class)->componer('grande.png', 'X');
    $png = base64_decode(substr($dataUri, strlen('data:image/png;base64,')));
    $info = getimagesizefromstring($png);

    expect($info[0])->toBeLessThanOrEqual(612);
    expect($info[1])->toBeLessThanOrEqual(252);
});

it('devuelve null si la firma no existe o no es una imagen válida', function () {
    Storage::fake('local');
    expect(app(FirmaReporte::class)->componer('no-existe.png', 'X'))->toBeNull();

    Storage::disk('local')->put('firmas/texto.png', 'no es una imagen');
    expect(app(FirmaReporte::class)->componer('texto.png', 'X'))->toBeNull();
});
```

- [ ] **Step 3: Ejecutar y ver que falla**

Run: `php artisan test --filter=FirmaReporteTest`
Expected: FAIL — clase `App\Services\FirmaReporte` no encontrada.

- [ ] **Step 4: Implementar `FirmaReporte`**

- `componer()` resuelve la ruta con la misma regla de hoy: rechaza rutas absolutas y `..`, lee de `Storage::disk('local')` y, si no existe, intenta el prefijo `firmas/`.
- Valida con `getimagesizefromstring()`: solo `image/png` e `image/jpeg`.
- Escala la firma para que no supere 600×240 conservando proporción, con `imagecopyresampled()` sobre un lienzo blanco con 6 px de margen. El lienzo usa `imagealphablending($lienzo, true)`; así el PNG transparente se aplana sobre el blanco al copiarlo.
- Marca con `imagettftext()`, tamaño 9, ángulo -30°, color `imagecolorallocatealpha($lienzo, 90, 90, 90, 105)` sobre `resources/fonts/DejaVuSans.ttf`. Repite la marca en una malla (paso ~260 px horizontal, ~70 px vertical) para cubrir el lienzo.
- Si la fuente no se puede leer, registra `Log::warning()` y sigue sin marca; si algo falla, devuelve `null`.
- Devuelve `data:image/png;base64,` + `base64_encode()` del `imagepng()` capturado con `ob_start()`.

- [ ] **Step 5: Ejecutar y ver que pasa**

Run: `php artisan test --filter=FirmaReporteTest`
Expected: PASS (4 tests).

- [ ] **Step 6: Commit (pedir confirmación antes)**

```bash
git add app/Services/FirmaReporte.php tests/Unit/FirmaReporteTest.php resources/fonts/DejaVuSans.ttf
git commit -m "feat: componer firma del reporte con marca de agua"
```

---

### Task 2: Código de verificación

**Files:**
- Create: `app/Services/CodigoVerificacionReporte.php`
- Create: `config/reportes.php`
- Create: `tests/Unit/CodigoVerificacionReporteTest.php`
- Modify: `.env.example` (después de `APP_KEY`)

**Interfaces:**
- Produces: `CodigoVerificacionReporte::generar(array $reporte): string` — código `XXXX-XXXX-XXXX`.

- [ ] **Step 1: Escribir el test que falla**

```php
<?php

use App\Services\CodigoVerificacionReporte;

function reporteParaCodigo(?string $resultado = '108'): array
{
    return [
        'paciente' => ['documento' => 'CC 1000000000'],
        'examenes' => [[
            'procedimiento_id' => 10,
            'nombre' => 'Glicemia basal',
            'orden_numero' => '2026-000154',
            'fecha_validacion' => '10-11-2026 10:42',
            'grupos' => [[
                'nombre' => null,
                'parametros' => [[
                    'nombre' => 'Glucosa en ayunas',
                    'resultado' => $resultado,
                    'unidades' => 'mg/dL',
                    'referencia' => '70 - 100',
                ]],
            ]],
        ]],
    ];
}

it('genera un código reproducible con formato XXXX-XXXX-XXXX', function () {
    $servicio = app(CodigoVerificacionReporte::class);
    $codigo = $servicio->generar(reporteParaCodigo());

    expect($codigo)->toMatch('/^[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}$/');
    expect($servicio->generar(reporteParaCodigo()))->toBe($codigo);
    expect($servicio->generar(reporteParaCodigo('109')))->not->toBe($codigo);
});

it('genera un código aunque no haya exámenes', function () {
    $codigo = app(CodigoVerificacionReporte::class)->generar([
        'paciente' => ['documento' => 'CC 1'],
        'examenes' => [],
    ]);

    expect($codigo)->toMatch('/^[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}$/');
});
```

- [ ] **Step 2: Ejecutar y ver que falla**

Run: `php artisan test --filter=CodigoVerificacionReporteTest`
Expected: FAIL — clase no encontrada.

- [ ] **Step 3: Crear `config/reportes.php` y la variable de entorno**

```php
<?php

return [
    'clave_verificacion' => env('REPORTE_VERIFICACION_KEY') ?: env('APP_KEY'),
];
```

En `.env.example`, después de `APP_KEY`:

```dotenv
# Clave para el código de verificación de reportes. Si se omite, se usa APP_KEY.
REPORTE_VERIFICACION_KEY=
```

- [ ] **Step 4: Implementar `CodigoVerificacionReporte`**

- Arma el canónico con `v = 1`, `paciente.documento`, los `orden_numero` únicos ordenados y, por examen, `procedimiento_id`, `nombre`, `fecha_validacion` y `[nombre, resultado, unidades, referencia]` de todos sus grupos.
- `json_encode($canonico, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)`.
- `strtoupper(substr(hash_hmac('sha256', $json, (string) config('reportes.clave_verificacion')), 0, 12))` y agrupa con `implode('-', str_split($codigo, 4))`.
- No incluye `fecha_emision`, `logo_src`, `firma_src`, `cierra_grupo` ni datos del profesional.

- [ ] **Step 5: Ejecutar y ver que pasa**

Run: `php artisan test --filter=CodigoVerificacionReporteTest`
Expected: PASS (2 tests).

- [ ] **Step 6: Commit (pedir confirmación antes)**

```bash
git add app/Services/CodigoVerificacionReporte.php config/reportes.php tests/Unit/CodigoVerificacionReporteTest.php .env.example
git commit -m "feat: código de verificación HMAC para el reporte"
```

---

### Task 3: `ConstruirReporteResultados` con modo pie y modo grupo

**Files:**
- Modify: `app/Services/ConstruirReporteResultados.php`
- Modify: `tests/Feature/OrdenEdicionReporteTest.php` (test «conserva las columnas…», alrededor de la línea 362)
- Test: `tests/Feature/OrdenEdicionReporteTest.php` (nuevos casos multi-profesional y firma ausente)

**Interfaces:**
- Consumes: `FirmaReporte::componer()`, `CodigoVerificacionReporte::generar()`.
- Produces: `$reporte['firma']`, `$reporte['codigo_verificacion']` y `$reporte['examenes'][*]['cierra_grupo']`.

- [ ] **Step 1: Actualizar el test existente (single) y verlo fallar**

En `conserva las columnas de resultados de texto y carga firmas y logos por nombre de archivo`, reemplazar las aserciones de firma por:

```php
$reporte = $servicio->paraOrden($orden);
$uri = 'data:image/png;base64,'.base64_encode($imagen);
expect($reporte['laboratorio']['logo_src'])->toBe($uri);
expect($reporte['firma']['src'])->toStartWith('data:image/png;base64,');
expect($reporte['firma']['src'])->not->toBe($uri);
expect($reporte['examenes'][0]['bacteriologo']['firma_src'])->toBeNull();
expect($reporte['codigo_verificacion'])->toMatch('/^[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}$/');
```

Y al final, tras cambiar la ruta a `firmas/profesional.png`:

```php
expect($servicio->paraOrden($orden)['firma']['src'])->toStartWith('data:image/png;base64,');
```

Run: `php artisan test --filter="conserva las columnas de resultados de texto"`
Expected: FAIL — `firma` no existe.

- [ ] **Step 2: Agregar el test multi-profesional y verlo fallar**

```php
it('firma al cierre de cada grupo cuando hay varios profesionales', function () {
    Storage::fake('public');
    Storage::fake('local');
    Storage::disk('local')->put('firmas/uno.png', pngTransparente(80, 30));
    Storage::disk('local')->put('firmas/dos.png', pngTransparente(90, 30));

    $empleadoUno = $this->user->empleado;
    $empleadoUno->update(['firma' => 'uno.png']);

    $usuarioDos = User::factory()->create();
    $usuarioDos->assignRole('prestador');
    $empleadoDos = Empleado::factory()->create([
        'user_id' => $usuarioDos->id,
        'empresa_id' => $this->sede->empresa_id,
        'tipo_documento_id' => 1,
        'firma' => 'dos.png',
    ]);

    $examen = Examen::factory()->create();
    $parametro = Parametro::create([
        'nombre' => 'Glucosa',
        'slug' => 'glucosa',
        'tipo_dato' => 'text',
        'unidades' => 'mg/dL',
        'examen_id' => $examen->id,
    ]);
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $primero = $orden->procedimientos()->firstOrFail();
    $primero->update(['empleado_id' => $empleadoUno->id]);
    Resultado::create(['parametro_id' => $parametro->id, 'procedimiento_id' => $primero->id, 'resultado' => '100']);

    $segundo = $orden->procedimientos()->create([
        'examen_id' => $examen->id,
        'fecha' => now(),
        'sede_id' => $this->sede->id,
        'diagnostico_principal' => 'Z017',
        'codigo_finalidad' => '15',
        'codigo_modalidad' => '01',
        'codigo_via_ingreso' => '01',
        'estado' => Estado::TERMINADO->value,
        'empleado_id' => $empleadoUno->id,
    ]);
    Resultado::create(['parametro_id' => $parametro->id, 'procedimiento_id' => $segundo->id, 'resultado' => '101']);

    $tercero = $orden->procedimientos()->create([
        'examen_id' => $examen->id,
        'fecha' => now(),
        'sede_id' => $this->sede->id,
        'diagnostico_principal' => 'Z017',
        'codigo_finalidad' => '15',
        'codigo_modalidad' => '01',
        'codigo_via_ingreso' => '01',
        'estado' => Estado::TERMINADO->value,
        'empleado_id' => $empleadoDos->id,
    ]);
    Resultado::create(['parametro_id' => $parametro->id, 'procedimiento_id' => $tercero->id, 'resultado' => '102']);

    $reporte = app(\App\Services\ConstruirReporteResultados::class)->paraOrden($orden);

    expect($reporte['firma'])->toBeNull();
    expect($reporte['examenes'])->toHaveCount(3);
    expect($reporte['examenes'][0]['cierra_grupo'])->toBeFalse();
    expect($reporte['examenes'][1]['cierra_grupo'])->toBeTrue();
    expect($reporte['examenes'][2]['cierra_grupo'])->toBeTrue();
    expect($reporte['examenes'][0]['bacteriologo']['firma_src'])->toBeNull();
    expect($reporte['examenes'][1]['bacteriologo']['firma_src'])->toStartWith('data:image/png;base64,');
    expect($reporte['examenes'][2]['bacteriologo']['firma_src'])->toStartWith('data:image/png;base64,');
});

it('genera el reporte sin firma si el archivo no existe', function () {
    Storage::fake('local');
    $empleado = $this->user->empleado;
    $empleado->update(['firma' => 'no-existe.png']);

    $examen = Examen::factory()->create();
    $parametro = Parametro::create([
        'nombre' => 'Glucosa',
        'slug' => 'glucosa',
        'tipo_dato' => 'text',
        'unidades' => 'mg/dL',
        'examen_id' => $examen->id,
    ]);
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $procedimiento = $orden->procedimientos()->firstOrFail();
    $procedimiento->update(['empleado_id' => $empleado->id]);
    Resultado::create(['parametro_id' => $parametro->id, 'procedimiento_id' => $procedimiento->id, 'resultado' => '100']);

    $reporte = app(\App\Services\ConstruirReporteResultados::class)->paraOrden($orden);

    expect($reporte['firma']['src'])->toBeNull();
    expect($reporte['firma']['nombre'])->toBe($this->user->name);
});
```

Agregar `use Illuminate\Support\Facades\Storage;` al encabezado del test.

Run: `php artisan test --filter="firma al cierre de cada grupo|genera el reporte sin firma"`
Expected: FAIL — `firma`/`cierra_grupo` no existen.

- [ ] **Step 3: Implementar la integración**

- Inyectar `FirmaReporte` y `CodigoVerificacionReporte` por constructor.
- En el `map` de exámenes, dejar `firma_src = null`; guardar nombre y documento como hoy.
- Después del `map`, calcular `empleado_id` por procedimiento (en el mismo orden) y `cierra_grupo` comparando con el siguiente; `true` en el último. `paraOrden()` ordena por `id`, así que el orden es estable.
- Armar el reporte base con `codigo_verificacion` calculado sobre ese arreglo.
- Referencia de la marca: `'ORDEN '.$ordenReporte->numero` o `'HISTORIA'`; marca completa: `referencia.' · '.$reporte['paciente']['documento'].' · '.$codigo`.
- Si hay un solo `empleado_id` distinto: `$reporte['firma'] = ['src' => componer(...), 'nombre' => ..., 'documento' => ...]`.
- Si hay varios: `$reporte['firma'] = null` y componer solo los exámenes con `cierra_grupo = true`.
- El logo sigue con `imagenComoDataUri()` sin cambios.

- [ ] **Step 4: Ejecutar los tests de integración**

Run: `php artisan test --filter="conserva las columnas de resultados de texto|firma al cierre de cada grupo|genera el reporte sin firma"`
Expected: PASS.

- [ ] **Step 5: Commit (pedir confirmación antes)**

```bash
git add app/Services/ConstruirReporteResultados.php tests/Feature/OrdenEdicionReporteTest.php
git commit -m "feat: firmar reporte en pie o por grupo de profesional"
```

---

### Task 4: Plantilla, pie fijo y paginación

**Files:**
- Modify: `resources/views/resultados/reporte.blade.php`
- Modify: `resources/views/resultados/reporte/_examen.blade.php`
- Test: `tests/Feature/OrdenEdicionReporteTest.php`

**Interfaces:**
- Consumes: `$reporte['firma']`, `$reporte['codigo_verificacion']`, `$examen['cierra_grupo']`.

- [ ] **Step 1: Agregar aserciones de plantilla al test single y verlo fallar**

```php
$html = $this->actingAs($this->user)->get(route('resultados.show', $procedimiento))->getContent();
expect(substr_count($html, 'class="pie-firma"'))->toBe(1);
expect($html)->toContain($reporte['firma']['src']);
expect($html)->toContain('Verificación');
```

Run: `php artisan test --filter="conserva las columnas de resultados de texto"`
Expected: FAIL — no existe `pie-firma`.

- [ ] **Step 2: Ajustar el cálculo de paginación**

En el bloque `@php` de `reporte.blade.php`:

```php
$firmaReporte = $reporte['firma'] ?? null;
$enPie = $firmaReporte !== null;
$ALTO_UTIL = $enPie ? 506 : (565 - 25);   // 32 mm de margen inferior con firma
$ALTO_FIRMA = $enPie ? 0 : 95;
```

Actualizar el comentario de las medidas: margen inferior de 20 mm sin firma en pie y 32 mm con firma.

- [ ] **Step 3: Mover la firma al pie**

En `_examen.blade.php`, condicionar el bloque de firma a:

```blade
@if (! $enPie && $bloque['ultimo'] && ($examen['cierra_grupo'] ?? false))
```

En `reporte.blade.php`, dentro del `<footer class="pie">`, agregar una celda central:

```blade
<td class="pie-firma">
    @if ($firmaReporte)
        @if (filled($firmaReporte['src'] ?? null))
            <img class="pie-firma-imagen" src="{{ $firmaReporte['src'] }}" alt="Firma de {{ $firmaReporte['nombre'] }}">
        @else
            <div class="pie-firma-vacia"></div>
        @endif
        <div class="pie-firma-linea">{{ $firmaReporte['nombre'] }}</div>
        <div class="pie-firma-cargo">Bacteriólogo(a) · {{ $firmaReporte['documento'] }}</div>
    @endif
</td>
```

Y en la celda de contacto:

```blade
@if (filled($reporte['codigo_verificacion'] ?? null))
    <div class="pie-verificacion">Verificación {{ $reporte['codigo_verificacion'] }}</div>
@endif
```

- [ ] **Step 4: Ajustar CSS y `@page`**

- `@page`: `margin: 60mm 14mm {{ $enPie ? '32mm' : '20mm' }} 14mm;` (sin `size`).
- `.pie`: `height: {{ $enPie ? '26mm' : '10mm' }}; bottom: {{ $enPie ? '-29mm' : '-14mm' }};`.
- Añadir `.pie-firma` (centrado), `.pie-firma-imagen` (alto 12 mm, ancho máximo 45 mm), `.pie-firma-vacia` (alto 12 mm), `.pie-firma-linea` (borde superior, 8 pt, negrita), `.pie-firma-cargo` y `.pie-verificacion` (7 pt).
- Mantener intacto el `@media print` de la vista web.

- [ ] **Step 5: Ejecutar tests y generar un PDF**

Run: `php artisan test --filter="conserva las columnas de resultados de texto|firma al cierre de cada grupo|genera un PDF"`
Expected: PASS.

Run: `php artisan test --filter=OrdenEdicionReporteTest`
Expected: PASS (salvo fallas preexistentes ajenas; anotarlas).

Verificación manual: abrir una orden con 10+ exámenes en `/resultados/orden/{orden}/reporte-resultados`, descargar el PDF y comprobar que **cada hoja tiene exactamente una firma** y que ningún examen se solapa con el pie. Repetir con el examen más largo.

- [ ] **Step 6: Commit (pedir confirmación antes)**

```bash
git add resources/views/resultados/reporte.blade.php resources/views/resultados/reporte/_examen.blade.php tests/Feature/OrdenEdicionReporteTest.php
git commit -m "feat: firmar cada hoja del reporte en el pie"
```

---

### Task 5: Ejemplo y documentación

**Files:**
- Modify: `resources/views/resultados/reporte/ejemplo.php`
- Modify: `tests/Feature/OrdenEdicionReporteTest.php`
- Modify: `docs/CHECKLIST_DEUDA_TECNICA.md:194-222` y tabla IM

- [ ] **Step 1: Actualizar el ejemplo y cubrirlo con un test**

- En `$base`, agregar `'codigo_verificacion' => 'A1B2-C3D4-E5F6'` y `'firma' => ['src' => $bacteriologa['firma_src'], 'nombre' => ..., 'documento' => ...]`.
- Dejar `firma_src => null` en los exámenes y `cierra_grupo => false` (el pie firma el documento).
- Agregar a `tests/Feature/OrdenEdicionReporteTest.php`:

```php
it('renderiza la plantilla de ejemplo con la firma en el pie', function () {
    $html = view('resultados.reporte', ['enPdf' => false])->render();

    expect($html)->toContain('Verificación');
    expect(substr_count($html, 'class="pie-firma"'))->toBe(1);
});
```

Run: `php artisan test --filter="renderiza la plantilla de ejemplo"`
Expected: PASS después de actualizar el ejemplo.

- [ ] **Step 2: Actualizar el contrato en la documentación**

En el bloque `$reporte` de `CHECKLIST_DEUDA_TECNICA.md`, agregar:

```php
'codigo_verificacion' => 'XXXX-XXXX-XXXX',
'firma' => ['src' => ?string, 'nombre', 'documento'] | null, // firma en pie (un solo profesional)
'examenes' => [[
    // ...
    'cierra_grupo' => bool, // varios profesionales: firma al cerrar el grupo
    'bacteriologo' => ['nombre', 'documento', 'firma_src' => ?string],
]],
```

Agregar la fila IM-05 en la tabla IM con estado `[x]`, fecha 2026-11-20 y una nota breve: firma compuesta con marca de agua, una por hoja en el caso normal, HMAC de verificación.

- [ ] **Step 3: Verificar y commit (pedir confirmación antes)**

Run: `php artisan test --filter="FirmaReporte|CodigoVerificacion|OrdenEdicionReporte"`
Expected: PASS.

```bash
git add resources/views/resultados/reporte/ejemplo.php tests/Feature/OrdenEdicionReporteTest.php docs/CHECKLIST_DEUDA_TECNICA.md
git commit -m "docs: contrato del reporte y ejemplo con firma compuesta"
```

---

### Task 6: Verificación final

- [ ] **Step 1: Ejecutar la suite dirigida**

Run: `php artisan test --filter="FirmaReporte|CodigoVerificacion|OrdenEdicionReporte"`
Expected: PASS.

- [ ] **Step 2: Formatear**

Run: `vendor/bin/pint app/Services/FirmaReporte.php app/Services/CodigoVerificacionReporte.php app/Services/ConstruirReporteResultados.php tests/Unit/FirmaReporteTest.php tests/Unit/CodigoVerificacionReporteTest.php tests/Feature/OrdenEdicionReporteTest.php`
Expected: sin errores.

- [ ] **Step 3: Limpiar vistas y revisar el PDF**

Run: `php artisan view:clear`
Expected: `Compiled views cleared successfully.`

Manual: orden con 10+ exámenes y el examen más largo; cada hoja firmada una vez; el código del pie coincide con el de la marca de agua; sin solapes.

- [ ] **Step 4: Reportar**

Resumir tests ejecutados, resultado y cualquier falla preexistente encontrada (por ejemplo, la vista `ordenes.show` con `direccion` nula en el test de remisiones).

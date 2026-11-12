# EX-02 y categorías de exámenes — diseño corto

**Fecha:** 2026-11-12
**Rama:** `be-relaciones-rotas`
**Origen:** hallazgo EX-02 del `CHECKLIST_DEUDA_TECNICA.md` y conversación del 2026-11-12.

## Intención y por qué

`Examen::procedimiento()` está declarada como `belongsTo`, pero la FK vive en
`procedimientos.examen_id`; la relación correcta es `hasMany`. `Examen::categoria()`
apunta a `categoria_id`, una columna que no existe, así que cualquier llamada falla.

Además, las categorías deben servir para **seleccionar exámenes rápido en la orden y
filtrar el catálogo**. Un examen puede pertenecer a varias categorías: por ejemplo,
Colesterol puede estar en “Perfil lipídico” y en “Química sanguínea”.

Cuando el usuario elige una categoría en la orden, se agregan **los exámenes que la
componen como líneas individuales**, cada uno con su precio, y se pueden quitar uno por
uno. La selección individual actual se conserva.

## Alcance de este incremento

- Corregir `Examen::procedimientos()` a `hasMany`.
- Convertir la relación examen ↔ categoría en muchos-a-muchos.
- Crear la tabla `categorias` y el pivote `categoria_examen` (`categoria_id`, `examen_id`).
- Migrar sin perder las filas existentes de la actual `categoria_examen`.
- Actualizar `Categoria` y `Examen` (incluida la eliminación de `Examen::categoria()`).
- Tests de relaciones y de esquema.

## No objetivos

- Selector rápido y filtro por categoría en la orden (EX-05; siguiente incremento).
- Perfiles facturables con precio de paquete (la opción B quedó descartada).
- Remisiones, RIPS, reportes o cambios al flujo de creación de órdenes.
- CRUD de categorías (EX-06).

## Enfoque

### 1. Migración segura para SQLite y MySQL

La tabla actual `categoria_examen` es en realidad la tabla de categorías: tiene
`nombre` único y `examen_id`. Para evitar `dropForeign`/`dropColumn` (problemáticos en
SQLite), la migración crea tablas nuevas y renombra:

1. Crear `categorias` (`id`, `nombre` único, `descripcion`, `timestamps`).
2. Copiar cada fila de `categoria_examen` a `categorias` conservando el `id`, el nombre y
   las fechas.
3. Crear el pivote temporal `categoria_examen_pivot` con `categoria_id`, `examen_id`,
   `timestamps` y PK compuesta.
4. Copiar los pares `(categoria_id = id, examen_id)` de la tabla vieja.
5. Eliminar la tabla vieja y renombrar el pivote a `categoria_examen`.

La migración debe poder correr sobre una base vacía y sobre una con filas. En `down()`
se reconstruye la tabla vieja con un examen por categoría (la primera asociación) y se
eliminan las tablas nuevas; las asociaciones adicionales del muchos-a-muchos no se
pueden recuperar en un downgrade.

### 2. Modelos

- `Categoria`: `$table = 'categorias'`, `$fillable = ['nombre', 'descripcion']`,
  `examenes(): BelongsToMany` contra `categoria_examen`.
- `Examen`: `procedimientos(): HasMany`, `categorias(): BelongsToMany` contra
  `categoria_examen`; se elimina `categoria()`, que no tiene columna de respaldo.

### 3. Tests

- Un examen tiene muchos procedimientos y un procedimiento pertenece a un examen.
- Un examen puede pertenecer a dos categorías y una categoría puede tener varios
  exámenes.
- El esquema final tiene `categorias` y `categoria_examen` con `categoria_id` y
  `examen_id`; `categorias` ya no tiene `examen_id`.
- La suite existente sigue pasando.

## Riesgos y decisiones

- **Datos existentes:** se preservan las categorías y su examen original. El nombre
  sigue siendo único.
- **SQLite:** se evita alterar columnas con FK; se usan tablas nuevas y `rename`.
- **Compatibilidad:** no hay código de aplicación que consulte `categoria_examen` ni
  `Examen::categoria()` hoy; el cambio no afecta el flujo de órdenes.
- **Siguiente incremento:** exponer las categorías en
  `/api/static-data-for-frontend` y agregar el selector rápido en la orden, sin duplicar
  exámenes ya cargados.

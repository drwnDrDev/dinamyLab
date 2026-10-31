# Despliegue por instancia (cPanel + Git)

Runbook operativo. Cada cliente tiene **una instancia completa**: su propio
directorio, su `.env`, su base de datos y su `storage/`. El código es el mismo
repositorio y el mismo tag para todas las instancias.

Diseño de referencia: `docs/superpowers/specs/2026-10-30-multitenant-design.md`.

---

## 0. Antes de empezar

### Decisión pendiente: `vendor/`

El repositorio **no versiona `vendor/`** (commit `041d1fa4`). En cPanel eso solo
funciona si el deploy puede ejecutar Composer. Hay que resolver el **spike S0**:

- **Opción A** — el servidor tiene PHP CLI y Composer funciona:
  versionar `composer.phar` y añadir como primera tarea de `.cpanel.yml`:
  `php composer.phar install --no-dev --optimize-autoloader`
- **Opción B** — no hay Composer: revertir el commit `041d1fa4` para volver a
  versionar `vendor/`.

Comprobación rápida en cPanel → *Terminal*:

```bash
php -v
composer -V
```

Anotar la versión de PHP. `composer.json` exige `^8.5`; si el host no la ofrece,
bajarla a `^8.3` (Laravel 13 lo soporta).

### Rutas del despliegue

```
/home/USUARIO/apps/<cliente>/          ← clone del repo (raíz de la app)
/home/USUARIO/apps/<cliente>/public/   ← document root del dominio
```

El clone **es** el directorio de la aplicación: no se copia a otro sitio. Así
`vendor/` y `storage/` no se duplican en cada deploy.

---

## 1. Preparación de una instancia nueva

Ejecutar una sola vez por cliente.

1. **Base de datos.** En cPanel → *MySQL Databases*: crear la BD y un usuario con
   todos los privilegios solo sobre esa BD. Anotar nombre, usuario y contraseña.

2. **Dominio.** Crear el addon domain o subdominio con document root en
   `/home/USUARIO/apps/<cliente>/public`. Activar AutoSSL.

3. **Clone.** En cPanel → *Git Version Control* → *Create*: clonar el repositorio
   en `/home/USUARIO/apps/<cliente>` y seleccionar el tag a desplegar.

4. **`.env`.** Copiar `.env.example` a `.env` y completar:

   | Variable | Valor |
   |---|---|
   | `APP_NAME` | Nombre comercial del cliente |
   | `APP_ENV` | `production` |
   | `APP_DEBUG` | `false` |
   | `APP_URL` | `https://<dominio>` |
   | `APP_INSTANCE` | Nombre corto del cliente (ej. `biotek`) |
   | `APP_HOSTS` | Dominios de esa instancia, separados por comas |
   | `SESSION_DOMAIN` | **vacío** (cookie host-only) |
   | `SESSION_COOKIE` | `<instancia>_session` |
   | `DB_*` | Los de su propia BD |
   | `CLIENTE_*` | Razón social, NIT, sede, código de prestador y admin |

   > `APP_INSTANCE`, `APP_HOSTS` y `SESSION_COOKIE` son lo que impide que una
   > instancia atienda el dominio de la otra. Si `SESSION_DOMAIN` lleva un punto
   > inicial (`.dominio.com`), la cookie se comparte entre clientes.

5. **Clave y almacenamiento.**

   ```bash
   php artisan key:generate
   php artisan storage:link
   ```

6. **Verificar la configuración.**

   ```bash
   php artisan instancia:verificar
   ```

   Debe salir con código 0. Si falla, **no continuar**: revisar el `.env`.

7. **Catálogos.**

   ```bash
   php artisan db:seed --class=CatalogoBaseSeeder --force
   php artisan db:seed --class=ClienteSeeder --force
   ```

   `CatalogoBaseSeeder` siembra el catálogo común (países, municipios, CUPS,
   exámenes, roles). Tarda unos **30–60 segundos**; si el host corta por tiempo
   de ejecución, subir `max_execution_time` temporalmente.

   `ClienteSeeder` crea la empresa, su sede y el usuario admin a partir de las
   variables `CLIENTE_*`. Es idempotente.

   > No ejecutar `db:seed` sin `--class`: `DatabaseSeeder` siembra datos de
   > demostración (Biotek y San Pablo), no datos de producción.

8. **Cron.** En cPanel → *Cron Jobs*, con la ruta absoluta de la instancia:

   | Tarea | Frecuencia | Comando |
   |---|---|---|
   | Cola | cada minuto | `cd ~/apps/<cliente> && php artisan queue:work --stop-when-empty --max-time=50` |
   | Backup | diario | `cd ~/apps/<cliente> && mysqldump -u USUARIO -p'CLAVE' BD \| gzip > ~/backups/<cliente>-$(date +\%F).sql.gz` |

   La tarea de cola solo hace falta si `QUEUE_CONNECTION` no es `sync`.

9. **Verificación final.** `/up` → 200, `/login` → 200, login del admin →
   `/dashboard`, y acceder por el dominio del **otro** cliente → **503**.

---

## 2. Desplegar una versión

1. Crear el tag en el repositorio y hacer push:

   ```bash
   git tag v1.5.0
   git push origin v1.5.0
   ```

2. En cPanel → *Git Version Control* → `<cliente>` → *Deploy HEAD Commit*.
   Las tareas de `.cpanel.yml` reconstruyen las cachés.

3. Si el tag trae migraciones, ejecutarlas **a mano** (ver §3).

4. Verificar: `/up`, `/login` y un listado con datos reales.

5. Repetir en la segunda instancia **solo después** de verificar la primera.

> **Nunca** configurar el webhook de auto-deploy en las dos instancias a la vez:
> un commit roto llegaría a los dos clientes en el mismo segundo.

---

## 3. Migraciones

Son un paso **manual y explícito**, nunca parte del deploy automático.

```bash
cd ~/apps/<cliente>
php artisan down
php artisan migrate --force
php artisan config:cache
php artisan up
```

Ejecutar primero en una instancia, verificar, y luego en la otra.

---

## 4. Operación

### Caché de configuración

`config:cache` **congela el `.env`**. Si se cambia una variable de entorno hay
que volver a ejecutar `php artisan config:cache`; de lo contrario la aplicación
sigue usando el valor anterior.

### Disco compartido

Las dos instancias comparten la cuenta y la cuota de disco. Si una llena el
disco, caen las dos.

- Revisar el uso en cPanel → *Disk Usage*.
- Activar las alertas de cuota del hosting.
- Los backups **deben descargarse fuera del hosting**: un backup en el mismo
  disco no es un backup.

### Logs

Cada instancia escribe en su propio `storage/logs/laravel.log`. Para revisar:

```bash
tail -n 100 ~/apps/<cliente>/storage/logs/laravel.log
```

---

## 5. Regla de release

- Todas las instancias corren **el mismo tag**.
- Si una se queda atrás, se registra como deuda explícita y se nivela en el
  siguiente despliegue.
- Ninguna diferencia entre clientes vive en el código: vive en el `.env`.
  Si aparece un `if` con el nombre de un cliente, es un error de diseño.

### Registro de instancias

| Cliente | Dominio | BD | Tag desplegado | Fecha |
|---|---|---|---|---|
| — | — | — | — | — |

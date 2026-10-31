# Entorno de desarrollo: WSL 2 + Docker

Guía para montar DinamyLAB en Windows usando WSL 2 y Docker. Reemplaza el
entorno anterior (XAMPP / PHP en Windows + SQLite).

## Qué cambió

| Antes                         | Ahora                                   |
|-------------------------------|-----------------------------------------|
| PHP 8.2.12 de XAMPP (Windows) | PHP 8.5 en contenedor (último parche)   |
| SQLite (`database.sqlite`)    | MySQL 8.4 en contenedor                 |
| `php artisan serve`           | nginx + PHP-FPM                         |
| `npm/pnpm run dev` en Windows | Vite en contenedor (Node 22 + pnpm)     |

> ⚠️ **Seguridad:** PHP 8.2.12 es vulnerable a PHP2Shell
> (CVE-2026-109618 / CVE-2026-109619, ejecución remota de código, explotada
> activamente). Si aún tienes XAMPP corriendo, apágalo o actualízalo.

La versión de Laravel **no** cambió (sigue 12.x). La actualización a Laravel 13
se hará en una rama aparte.

## Requisitos (una sola vez)

1. **WSL 2 con Ubuntu.** En PowerShell como administrador:
   ```powershell
   wsl --install -d Ubuntu
   ```
   Reinicia y crea tu usuario de Linux cuando lo pida.

2. **Docker Desktop** para Windows, y activar la integración con WSL:
   *Settings → Resources → WSL Integration → activar tu distro (Ubuntu)*.

   Verifica desde la terminal de Ubuntu:
   ```bash
   docker --version
   docker compose version
   ```

3. **Git dentro de WSL** con tu identidad:
   ```bash
   sudo apt update && sudo apt install -y git
   git config --global user.name "Tu Nombre"
   git config --global user.email "tu@correo.com"
   ```

## Clonar el proyecto

Clona **dentro del sistema de archivos de Linux** (`~/...`), no en `/mnt/c/...`.
Desde `/mnt/c` todo es muchísimo más lento y el HMR de Vite no detecta cambios.

```bash
mkdir -p ~/Projects && cd ~/Projects
git clone <url-del-repo> dinamyLab
cd dinamyLab
```

Para abrirlo en VS Code: `code .` (requiere la extensión **WSL**).

## Primer arranque

```bash
cp .env.example .env
```

Edita `.env` y cambia **`DB_PASSWORD`** y **`DB_ROOT_PASSWORD`** (cualquier valor;
solo es tu BD local). Puedes generarlas con `openssl rand -hex 16`.

```bash
docker compose up -d --build                     # construye y levanta todo
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose restart queue                     # el worker necesita la tabla jobs
```

La primera vez, el contenedor `vite` instala dependencias con pnpm; tarda un
par de minutos. Sigue el progreso con `docker compose logs -f vite`.

Abre **http://localhost:8000**.

## Servicios

| Servicio | Qué es                     | Puerto en tu máquina     |
|----------|----------------------------|--------------------------|
| `web`    | nginx                      | `8000` (`APP_PORT`)      |
| `app`    | PHP 8.5-FPM (Laravel)      | —                        |
| `queue`  | `php artisan queue:work`   | —                        |
| `db`     | MySQL 8.4                  | `3307` (`FORWARD_DB_PORT`) |
| `vite`   | Vite dev server + HMR      | `5173`                   |

Los datos de MySQL viven en el volumen `dbdata`; sobreviven a `docker compose down`.

### Conectarse a la BD (DBeaver, Workbench, etc.)

- Host: `127.0.0.1`, puerto: `3307`
- Usuario / clave / base: los de `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE` en `.env`

Se usa `3307` para no chocar con un MySQL de XAMPP en `3306`.

## Comandos del día a día

```bash
docker compose up -d                 # levantar
docker compose down                  # apagar (conserva la BD)
docker compose ps                    # estado de los servicios
docker compose logs -f app           # logs (app, queue, vite, db, web)

docker compose exec app php artisan <comando>
docker compose exec app composer <comando>
docker compose exec app php artisan tinker
docker compose exec app php artisan test

docker compose exec vite corepack pnpm <comando>   # p. ej. pnpm build
docker compose exec db mysql -u root -p            # consola MySQL
```

Tip: un alias en `~/.bashrc` ahorra escribir:
```bash
alias art='docker compose exec app php artisan'
```

### Reiniciar la BD desde cero

```bash
docker compose exec app php artisan migrate:fresh --seed
```

> Borra **todos** los datos locales.

## Cosas a tener en cuenta

- **`vendor/` está versionado en git.** Al correr `composer install` dentro del
  contenedor, Composer regenera `vendor/autoload.php` y `vendor/composer/*`
  (mismo contenido, otro orden). **No hagas commit de esos cambios**; descártalos
  con `git checkout -- vendor/`.
- **Los tests siguen usando SQLite en memoria** (`phpunit.xml`), no MySQL.
- **Búsquedas `LIKE`:** MySQL con `utf8mb4_unicode_ci` no distingue mayúsculas
  ni tildes (`jose` encuentra `José`). SQLite sí distinguía tildes, así que el
  comportamiento de búsqueda mejora, no empeora.
- **MySQL es más estricto que SQLite** con tipos y llaves foráneas: las nuevas
  migraciones deben probarse en este entorno, no solo en SQLite.
- **Zona horaria** de PHP y MySQL: `America/Bogota`.

## Problemas comunes

| Síntoma | Solución |
|---------|----------|
| `docker: command not found` en WSL | Activar la integración WSL en Docker Desktop y reabrir la terminal. |
| `queue` en estado *Restarting* | Aún no corriste `migrate`. Ejecútalo y `docker compose restart queue`. |
| Error de permisos en `storage/` o archivos creados como root | Tu UID no es 1000 (`id -u`). Agrega `UID=` y `GID=` con tus valores al `.env` y `docker compose up -d --build`. |
| Puerto 8000 / 3307 / 5173 ocupado | Cambia `APP_PORT` o `FORWARD_DB_PORT` en `.env`, o apaga el proceso que lo usa (XAMPP). |
| Estilos/JS no cargan, o cargan versiones viejas | Revisa `docker compose logs vite`. Si existe `public/hot` sin Vite corriendo, bórralo. |
| `ERR_PNPM_IGNORED_BUILDS` | Ya resuelto en `pnpm-workspace.yaml` (`allowBuilds`). Si aparece otro paquete, agrégalo ahí. |
| Cambios en el código no se reflejan | Confirma que el proyecto está en `~/...` y no en `/mnt/c/...`. |

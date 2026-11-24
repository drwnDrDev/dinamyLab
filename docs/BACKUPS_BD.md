# Respaldos periódicos de MySQL

El comando `php artisan db:backup` exporta el esquema y los datos de la conexión
predeterminada de Laravel, incluidos vistas y triggers. Usa la configuración
resuelta de la conexión (`DB_*` o `DB_URL`) y el cliente `mysqldump` de MySQL.
No instala dependencias nuevas. Las rutinas y eventos SQL no se exportan; la
aplicación no los utiliza.

## Programación y configuración

Por defecto se ejecuta **todos los días a las 02:00, America/Bogota**, y conserva
las copias de los últimos **14 días**. Se puede ajustar en el `.env`:

```dotenv
DB_BACKUP_ENABLED=true
DB_BACKUP_CRON="0 2 * * *"
DB_BACKUP_TIMEZONE=America/Bogota
DB_BACKUP_RETENTION_DAYS=14
DB_BACKUP_TIMEOUT=3600
DB_BACKUP_BINARY=mysqldump
# Opcional: ruta absoluta privada, con permiso de escritura.
# DB_BACKUP_PATH=/home/usuario/backups/cliente
```

Después de cambiar estas variables, reconstruir la caché si se utiliza:

```bash
php artisan config:cache
php artisan schedule:list --timezone=America/Bogota
```

`DB_BACKUP_ENABLED=false` desactiva la programación; el comando manual sigue
disponible. Cada copia contiene toda la BD de esa instancia. En Docker solo se
ejecutan tareas mientras Docker y los servicios estén encendidos; no se recuperan
automáticamente las ejecuciones perdidas mientras el equipo estaba apagado.

## Docker

`compose.yaml` incluye `scheduler`, que ejecuta `php artisan schedule:work` con
la misma imagen y el mismo volumen de archivos que `app`. Al levantar el entorno
completo también se activa este servicio. Para activarlo en un entorno existente:

```bash
docker compose up -d scheduler
docker compose exec scheduler php artisan schedule:list --timezone=America/Bogota
docker compose exec scheduler php artisan db:backup
docker compose logs --tail=100 scheduler
```

Si falta `mysqldump`, reconstruir `app` y recrear los servicios PHP:

```bash
docker compose build app
docker compose up -d app queue scheduler
```

Los archivos se guardan por defecto en `storage/app/private/backups/` del
proyecto, fuera del volumen de datos de MySQL y excluidos de Git. No ejecutar
simultáneamente otro scheduler o cron para la misma instancia.

## cPanel

Comprobar `mysqldump --version` en el hosting. Si el cliente está en otra ruta,
definirla en `DB_BACKUP_BINARY`. El usuario de BD necesita permisos para leer
tablas y exportar vistas y triggers. Se usa `--no-tablespaces` para evitar exigir
el permiso global `PROCESS`.

En **Cron Jobs**, añadir una única tarea cada minuto por instancia, con las rutas
absolutas de PHP y del proyecto. Ajustar `/home/usuario/apps/cliente` y el binario
PHP a los del hosting:

```cron
* * * * * cd /home/usuario/apps/cliente && /usr/local/bin/php artisan schedule:run >> /home/usuario/apps/cliente/storage/logs/scheduler.log 2>&1
```

El scheduler decide cuándo corresponde el backup según `DB_BACKUP_CRON` y su zona
horaria. Eliminar el cron antiguo que invocaba `mysqldump` directamente. Verificar
una primera copia con `php artisan db:backup` y revisar `php artisan schedule:list`.

## Archivos, retención y fallos

- Se generan archivos `db-<instancia>-<identificador>-<fecha>-<aleatorio>.sql.gz`.
  El identificador distingue instancia, servidor y BD para aislar la retención.
- Los directorios nuevos tienen permisos `0700` y las copias `0600`. Se rechazan
  destinos dentro de `public/` o `storage/app/public/`.
- Las credenciales se pasan mediante un archivo temporal privado, nunca como
  argumentos de consola. El archivo se elimina al terminar, también ante errores.
- El SQL se comprime por bloques. El archivo `.part` solo cambia a `.sql.gz`
  después de que `mysqldump` termine con éxito y se cierre la compresión.
- Un bloqueo de archivo impide solapar copias manuales y programadas.
- Después de completar una copia se eliminan únicamente los `.sql.gz` de esa
  instancia y conexión cuya fecha de modificación supera la retención. Si el
  volcado falla, se conservan las copias anteriores y se elimina el parcial.
- Los fallos retornan código `1` y quedan en el log configurado de Laravel
  (normalmente `storage/logs/laravel.log`), sin volcar el SQL ni stderr del cliente.
  No hay notificaciones externas configuradas.

El volcado usa `--single-transaction --quick` para obtener una copia consistente
de tablas InnoDB mientras continúa el uso de la aplicación. No ejecutar migraciones
ni cambios de estructura durante el respaldo. Las tablas no transaccionales
requieren una estrategia de bloqueo propia. Referencia:
[manual de mysqldump de MySQL 8.4](https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html).

Las copias están comprimidas, sin cifrado adicional. Descargarlas o replicarlas a
un destino privado fuera de este equipo/hosting, con acceso restringido. La copia
externa no queda automatizada hasta definir y configurar ese destino. Estos
respaldos cubren la BD; archivos como firmas, logos y `.env` requieren su propio
respaldo.

## Verificar y restaurar

Comprobar la integridad de una copia, sin imprimir datos de pacientes:

```bash
gzip -t /ruta/privada/copia.sql.gz
```

Validar la restauración periódicamente en una **BD de prueba vacía y separada**,
usando un usuario con permisos sobre esa BD. Crear la BD antes de importar y
confirmar el servidor y nombre de destino:

```bash
umask 077
gunzip -c /ruta/privada/copia.sql.gz > /ruta/privada/restauracion.sql
mysql -h HOST_PRUEBAS -u USUARIO_PRUEBAS -p BD_PRUEBAS < /ruta/privada/restauracion.sql
rm /ruta/privada/restauracion.sql
```

El SQL puede reemplazar tablas existentes: usar siempre una BD
de prueba para validar. Comprobar tablas, conteos y una muestra de órdenes y
resultados antes de considerar recuperable la copia.

<?php

return [
    'enabled' => env('DB_BACKUP_ENABLED', true),
    'schedule' => env('DB_BACKUP_CRON', '0 2 * * *'),
    'timezone' => env('DB_BACKUP_TIMEZONE', 'America/Bogota'),
    'path' => env('DB_BACKUP_PATH', storage_path('app/private/backups')),
    'retention_days' => (int) env('DB_BACKUP_RETENTION_DAYS', 14),
    'timeout' => (int) env('DB_BACKUP_TIMEOUT', 3600),
    'binary' => env('DB_BACKUP_BINARY', 'mysqldump'),
];

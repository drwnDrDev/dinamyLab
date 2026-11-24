<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->backupTestPath = sys_get_temp_dir().'/dinamylab-backup-'.Str::uuid();
    File::makeDirectory($this->backupTestPath, 0700, true);
    $this->backupDirectory = $this->backupTestPath.'/backups';
    $this->backupClient = $this->backupTestPath.'/mysqldump';

    config([
        'backups.path' => $this->backupDirectory,
        'backups.binary' => $this->backupClient,
        'backups.retention_days' => 14,
        'backups.timeout' => 10,
        'instancias.actual' => 'laboratorio',
        'database.default' => 'backup_test',
        'database.connections.backup_test' => [
            'driver' => 'mysql',
            'host' => 'db',
            'port' => 3306,
            'database' => 'laboratorio',
            'username' => 'backup_user',
            'password' => "clave con \"comillas\" \\ # ; $ y\nsalto",
            'charset' => 'utf8mb4',
        ],
    ]);

    $this->writeBackupClient = function (string $body): void {
        file_put_contents($this->backupClient, '#!'.PHP_BINARY."\n<?php\n".$body);
        chmod($this->backupClient, 0700);
    };

    ($this->writeBackupClient)('echo "CREATE TABLE ejemplo (id INT);\nINSERT INTO ejemplo VALUES (42);\n";');
});

afterEach(function () {
    DB::setDefaultConnection('sqlite');
    File::deleteDirectory($this->backupTestPath);
});

test('genera SQL comprimido privado sin dejar credenciales ni temporales', function () {
    $capture = var_export($this->backupTestPath.'/capture.json', true);
    ($this->writeBackupClient)(
        '$file = substr($argv[1], strlen("--defaults-file="));'.
        'file_put_contents('.$capture.', json_encode(["args" => $argv, "options" => file_get_contents($file), "mode" => fileperms($file) & 0777]));'.
        'echo "CREATE TABLE ejemplo (id INT);\nINSERT INTO ejemplo VALUES (42);\n";'
    );

    $this->artisan('db:backup')->assertSuccessful();

    $files = glob($this->backupDirectory.'/*.sql.gz');
    expect($files)->toHaveCount(1);
    expect(gzdecode(file_get_contents($files[0])))->toContain('INSERT INTO ejemplo VALUES (42)');
    expect(fileperms($files[0]) & 0777)->toBe(0600);
    expect(fileperms($this->backupDirectory) & 0777)->toBe(0700);
    expect(glob($this->backupDirectory.'/*.part'))->toBe([]);
    expect(glob($this->backupDirectory.'/.mysql-*'))->toBe([]);

    $capture = json_decode(file_get_contents($this->backupTestPath.'/capture.json'), true);
    expect($capture['mode'])->toBe(0600);
    expect($capture['args'])->toContain('--single-transaction', '--quick', '--skip-lock-tables', '--no-tablespaces', '--set-gtid-purged=OFF');
    expect(implode(' ', $capture['args']))->not->toContain('backup_user', 'clave con');
    expect($capture['options'])->toContain('user="backup_user"', 'protocol="TCP"', 'password="clave con \\"comillas\\" \\\\ # ; $ y\\nsalto"');
});

test('conserva las copias anteriores y elimina el parcial cuando falla mysqldump', function () {
    $this->artisan('db:backup')->assertSuccessful();
    $previous = glob($this->backupDirectory.'/*.sql.gz')[0];
    touch($previous, now()->subDays(20)->timestamp);
    ($this->writeBackupClient)('echo "SQL incompleto"; fwrite(STDERR, "error privado"); exit(2);');

    $this->artisan('db:backup')
        ->doesntExpectOutputToContain('error privado')
        ->assertFailed();

    expect(glob($this->backupDirectory.'/*.sql.gz'))->toBe([$previous]);
    expect(glob($this->backupDirectory.'/*.part'))->toBe([]);
    expect(glob($this->backupDirectory.'/.mysql-*'))->toBe([]);
});

test('rechaza un volcado vacío aunque el cliente termine sin error', function () {
    ($this->writeBackupClient)('exit(0);');

    $this->artisan('db:backup')->assertFailed();

    expect(glob($this->backupDirectory.'/*.sql.gz'))->toBe([]);
    expect(glob($this->backupDirectory.'/*.part'))->toBe([]);
});

test('depura solo copias vencidas de la misma instancia y conexión', function () {
    $this->artisan('db:backup')->assertSuccessful();
    $old = glob($this->backupDirectory.'/*.sql.gz')[0];
    touch($old, now()->subDays(20)->timestamp);
    $unrelated = $this->backupDirectory.'/otra-base.sql.gz';
    file_put_contents($unrelated, 'otra copia');
    touch($unrelated, now()->subDays(20)->timestamp);

    config(['instancias.actual' => 'otro-laboratorio']);
    $this->artisan('db:backup')->assertSuccessful();
    $otherInstance = array_values(array_diff(glob($this->backupDirectory.'/*.sql.gz'), [$old, $unrelated]))[0];
    touch($otherInstance, now()->subDays(20)->timestamp);

    config(['instancias.actual' => 'laboratorio']);
    $this->artisan('db:backup')->assertSuccessful();
    $this->artisan('db:backup')->assertSuccessful();

    expect(file_exists($old))->toBeFalse();
    expect(file_exists($unrelated))->toBeTrue();
    expect(file_exists($otherInstance))->toBeTrue();
    expect(glob($this->backupDirectory.'/*.sql.gz'))->toHaveCount(4);
});

test('impide respaldos simultáneos incluso si uno se ejecuta manualmente', function () {
    $this->artisan('db:backup')->assertSuccessful();
    $lock = fopen(glob($this->backupDirectory.'/*lock')[0], 'c');
    flock($lock, LOCK_EX);

    try {
        $this->artisan('db:backup')->expectsOutputToContain('Ya hay un respaldo en ejecución')->assertFailed();
        expect(glob($this->backupDirectory.'/*.sql.gz'))->toHaveCount(1);
    } finally {
        fclose($lock);
    }
});

test('limpia temporales al exceder el tiempo máximo', function () {
    config(['backups.timeout' => 1]);
    ($this->writeBackupClient)('echo "SQL parcial"; sleep(3);');

    $this->artisan('db:backup')->assertFailed();

    expect(glob($this->backupDirectory.'/*.sql.gz'))->toBe([]);
    expect(glob($this->backupDirectory.'/*.part'))->toBe([]);
    expect(glob($this->backupDirectory.'/.mysql-*'))->toBe([]);
});

test('rechaza destinos públicos', function (string $location) {
    config(['backups.path' => $location === 'public' ? public_path() : storage_path('app/public')]);

    $this->artisan('db:backup')->expectsOutputToContain('fuera de los directorios públicos')->assertFailed();
})->with(['public', 'storage-public']);

test('rechaza motores no soportados y retenciones inválidas', function () {
    config(['database.default' => 'sqlite']);
    $this->artisan('db:backup')->assertFailed();

    config(['database.default' => 'backup_test', 'backups.retention_days' => 0]);
    $this->artisan('db:backup')->assertFailed();

    expect(is_dir($this->backupDirectory))->toBeFalse();
});

test('el scheduler registra el backup diario con la zona horaria configurada', function () {
    $events = collect(app(Schedule::class)->events())->filter(fn ($event) => str_contains($event->command ?? '', 'db:backup'));

    expect($events)->toHaveCount(1);
    expect($events->first()->expression)->toBe('0 2 * * *');
    expect($events->first()->timezone)->toBe('America/Bogota');
});

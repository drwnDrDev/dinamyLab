<?php

use App\Models\Persona;
use Database\Seeders\PaisSeeder;
use Database\Seeders\TipoDocumentoSeeder;

beforeEach(function () {
    $this->seed(PaisSeeder::class);
    $this->seed(TipoDocumentoSeeder::class);
});

it('normaliza los datos legados de personas al ejecutar la migracion dos veces', function () {
    $personas = [
        'h' => Persona::factory()->create(['sexo' => 'H']),
        'm' => Persona::factory()->create(['sexo' => 'M']),
        'f' => Persona::factory()->create(['sexo' => 'F']),
        'i' => Persona::factory()->create(['sexo' => 'I']),
        'nulo' => Persona::factory()->create(['sexo' => null]),
        'invalido' => Persona::factory()->create(['sexo' => 'X']),
        'conEspacios' => Persona::factory()->create(['sexo' => ' m ']),
    ];

    $migracion = require database_path('migrations/2026_11_10_000100_normalize_personas_sexo.php');

    $migracion->up();
    $migracion->up();

    expect($personas['h']->fresh()->sexo)->toBe('M');
    expect($personas['m']->fresh()->sexo)->toBe('M');
    expect($personas['f']->fresh()->sexo)->toBe('F');
    expect($personas['i']->fresh()->sexo)->toBe('I');
    expect($personas['nulo']->fresh()->sexo)->toBeNull();
    expect($personas['invalido']->fresh()->sexo)->toBe('I');
    expect($personas['conEspacios']->fresh()->sexo)->toBe('M');
});

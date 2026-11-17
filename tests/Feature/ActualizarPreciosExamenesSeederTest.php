<?php

use App\Models\Examen;
use Database\Seeders\ActualizarPreciosExamenesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('actualiza solo precios de exámenes existentes que tienen correspondencia en el CSV', function () {
    $glicemia = Examen::factory()->create([
        'nombre' => 'Glicemia',
        'valor' => 18000,
    ]);
    $sinPrecioEnCsv = Examen::factory()->create([
        'nombre' => 'Hemoclasificación',
        'valor' => 10000,
    ]);

    $this->seed(ActualizarPreciosExamenesSeeder::class);

    expect($glicemia->fresh()->valor)->toBe('20000.00')
        ->and($sinPrecioEnCsv->fresh()->valor)->toBe('10000.00')
        ->and(Examen::count())->toBe(2);
});

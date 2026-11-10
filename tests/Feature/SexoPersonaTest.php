<?php

use App\Models\Persona;
use App\Models\User;
use Database\Seeders\PaisSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TipoDocumentoSeeder;

beforeEach(function () {
    $this->seed(PaisSeeder::class);
    $this->seed(TipoDocumentoSeeder::class);
    $this->seed(RolesYPermisosSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('prestador');
});

it('rechaza H y acepta F M I al actualizar una persona', function () {
    $persona = Persona::factory()->create([
        'primer_nombre' => 'Ana',
        'primer_apellido' => 'Pérez',
        'sexo' => 'F',
    ]);

    $datos = [
        'primer_nombre' => 'Ana',
        'segundo_nombre' => null,
        'primer_apellido' => 'Pérez',
        'segundo_apellido' => null,
        'fecha_nacimiento' => '1990-01-01',
    ];

    $this->actingAs($this->user)
        ->put(route('personas.update', $persona), $datos + ['sexo' => 'H'])
        ->assertSessionHasErrors('sexo');

    expect($persona->fresh()->sexo)->toBe('F');

    foreach (['M', 'I', 'F'] as $sexo) {
        $this->actingAs($this->user)
            ->put(route('personas.update', $persona), $datos + ['sexo' => $sexo])
            ->assertRedirect(route('personas.show', $persona));

        expect($persona->fresh()->sexo)->toBe($sexo);
    }
});

it('rechaza H en la actualizacion por API', function () {
    $this->withoutMiddleware();
    $persona = Persona::factory()->create(['sexo' => 'F']);

    $this->actingAs($this->user)
        ->putJson("/api/personas/{$persona->id}", ['sexo' => 'H'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('sexo');

    expect($persona->fresh()->sexo)->toBe('F');
});

it('resuelve el nombre del catalogo y el codigo RIPS', function () {
    $personaI = Persona::factory()->create(['sexo' => 'I']);
    $personaM = Persona::factory()->create(['sexo' => 'M']);
    $personaF = Persona::factory()->create(['sexo' => 'F']);
    $personaSin = Persona::factory()->create(['sexo' => null]);

    expect($personaI->sexo_nombre)->toBe('Indeterminado o Intersexual');
    expect($personaI->sexo_rips)->toBe('I');
    expect($personaM->sexo_nombre)->toBe('Masculino');
    expect($personaM->sexo_rips)->toBe('H');
    expect($personaF->sexo_nombre)->toBe('Femenino');
    expect($personaF->sexo_rips)->toBe('M');
    expect($personaSin->sexo_nombre)->toBe('No especificado');
    expect($personaSin->sexo_rips)->toBeNull();
});

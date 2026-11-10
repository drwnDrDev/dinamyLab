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

it('canonicaliza m y M con espacios antes de guardar', function () {
    $persona = Persona::factory()->create(['sexo' => 'F']);

    $datos = [
        'primer_nombre' => 'Ana',
        'segundo_nombre' => null,
        'primer_apellido' => 'Pérez',
        'segundo_apellido' => null,
        'fecha_nacimiento' => '1990-01-01',
    ];

    foreach (['m', ' M '] as $sexo) {
        $this->actingAs($this->user)
            ->put(route('personas.update', $persona), $datos + ['sexo' => $sexo])
            ->assertRedirect(route('personas.show', $persona));

        expect($persona->fresh()->sexo)->toBe('M');
    }
});

it('rechaza h y H normalizados en mayuscula', function () {
    $persona = Persona::factory()->create(['sexo' => 'M']);

    $datos = [
        'primer_nombre' => 'Ana',
        'segundo_nombre' => null,
        'primer_apellido' => 'Pérez',
        'segundo_apellido' => null,
        'fecha_nacimiento' => '1990-01-01',
    ];

    foreach (['h', 'H'] as $sexo) {
        $this->actingAs($this->user)
            ->put(route('personas.update', $persona), $datos + ['sexo' => $sexo])
            ->assertSessionHasErrors('sexo');

        expect($persona->fresh()->sexo)->toBe('M');
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

it('canonicaliza sexo en la actualizacion por API', function () {
    $this->withoutMiddleware();
    $persona = Persona::factory()->create(['sexo' => 'F']);

    $this->actingAs($this->user)
        ->putJson("/api/personas/{$persona->id}", ['sexo' => ' m '])
        ->assertOk();

    expect($persona->fresh()->sexo)->toBe('M');

    $this->actingAs($this->user)
        ->putJson("/api/personas/{$persona->id}", ['sexo' => 'h'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('sexo');

    expect($persona->fresh()->sexo)->toBe('M');
});

it('canonicaliza el filtro de sexo en el listado de personas', function () {
    $masculino = Persona::factory()->create([
        'sexo' => 'M',
        'numero_documento' => '80000001',
    ]);
    $femenino = Persona::factory()->create([
        'sexo' => 'F',
        'numero_documento' => '80000002',
    ]);

    $this->actingAs($this->user)
        ->get(route('personas', ['sexo' => 'm']))
        ->assertOk()
        ->assertSee($masculino->numero_documento)
        ->assertDontSee($femenino->numero_documento);
});

it('canonicaliza sexo al crear una persona por API', function () {
    $this->withoutMiddleware();

    $this->actingAs($this->user)
        ->postJson('/api/personas', [
            'nombres' => 'Ana Maria',
            'apellidos' => 'Perez',
            'tipo_documento' => 'CC',
            'numero_documento' => '1234567890',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => ' m ',
            'perfil' => 'Paciente',
            'telefono' => null,
            'municipio' => null,
            'direccion' => null,
            'correo' => null,
        ])
        ->assertCreated();

    expect(Persona::where('numero_documento', '1234567890')->first()->sexo)->toBe('M');
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

it('muestra masculino para valores legados H o m y usa indeterminado para invalidos', function () {
    $personaH = Persona::factory()->create(['sexo' => 'H']);
    $personaMinuscula = Persona::factory()->create(['sexo' => 'm']);
    $personaEspacios = Persona::factory()->create(['sexo' => ' m ']);
    $personaInvalida = Persona::factory()->create(['sexo' => 'X']);

    expect($personaH->sexo_nombre)->toBe('Masculino');
    expect($personaH->sexo_rips)->toBe('H');
    expect($personaMinuscula->sexo_nombre)->toBe('Masculino');
    expect($personaMinuscula->sexo_rips)->toBe('H');
    expect($personaEspacios->sexo_nombre)->toBe('Masculino');
    expect($personaEspacios->sexo_rips)->toBe('H');
    expect($personaInvalida->sexo_nombre)->toBe('No especificado');
    expect($personaInvalida->sexo_rips)->toBe('I');
});

<?php

use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->prestador = User::factory()->create();
    $this->prestador->assignRole('prestador');
});

it('busca CUPS por nombre y no cae en la ruta show', function () {
    DB::table('codigo_cups')->insert([
        ['codigo' => '90.01.01', 'nombre' => 'HEMOGRAMA COMPLETO', 'activo' => true, 'nivel' => 1],
        ['codigo' => '90.01.02', 'nombre' => 'HEMOGLOBINA', 'activo' => true, 'nivel' => 1],
    ]);

    $this->actingAs($this->prestador)
        ->getJson('/api/cups/buscar?nombre=HEMO')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['codigo' => '90.01.01'])
        ->assertJsonMissingPath('data.0.descripcion');
});

it('busca CUPS tanto por nombre como por código', function () {
    DB::table('codigo_cups')->insert([
        ['codigo' => '90.03.01', 'nombre' => 'ECOGRAFÍA ABDOMINAL', 'activo' => true, 'nivel' => 1],
        ['codigo' => '91.04.02', 'nombre' => 'HEMOGRAMA', 'activo' => true, 'nivel' => 1],
    ]);

    $this->actingAs($this->prestador)
        ->getJson('/api/cups/buscar?query=ECOGRAFÍA')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['codigo' => '90.03.01']);

    $this->actingAs($this->prestador)
        ->getJson('/api/cups/buscar?query=91.04')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['codigo' => '91.04.02']);
});

it('acepta codigo_cup como parámetro de búsqueda', function () {
    DB::table('codigo_cups')->insert([
        'codigo' => '92.05.01',
        'nombre' => 'RADIOGRAFÍA DE TÓRAX',
        'activo' => true,
        'nivel' => 1,
    ]);

    $this->actingAs($this->prestador)
        ->getJson('/api/cups/buscar?codigo_cup=92.05')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['codigo' => '92.05.01']);
});

it('limita la búsqueda de CUPS a 20 resultados', function () {
    $rows = [];
    for ($i = 1; $i <= 25; $i++) {
        $rows[] = [
            'codigo' => sprintf('90.02.%02d', $i),
            'nombre' => "HEMO {$i}",
            'activo' => true,
            'nivel' => 1,
        ];
    }
    DB::table('codigo_cups')->insert($rows);

    $this->actingAs($this->prestador)
        ->getJson('/api/cups/buscar?nombre=HEMO')
        ->assertOk()
        ->assertJsonCount(20, 'data');
});

it('valida el parámetro nombre de CUPS', function () {
    $this->actingAs($this->prestador)
        ->getJson('/api/cups/buscar')
        ->assertStatus(422);
});

it('busca CIE y limita a 20 resultados', function () {
    $rows = [];
    for ($i = 1; $i <= 25; $i++) {
        $rows[] = [
            'codigo' => sprintf('ZZ%02d', $i),
            'nombre' => "PRUEBA {$i}",
            'descripcion' => "Descripcion {$i}",
            'activo' => true,
            'nivel' => 1,
        ];
    }
    DB::table('codigo_diagnosticos')->insert($rows);

    $this->actingAs($this->prestador)
        ->postJson('/api/cie10/buscar', ['query' => 'PRUEBA'])
        ->assertOk()
        ->assertJsonCount(20, 'data');
});

it('valida el parámetro query de CIE', function () {
    $this->actingAs($this->prestador)
        ->postJson('/api/cie10/buscar', ['query' => ''])
        ->assertStatus(422);
});

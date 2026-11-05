<?php

use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(TestDataSeeder::class);
    $this->prestador = User::factory()->create();
    $this->prestador->assignRole('prestador');
});

it('exige autenticación', function () {
    $this->getJson('/api/static-data-for-frontend')->assertUnauthorized();
});

it('entrega los catálogos livianos sin CUPS ni CIE', function () {
    $this->actingAs($this->prestador)
        ->getJson('/api/static-data-for-frontend')
        ->assertOk()
        ->assertJsonStructure([
            'documentos_paciente',
            'documentos_pagador',
            'paises',
            'municipios',
            'eps',
            'servicios_habilitados',
            'via_ingreso',
            'modalidad_atencion',
            'tipo_afiliacion',
            'causa_externa',
            'finalidad_consulta',
        ])
        ->assertJsonMissingPath('cups')
        ->assertJsonMissingPath('defaultCies');
});

it('está protegido por sanctum y throttle', function () {
    $route = collect(Route::getRoutes())->first(
        fn ($route) => $route->uri() === 'api/static-data-for-frontend'
    );

    expect($route)->not->toBeNull();
    expect($route->gatherMiddleware())
        ->toContain('auth:sanctum')
        ->toContain('throttle:60,1');
});

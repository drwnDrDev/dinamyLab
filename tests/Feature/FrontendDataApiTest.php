<?php

use App\Models\User;
use App\Support\CatalogsCache;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Illuminate\Support\Facades\Cache;
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

it('cachea el payload en el servidor', function () {
    Cache::flush();
    $this->actingAs($this->prestador)
        ->getJson('/api/static-data-for-frontend')
        ->assertOk();

    expect(Cache::has(CatalogsCache::KEY))->toBeTrue();
});

it('devuelve ETag y 304 cuando coincide', function () {
    $first = $this->actingAs($this->prestador)
        ->getJson('/api/static-data-for-frontend')
        ->assertOk();

    $etag = $first->headers->get('ETag');
    expect($etag)->not->toBeNull();

    $this->withHeaders(['If-None-Match' => $etag])
        ->actingAs($this->prestador)
        ->getJson('/api/static-data-for-frontend')
        ->assertStatus(304)
        ->assertHeader('ETag', $etag);
});

it('marca la respuesta como privada y revalidable', function () {
    $response = $this->actingAs($this->prestador)
        ->getJson('/api/static-data-for-frontend')
        ->assertOk();

    // El middleware HandleCors agrega "Origin" al Vary; validamos que "Cookie" esté presente.
    expect($response->headers->get('Vary'))->toContain('Cookie');

    $cacheControl = $response->headers->get('Cache-Control');
    expect($cacheControl)
        ->toContain('private')
        ->toContain('max-age=0')
        ->toContain('must-revalidate');
});

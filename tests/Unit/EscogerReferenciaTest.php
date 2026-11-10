<?php

use App\Models\Parametro;
use App\Models\Persona;
use App\Models\ValorReferencia;
use App\Services\EscogerReferencia;
use Carbon\Carbon;
use Tests\TestCase;

uses(TestCase::class);

function parametroConReferencias(array $referencias): Parametro
{
    $parametro = new Parametro();
    $parametro->setRelation(
        'valoresReferencia',
        collect($referencias)->map(fn (array $referencia) => new ValorReferencia($referencia))
    );

    return $parametro;
}

it('mapea F a mujeres y M a hombres', function () {
    $fecha = Carbon::parse('2026-01-01');

    $hombre = new Persona(['sexo' => 'M', 'fecha_nacimiento' => '1990-01-01']);
    $mujer = new Persona(['sexo' => 'F', 'fecha_nacimiento' => '1990-01-01']);
    $intersexual = new Persona(['sexo' => 'I', 'fecha_nacimiento' => '1990-01-01']);
    $sinSexo = new Persona(['sexo' => null, 'fecha_nacimiento' => '1990-01-01']);
    $menor = new Persona(['sexo' => 'M', 'fecha_nacimiento' => '2024-01-01']);

    expect(EscogerReferencia::datosDemograficos($hombre, $fecha))
        ->toMatchArray(['sexo' => 'hombres', 'etario' => 'adultos']);
    expect(EscogerReferencia::datosDemograficos($mujer, $fecha))
        ->toMatchArray(['sexo' => 'mujeres', 'etario' => 'adultos']);
    expect(EscogerReferencia::datosDemograficos($intersexual, $fecha))
        ->toMatchArray(['sexo' => null, 'etario' => 'adultos']);
    expect(EscogerReferencia::datosDemograficos($sinSexo, $fecha))
        ->toMatchArray(['sexo' => null, 'etario' => 'adultos']);
    expect(EscogerReferencia::datosDemograficos($menor, $fecha))
        ->toMatchArray(['sexo' => 'hombres', 'etario' => 'menores']);
});

it('selecciona una referencia solo por sexo', function () {
    $parametro = parametroConReferencias([
        ['demografia' => 'hombres', 'salida' => 'Hombres'],
        ['demografia' => 'mujeres', 'salida' => 'Mujeres'],
    ]);

    $referencia = EscogerReferencia::estableceReferencia(
        ['sexo' => 'hombres', 'etario' => 'adultos'],
        $parametro
    );

    expect($referencia?->salida)->toBe('Hombres');
});

it('selecciona la referencia de mujeres para una persona F', function () {
    $parametro = parametroConReferencias([
        ['demografia' => 'hombres', 'salida' => 'Hombres'],
        ['demografia' => 'mujeres', 'salida' => 'Mujeres'],
    ]);

    $referencia = EscogerReferencia::estableceReferencia(
        ['sexo' => 'mujeres', 'etario' => 'menores'],
        $parametro
    );

    expect($referencia?->salida)->toBe('Mujeres');
});

it('no usa referencias de sexo para una persona I', function () {
    $parametro = parametroConReferencias([
        ['demografia' => 'hombres', 'salida' => 'Hombres'],
        ['demografia' => 'mujeres', 'salida' => 'Mujeres'],
    ]);

    $referencia = EscogerReferencia::estableceReferencia(
        ['sexo' => null, 'etario' => 'adultos'],
        $parametro
    );

    expect($referencia)->toBeNull();
});

it('conserva la seleccion etaria cuando existe', function () {
    $parametro = parametroConReferencias([
        ['demografia' => 'menores', 'salida' => 'Menores'],
        ['demografia' => 'adultos', 'salida' => 'Adultos'],
    ]);

    $referencia = EscogerReferencia::estableceReferencia(
        ['sexo' => 'hombres', 'etario' => 'adultos'],
        $parametro
    );

    expect($referencia?->salida)->toBe('Adultos');
});

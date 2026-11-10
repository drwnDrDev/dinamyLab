<?php

use App\Models\Sexo;

it('expone los tres códigos canonicos del catalogo', function () {
    expect(Sexo::normalizar('H'))->toBe('M');
    expect(Sexo::normalizar('M'))->toBe('M');
    expect(Sexo::normalizar('F'))->toBe('F');
    expect(Sexo::normalizar('I'))->toBe('I');
    expect(Sexo::normalizar(null))->toBeNull();
});

it('traduce a y desde RIPS sin guardar codigos RIPS', function () {
    expect(Sexo::aRips('F'))->toBe('M');
    expect(Sexo::aRips('M'))->toBe('H');
    expect(Sexo::aRips('I'))->toBe('I');
    expect(Sexo::aRips(null))->toBeNull();
    expect(Sexo::desdeRips('H'))->toBe('M');
    expect(Sexo::desdeRips('M'))->toBe('F');
    expect(Sexo::desdeRips('I'))->toBe('I');
    expect(Sexo::desdeRips(null))->toBeNull();
});

it('rechaza codigos de sexo desconocidos', function () {
    expect(fn () => Sexo::normalizar('X'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Sexo::desdeRips('X'))->toThrow(InvalidArgumentException::class);
});

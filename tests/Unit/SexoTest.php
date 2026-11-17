<?php

use App\Models\Sexo;

it('expone los tres códigos canonicos del catalogo', function () {
    expect(Sexo::normalizar('H'))->toBe('M');
    expect(Sexo::normalizar('M'))->toBe('M');
    expect(Sexo::normalizar('F'))->toBe('F');
    expect(Sexo::normalizar('I'))->toBe('I');
    expect(Sexo::normalizar(null))->toBeNull();
});



it('rechaza codigos de sexo desconocidos', function () {
    expect(fn () => Sexo::normalizar('X'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Sexo::desdeRips('X'))->toThrow(InvalidArgumentException::class);
});

<?php

use App\Services\FirmaReporte;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

it('compone la firma sobre fondo blanco y devuelve un PNG', function () {
    Storage::fake('local');
    Storage::disk('local')->put('firmas/profesional.png', pngTransparente());

    $dataUri = app(FirmaReporte::class)->componer('profesional.png', 'ORDEN 872 · CC 123 · A1B2-C3D4-E5F6');

    expect($dataUri)->toStartWith('data:image/png;base64,');
    $png = base64_decode(substr($dataUri, strlen('data:image/png;base64,')));
    $info = getimagesizefromstring($png);
    expect($info['mime'])->toBe('image/png');
    expect($info[0])->toBeGreaterThan(80);

    $img = imagecreatefromstring($png);
    $rgb = imagecolorsforindex($img, imagecolorat($img, 0, 0));
    expect($rgb['alpha'])->toBe(0);
    expect($rgb['red'])->toBe(255);
    imagedestroy($img);
});

it('compone una firma de 1x1 sin errores', function () {
    Storage::fake('local');
    Storage::disk('local')->put('firmas/minima.png', pngTransparente(1, 1));

    expect(app(FirmaReporte::class)->componer('minima.png', 'X'))
        ->toStartWith('data:image/png;base64,');
});

it('limita el tamaño de una firma muy grande', function () {
    Storage::fake('local');
    Storage::disk('local')->put('firmas/grande.png', pngTransparente(1200, 500));

    $dataUri = app(FirmaReporte::class)->componer('grande.png', 'X');
    $png = base64_decode(substr($dataUri, strlen('data:image/png;base64,')));
    $info = getimagesizefromstring($png);

    expect($info[0])->toBeLessThanOrEqual(612);
    expect($info[1])->toBeLessThanOrEqual(252);
});

it('devuelve null si la firma no existe o no es una imagen válida', function () {
    Storage::fake('local');
    expect(app(FirmaReporte::class)->componer('no-existe.png', 'X'))->toBeNull();

    Storage::disk('local')->put('firmas/texto.png', 'no es una imagen');
    expect(app(FirmaReporte::class)->componer('texto.png', 'X'))->toBeNull();
});

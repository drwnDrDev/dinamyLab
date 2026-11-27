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

it('aplana el fondo transparente y dibuja la marca de agua', function () {
    Storage::fake('local');
    Storage::disk('local')->put('firmas/vacia.png', pngTransparente(200, 80, false));

    $sinMarca = app(FirmaReporte::class)->componer('vacia.png', '');
    $pngSinMarca = base64_decode(substr($sinMarca, strlen('data:image/png;base64,')));
    $img = imagecreatefromstring($pngSinMarca);
    $centro = imagecolorsforindex($img, imagecolorat($img, (int) (imagesx($img) / 2), (int) (imagesy($img) / 2)));
    expect($centro['red'])->toBe(255);
    expect($centro['green'])->toBe(255);
    expect($centro['blue'])->toBe(255);
    expect($centro['alpha'])->toBe(0);
    imagedestroy($img);

    $conMarca = app(FirmaReporte::class)->componer('vacia.png', 'ORDEN 1 · CC 1 · ABCD-EFGH-IJKL');
    $pngConMarca = base64_decode(substr($conMarca, strlen('data:image/png;base64,')));
    $img = imagecreatefromstring($pngConMarca);
    $oscuros = 0;

    for ($y = 0; $y < imagesy($img); $y += 2) {
        for ($x = 0; $x < imagesx($img); $x += 2) {
            $rgb = imagecolorsforindex($img, imagecolorat($img, $x, $y));

            if ($rgb['red'] < 250) {
                $oscuros++;
            }
        }
    }

    imagedestroy($img);

    expect($oscuros)->toBeGreaterThan(0);
});

it('devuelve null si la firma no existe o no es una imagen válida', function () {
    Storage::fake('local');
    expect(app(FirmaReporte::class)->componer('no-existe.png', 'X'))->toBeNull();

    Storage::disk('local')->put('firmas/texto.png', 'no es una imagen');
    expect(app(FirmaReporte::class)->componer('texto.png', 'X'))->toBeNull();
});

it('devuelve null ante rutas no permitidas o corruptas', function () {
    Storage::fake('local');
    Storage::disk('local')->put('firmas/ok.png', pngTransparente());

    expect(app(FirmaReporte::class)->componer('../ok.png', 'X'))->toBeNull();
    expect(app(FirmaReporte::class)->componer('/etc/passwd', 'X'))->toBeNull();
    expect(app(FirmaReporte::class)->componer("firma\0.png", 'X'))->toBeNull();
});

it('devuelve null si la firma declara dimensiones excesivas', function () {
    Storage::fake('local');
    Storage::disk('local')->put('firmas/bomba.png', pngTransparente(2500, 2000));

    expect(app(FirmaReporte::class)->componer('bomba.png', 'X'))->toBeNull();
});

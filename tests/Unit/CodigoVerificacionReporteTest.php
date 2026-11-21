<?php

use App\Services\CodigoVerificacionReporte;
use Tests\TestCase;

uses(TestCase::class);

function reporteParaCodigo(?string $resultado = '108'): array
{
    return [
        'paciente' => ['documento' => 'CC 1000000000'],
        'examenes' => [[
            'procedimiento_id' => 10,
            'nombre' => 'Glicemia basal',
            'orden_numero' => '2026-000154',
            'fecha_validacion' => '10-11-2026 10:42',
            'grupos' => [[
                'nombre' => null,
                'parametros' => [[
                    'nombre' => 'Glucosa en ayunas',
                    'resultado' => $resultado,
                    'unidades' => 'mg/dL',
                    'referencia' => '70 - 100',
                ]],
            ]],
        ]],
    ];
}

it('genera un código reproducible con formato XXXX-XXXX-XXXX', function () {
    $servicio = app(CodigoVerificacionReporte::class);
    $codigo = $servicio->generar(reporteParaCodigo());

    expect($codigo)->toMatch('/^[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}$/');
    expect($servicio->generar(reporteParaCodigo()))->toBe($codigo);
    expect($servicio->generar(reporteParaCodigo('109')))->not->toBe($codigo);
});

it('genera un código aunque no haya exámenes', function () {
    $codigo = app(CodigoVerificacionReporte::class)->generar([
        'paciente' => ['documento' => 'CC 1'],
        'examenes' => [],
    ]);

    expect($codigo)->toMatch('/^[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}$/');
});

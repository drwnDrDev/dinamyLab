<?php

/**
 * Datos ficticios para maquetar resultados/reporte.blade.php mientras el BE
 * entrega el service de IM-01. Siguen el contrato $reporte propuesto en
 * docs/CHECKLIST_DEUDA_TECNICA.md (IM-01 ⇄ IM-02).
 *
 * Ningún dato corresponde a una persona real.
 *
 * Uso: require este archivo con $modoEjemplo = 'orden' (por defecto) o
 * 'historia' (exámenes de varias órdenes, sin `orden`).
 */

$p = fn (string $nombre, ?string $resultado, ?string $unidades = null, ?string $referencia = null, bool $fuera = false, ?string $metodo = null) => [
    'nombre' => $nombre,
    'metodo' => $metodo,
    'resultado' => $resultado,
    'unidades' => $unidades,
    'referencia' => $referencia,
    'fuera_de_rango' => $fuera,
    'tipo' => 'valor',
];

$texto = fn (string $nombre, ?string $resultado) => [
    'nombre' => $nombre,
    'metodo' => null,
    'resultado' => $resultado,
    'unidades' => null,
    'referencia' => null,
    'fuera_de_rango' => false,
    'tipo' => 'texto',
];

$bacteriologa = [
    'nombre' => 'Laura Restrepo Gómez',
    'documento' => 'CC 00000001',
    'firma_src' => 'data:image/svg+xml;base64,'.base64_encode(
        '<svg xmlns="http://www.w3.org/2000/svg" width="240" height="70" viewBox="0 0 240 70">'
        .'<path d="M10 50 C 30 10, 50 10, 60 40 S 90 70, 110 30 S 150 5, 165 45 S 200 60, 230 20" '
        .'fill="none" stroke="#1d3557" stroke-width="3" stroke-linecap="round"/></svg>'
    ),
];

$bacteriologo = [
    'nombre' => 'Andrés Felipe Mora',
    'documento' => 'CC 00000002',
    'firma_src' => null,
];

$quimica = fn (string $nombre, string $parametro, string $resultado, string $unidades, string $referencia, bool $fuera = false, string $orden = '2026-000154') => [
    'nombre' => $nombre,
    'orden_numero' => $orden,
    'fecha_validacion' => '10-11-2026 10:42',
    'bacteriologo' => $bacteriologa,
    'grupos' => [
        ['nombre' => null, 'parametros' => [
            $p($parametro, $resultado, $unidades, $referencia, $fuera, 'Enzimático colorimétrico'),
        ]],
    ],
];

$examenes = [
    [
        'nombre' => 'Cuadro hemático',
        'orden_numero' => '2026-000154',
        'fecha_validacion' => '10-11-2026 09:15',
        'bacteriologo' => $bacteriologa,
        'grupos' => [
            ['nombre' => 'Serie roja', 'parametros' => [
                $p('Hemoglobina', '11.2', 'g/dL', '12.0 - 16.0', true),
                $p('Hematocrito', '34.5', '%', '37.0 - 47.0', true),
                $p('Recuento de eritrocitos', '4.10', 'millones/µL', '4.20 - 5.40', true),
                $p('Volumen corpuscular medio (VCM)', '84.1', 'fL', '80.0 - 100.0'),
                $p('Hemoglobina corpuscular media (HCM)', '27.3', 'pg', '27.0 - 33.0'),
                $p('Concentración de hemoglobina corpuscular media (CHCM)', '32.5', 'g/dL', '32.0 - 36.0'),
                $p('Ancho de distribución eritrocitaria (RDW)', '14.9', '%', '11.5 - 14.5', true),
            ]],
            ['nombre' => 'Serie blanca', 'parametros' => [
                $p('Recuento de leucocitos', '7.800', 'x10³/µL', '4.500 - 11.000'),
                $p('Neutrófilos', '62', '%', '40 - 70'),
                $p('Linfocitos', '28', '%', '20 - 45'),
                $p('Monocitos', '6', '%', '2 - 10'),
                $p('Eosinófilos', '3', '%', '1 - 6'),
                $p('Basófilos', '1', '%', '0 - 2'),
                $p('Neutrófilos absolutos', '4.836', 'x10³/µL', '1.800 - 7.700'),
                $p('Linfocitos absolutos', '2.184', 'x10³/µL', '1.000 - 4.800'),
            ]],
            ['nombre' => 'Serie plaquetaria', 'parametros' => [
                $p('Recuento de plaquetas', '265.000', '/µL', '150.000 - 450.000'),
                $p('Volumen plaquetario medio (VPM)', '9.8', 'fL', '7.5 - 11.5'),
            ]],
            ['nombre' => null, 'parametros' => [
                $texto('Observaciones', 'Anisocitosis leve. Se sugiere correlacionar con ferritina sérica y control en 3 meses.'),
            ]],
        ],
    ],
    $quimica('Glicemia basal', 'Glucosa en ayunas', '108', 'mg/dL', '70 - 100', true),
    $quimica('Colesterol total', 'Colesterol total', '182', 'mg/dL', 'Deseable: menor de 200'),
    $quimica('Colesterol HDL', 'Colesterol HDL', '38', 'mg/dL', 'Mayor de 40', true),
    $quimica('Colesterol LDL', 'Colesterol LDL', '112', 'mg/dL', 'Óptimo: menor de 100', true),
    $quimica('Triglicéridos', 'Triglicéridos', '160', 'mg/dL', 'Normal: menor de 150', true),
    $quimica('Ácido úrico', 'Ácido úrico', '5.1', 'mg/dL', '2.4 - 5.7'),
    [
        'nombre' => 'Uroanálisis',
        'orden_numero' => '2026-000154',
        'fecha_validacion' => '10-11-2026 11:05',
        'bacteriologo' => $bacteriologo,
        'grupos' => [
            ['nombre' => 'Examen físico', 'parametros' => [
                $p('Color', 'Amarillo'),
                $p('Aspecto', 'Ligeramente turbio', null, 'Transparente', true),
                $p('Densidad', '1.025', null, '1.005 - 1.030'),
                $p('Olor', 'Sui géneris'),
            ]],
            ['nombre' => 'Examen químico', 'parametros' => [
                $p('pH', '6.0', null, '5.0 - 8.0'),
                $p('Leucocitos', 'Negativo', 'Leu/µL', 'Negativo'),
                $p('Nitritos', 'Negativo', null, 'Negativo'),
                $p('Proteínas', 'Trazas', 'mg/dL', 'Negativo', true),
                $p('Glucosa', 'Negativo', 'mg/dL', 'Negativo'),
                $p('Cetonas', 'Negativo', 'mg/dL', 'Negativo'),
                $p('Urobilinógeno', 'Normal', 'mg/dL', '0.2 - 1.0'),
                $p('Bilirrubina', 'Negativo', null, 'Negativo'),
                $p('Sangre', 'Negativo', 'Ery/µL', 'Negativo'),
            ]],
            ['nombre' => 'Sedimento urinario', 'parametros' => [
                $p('Células epiteliales', 'Escasas', 'x campo'),
                $p('Leucocitos', '2 - 4', 'x campo', '0 - 5'),
                $p('Hematíes', '0 - 2', 'x campo', '0 - 2'),
                $p('Bacterias', '+', null, 'Escasas'),
                $p('Moco', '++'),
                $p('Cristales', 'Oxalato de calcio +'),
                $p('Cilindros', 'No se observan'),
                $p('Levaduras', 'No se observan'),
            ]],
            ['nombre' => null, 'parametros' => [
                $texto('Observaciones', 'Muestra recolectada en el laboratorio, chorro medio, primera orina de la mañana. Se observa moco abundante y cristales de oxalato de calcio, sin significado clínico aislado. Correlacionar con la clínica del paciente.'),
            ]],
        ],
    ],
    [
        'nombre' => 'Coproanálisis',
        'orden_numero' => '2026-000154',
        'fecha_validacion' => '10-11-2026 11:20',
        'bacteriologo' => $bacteriologo,
        'grupos' => [
            ['nombre' => 'Examen macroscópico', 'parametros' => [
                $p('Consistencia', 'Blanda'),
                $p('Color', 'Café'),
                $p('Moco', 'No se observa'),
                $p('Sangre', 'No se observa'),
            ]],
            ['nombre' => 'Examen microscópico', 'parametros' => [
                $p('pH', '7.0', null, '6.5 - 7.5'),
                $p('Leucocitos', '0 - 1', 'x campo'),
                $p('Hematíes', 'No se observan', 'x campo'),
                $p('Levaduras', 'Escasas'),
                $p('Almidones', '+'),
                $p('Grasas', 'Negativo'),
                $p('Flora bacteriana', 'Normal'),
            ]],
            ['nombre' => 'Parasitológico', 'parametros' => [
                $texto('Protozoarios', 'Quistes de Entamoeba coli: 0 - 2 por campo. Blastocystis hominis: escasos.'),
                $texto('Helmintos', 'No se observan huevos ni larvas.'),
            ]],
        ],
    ],
    $quimica('Proteína C reactiva', 'PCR cuantitativa', '4.2', 'mg/L', 'Menor de 6.0', false),
    [
        'nombre' => 'Hemoclasificación',
        'orden_numero' => '2026-000154',
        'fecha_validacion' => '10-11-2026 09:30',
        'bacteriologo' => $bacteriologa,
        'grupos' => [
            ['nombre' => null, 'parametros' => [
                $p('Grupo sanguíneo', 'O', null, null, false, 'Aglutinación en tubo'),
                $p('Factor Rh', 'Positivo', null, null, false, 'Aglutinación en tubo'),
            ]],
        ],
    ],
    [
        'nombre' => 'Serología (VDRL)',
        'orden_numero' => '2026-000154',
        'fecha_validacion' => '10-11-2026 10:00',
        'bacteriologo' => $bacteriologo,
        'grupos' => [
            ['nombre' => null, 'parametros' => [
                $p('VDRL', 'No reactivo', null, 'No reactivo', false, 'Floculación'),
            ]],
        ],
    ],
];

$base = [
    'laboratorio' => [
        'nombre_comercial' => 'Laboratorio Clínico Demo',
        'sede' => 'Sede Centro',
        'nit' => '900.000.000-0',
        'telefonos' => ['601 000 0000', '300 000 0000'],
        'direccion' => 'Calle 00 # 00-00, Bogotá D.C.',
        'email' => 'resultados@laboratorio-demo.test',
        'logo_src' => 'data:image/svg+xml;base64,'.base64_encode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120">'
            .'<circle cx="60" cy="60" r="56" fill="#0eb4d1"/>'
            .'<path d="M48 28 h24 v6 h-4 v22 l20 34 a6 6 0 0 1 -5 9 h-46 a6 6 0 0 1 -5 -9 l20 -34 v-22 h-4 z" fill="#ffffff"/>'
            .'<path d="M41 78 h38 l7 12 a3 3 0 0 1 -3 4 h-46 a3 3 0 0 1 -3 -4 z" fill="#4b8f9b"/></svg>'
        ),
    ],
    'paciente' => [
        'nombre' => 'María Fernanda Ejemplo Prueba',
        'documento' => 'CC 1000000000',
        'sexo' => 'Femenino',
        'edad' => '34 años',
        'fecha_nacimiento' => '15-03-1992',
    ],
    'fecha_emision' => '10-11-2026 14:30',
];

if (($modoEjemplo ?? 'orden') === 'historia') {
    $historia = array_slice($examenes, 0, 4);
    $historia[] = array_merge($quimica('Glicemia basal', 'Glucosa en ayunas', '96', 'mg/dL', '70 - 100', false, '2026-000087'), [
        'fecha_validacion' => '02-08-2026 08:50',
    ]);

    return $base + [
        'orden' => null,
        'total_examenes' => count($historia),
        'examenes' => $historia,
        'remisiones' => [],
    ];
}

return $base + [
    'orden' => [
        'numero' => '2026-000154',
        'fecha_atencion' => '10-11-2026',
    ],
    'total_examenes' => count($examenes),
    'examenes' => $examenes,
    'remisiones' => [
        ['cup' => '906913', 'nombre' => 'Hormona estimulante del tiroides (TSH)', 'destino' => 'Laboratorio de referencia externo'],
        ['cup' => '903426', 'nombre' => 'Hemoglobina glicosilada (HbA1c)', 'destino' => 'Sede Norte'],
    ],
];

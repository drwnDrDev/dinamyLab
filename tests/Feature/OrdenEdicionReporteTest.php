<?php

use App\Estado;
use App\Models\Empleado;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\Resultado;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\PaisSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;

beforeEach(function () {
    $this->seed(PaisSeeder::class);
    $this->seed(TipoDocumentoSeeder::class);
    $this->seed(TestDataSeeder::class);
    $this->seed(RolesYPermisosSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('prestador');
    $this->sede = Sede::factory()->create();
    $empleado = Empleado::factory()->create([
        'user_id' => $this->user->id,
        'empresa_id' => $this->sede->empresa_id,
        'tipo_documento_id' => 1,
    ]);
    $empleado->sedes()->attach($this->sede->id);
    $this->withSession(['sede' => $this->sede]);
});

function crearOrdenParaEdicion($test, Examen $examen, string $estado = 'en proceso'): Orden
{
    $orden = Orden::create([
        'sede_id' => $test->sede->id,
        'numero' => 872,
        'paciente_id' => Persona::factory()->create()->id,
        'user_id' => $test->user->id,
        'total' => $examen->valor,
    ]);
    $orden->examenes()->attach($examen->id, ['cantidad' => 1]);
    $orden->procedimientos()->create([
        'examen_id' => $examen->id,
        'fecha' => now(),
        'sede_id' => $test->sede->id,
        'diagnostico_principal' => 'Z017',
        'codigo_finalidad' => '15',
        'codigo_modalidad' => '01',
        'codigo_via_ingreso' => '01',
        'estado' => $estado,
    ]);

    return $orden;
}

it('permite editar examenes y destinos, y agregar examenes y remisiones', function () {
    $examenExistente = Examen::factory()->create(['valor' => 100]);
    $examenNuevo = Examen::factory()->create(['valor' => 50]);
    $orden = crearOrdenParaEdicion($this, $examenExistente);
    $remision = $orden->remisiones()->create([
        'cup' => '890201',
        'nombre' => 'Consulta externa',
        'valor' => 25,
        'destino' => 'IPS anterior',
    ]);

    $this->actingAs($this->user)
        ->put(route('ordenes.update', $orden), [
            'examenes' => [$examenExistente->id => 1],
            'examenes_nuevos' => [['id' => $examenNuevo->id, 'cantidad' => 2]],
            'remisiones' => [$remision->id => [
                'id' => $remision->id,
                'destino' => 'IPS actualizada',
            ]],
            'nuevas_remisiones' => [[
                'cup' => '902210',
                'nombre' => 'Estudio remitido',
                'valor' => 30,
                'destino' => 'Nueva IPS',
            ]],
            'abono' => 0,
            'observaciones' => 'Orden actualizada',
        ])
        ->assertRedirect(route('ordenes.show', $orden));

    expect($orden->fresh()->total)->toBe('255.00');
    expect($orden->procedimientos()->count())->toBe(3);
    expect($remision->fresh()->destino)->toBe('IPS actualizada');
    expect($orden->remisiones()->count())->toBe(2);
});

it('conserva la tarifa histórica al guardar y toma el costo correspondiente al cambiar de IPS', function () {
    $this->seed(\Database\Seeders\ValorRemisionSeeder::class);
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create(['valor' => 100]));
    $tarifas = \App\Models\Remision::where('cup', '901101')->firstOrFail()->valores()->orderBy('id')->get();
    $tarifa = $tarifas[0];
    $remision = $orden->remisiones()->create(\App\Services\PrepararRemision::paraOrden(['valor_remision_id' => $tarifa->id, 'valor' => 21000]));
    $tarifa->update(['valor_remitido' => 12345]);

    $this->actingAs($this->user)->get(route('ordenes.edit', $orden))->assertOk()
        ->assertSee('IPS externa registrada')->assertDontSee('destino_sede_id');

    foreach ([$tarifa->ips_externa_id, $tarifas[1]->ips_externa_id] as $indice => $ipsId) {
        $this->put(route('ordenes.update', $orden), [
            'examenes' => $orden->examenes->mapWithKeys(fn ($examen) => [$examen->id => 1])->all(),
            'remisiones' => [['id' => $remision->id, 'ips_externa_id' => $ipsId]],
        ])->assertRedirect(route('ordenes.show', $orden));
        expect($remision->fresh()->valor)->toBe('21000.00');
        expect($remision->fresh()->valor_remitido)->toBe('10000.00');
        expect($remision->fresh()->valor_remision_id)->toBe($tarifas[$indice]->id);
    }
});

it('permite al administrador cambiar el número y el estado de una orden', function () {
    $examen = Examen::factory()->create();
    $orden = crearOrdenParaEdicion($this, $examen);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->put(route('ordenes.update', $orden), [
            'numero' => 'ADM-872',
            'estado_orden' => 'completada',
            'examenes' => [$examen->id => 1],
            'abono' => 0,
            'observaciones' => null,
        ])
        ->assertRedirect(route('ordenes.show', $orden));

    expect($orden->fresh()->numero)->toBe('ADM-872')
        ->and($orden->fresh()->terminada)->not->toBeNull();

    $this->put(route('ordenes.update', $orden), [
        'numero' => 'ADM-873',
        'estado_orden' => 'pendiente',
        'examenes' => [$examen->id => 1],
        'abono' => 0,
        'observaciones' => null,
    ])->assertRedirect(route('ordenes.show', $orden));

    expect($orden->fresh()->numero)->toBe('ADM-873')
        ->and($orden->fresh()->terminada)->toBeNull();
});

it('permite al administrador eliminar una orden desde el detalle', function () {
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create());
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get(route('ordenes.show', $orden))
        ->assertOk()
        ->assertSee('Eliminar orden');

    $this->delete(route('ordenes.destroy', $orden))
        ->assertRedirect(route('ordenes'));
    expect($orden->fresh())->toBeNull();
});

it('cuenta los procedimientos anulados como finalizados en el progreso de la orden', function () {
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create(), Estado::ANULADO->value);

    expect($orden->fresh()->terminada)->not->toBeNull();

    $this->actingAs($this->user)->get(route('ordenes.show', $orden))
        ->assertOk()
        ->assertSee('1 de 1 listos');
});

it('permite al prestador cambiar manualmente el estado de un procedimiento', function () {
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create(), Estado::PROCESO->value);
    $procedimiento = $orden->procedimientos()->firstOrFail();

    $this->actingAs($this->user)
        ->patch(route('procedimientos.observaciones', $procedimiento), [
            'estado' => Estado::ANULADO->value,
            'observacion' => 'Procedimiento cancelado por el profesional',
        ])
        ->assertRedirect(route('procedimientos.show', $procedimiento));

    expect($procedimiento->fresh()->estado)->toBe(Estado::ANULADO->value)
        ->and($orden->fresh()->terminada)->not->toBeNull();
});

it('crea una copia CO con los procedimientos pendientes y bloquea los exportados a RIPS', function () {
    $examen = Examen::factory()->create();
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $terminado = $orden->procedimientos()->firstOrFail();
    $entregado = $orden->procedimientos()->create([
        'examen_id' => Examen::factory()->create()->id,
        'fecha' => now(),
        'sede_id' => $this->sede->id,
        'diagnostico_principal' => 'Z017',
        'codigo_finalidad' => '15',
        'codigo_modalidad' => '01',
        'codigo_via_ingreso' => '01',
        'estado' => Estado::ENTREGADO->value,
    ]);
    $anulado = $orden->procedimientos()->create([
        'examen_id' => Examen::factory()->create()->id,
        'fecha' => now(),
        'sede_id' => $this->sede->id,
        'diagnostico_principal' => 'Z017',
        'codigo_finalidad' => '15',
        'codigo_modalidad' => '01',
        'codigo_via_ingreso' => '01',
        'estado' => Estado::ANULADO->value,
    ]);
    $pendiente = $orden->procedimientos()->create([
        'examen_id' => Examen::factory()->create()->id,
        'fecha' => now(),
        'sede_id' => $this->sede->id,
        'diagnostico_principal' => 'Z017',
        'codigo_finalidad' => '15',
        'codigo_modalidad' => '01',
        'codigo_via_ingreso' => '01',
        'estado' => Estado::PROCESO->value,
    ]);

    $response = $this->actingAs($this->user)->withSession(['sede' => $this->sede])
        ->post(route('descargar.rips'), [
            'sede_id' => $this->sede->id,
            'fecha_inicio' => now()->startOfMonth()->toDateString(),
            'fecha_fin' => now()->endOfMonth()->toDateString(),
        ])->assertOk();
    $servicios = json_decode($response->getContent(), true, flags: JSON_THROW_ON_ERROR)['usuarios'][0]['servicios']['procedimientos'];

    expect($servicios)->toHaveCount(2)
        ->and(collect($servicios)->pluck('codProcedimiento')->all())->toContain($entregado->examen->cup)
        ->and(collect($servicios)->pluck('codProcedimiento')->all())->not->toContain($anulado->examen->cup)
        ->and($terminado->fresh()->rips_procesado_at)->not->toBeNull()
        ->and($entregado->fresh()->rips_procesado_at)->not->toBeNull()
        ->and($anulado->fresh()->rips_procesado_at)->not->toBeNull();

    $copia = Orden::where('orden_origen_id', $orden->id)->firstOrFail();
    expect($copia->numero)->toBe('CO-'.$orden->numero)
        ->and($copia->procedimientos)->toHaveCount(1)
        ->and($copia->procedimientos->first()->estado)->toBe(Estado::PROCESO->value)
        ->and($copia->procedimientos->first()->examen_id)->toBe($pendiente->examen_id);

    $this->patch(route('procedimientos.observaciones', $terminado), [
        'estado' => Estado::ANULADO->value,
        'observacion' => 'Cambio posterior a exportación',
    ])->assertStatus(409);
    $this->delete(route('procedimientos.destroy', $entregado))->assertStatus(409);
    $this->patch(route('procedimientos.observaciones', $anulado), [
        'estado' => Estado::PROCESO->value,
        'observacion' => 'Reactivar después de exportación',
    ])->assertStatus(409);
    $this->delete(route('ordenes.destroy', $orden))->assertStatus(409);
    expect($orden->fresh())->not->toBeNull();
});

it('impide quitar un procedimiento terminado y conserva sus resultados', function () {
    $examen = Examen::factory()->create();
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $procedimiento = $orden->procedimientos()->firstOrFail();
    $parametro = Parametro::create([
        'nombre' => 'Hemoglobina',
        'slug' => 'hemoglobina',
        'tipo_dato' => 'number',
        'examen_id' => $examen->id,
    ]);
    $resultado = Resultado::create([
        'parametro_id' => $parametro->id,
        'procedimiento_id' => $procedimiento->id,
        'resultado' => '13.5',
    ]);

    $this->actingAs($this->user)
        ->from(route('ordenes.edit', $orden))
        ->put(route('ordenes.update', $orden), [
            'examenes' => [$examen->id => 0],
            'abono' => 0,
            'observaciones' => null,
        ])
        ->assertRedirect(route('ordenes.edit', $orden))
        ->assertSessionHasErrors(["examenes.{$examen->id}"]);

    expect($orden->procedimientos()->count())->toBe(1);
    expect($orden->examenes()->count())->toBe(1);
    expect($resultado->fresh())->not->toBeNull();
});

it('muestra el reporte agrupado de una orden', function () {
    $examen = Examen::factory()->create();
    $this->sede->empresa->update([
        'razon_social' => 'Laboratorio Legal S.A.S.',
        'nombre_comercial' => 'Marca del Laboratorio',
    ]);
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $orden->remisiones()->create([
        'cup' => '902299',
        'nombre' => 'Examen externo reservado',
        'valor' => 25,
        'destino' => 'Laboratorio externo reservado',
    ]);
    expect(app(\App\Services\ConstruirReporteResultados::class)->paraOrden($orden))
        ->not->toHaveKey('remisiones');

    $procedimiento = $orden->procedimientos()->firstOrFail();
    $parametro = Parametro::create([
        'nombre' => 'Hemoglobina',
        'slug' => 'hemoglobina',
        'tipo_dato' => 'number',
        'examen_id' => $examen->id,
    ]);
    Resultado::create([
        'parametro_id' => $parametro->id,
        'procedimiento_id' => $procedimiento->id,
        'resultado' => '13.5',
    ]);

    $this->actingAs($this->user)
        ->get(route('resultados.reporte_orden', $orden))
        ->assertOk()
        ->assertSee("Resultados de orden {$orden->numero}")
        ->assertSee('Laboratorio Legal S.A.S.')
        ->assertDontSee('Marca del Laboratorio')
        ->assertSee($examen->nombre)
        ->assertSee('Hemoglobina')
        ->assertDontSee('Examen externo reservado')
        ->assertDontSee('Laboratorio externo reservado')
        ->assertDontSee('Exámenes remitidos')
        ->assertSee('13.5');
});

it('reutiliza el contenido del reporte para resultados individuales e historia', function () {
    $this->sede->empresa->update([
        'razon_social' => 'Razón Social Legal S.A.S.',
        'nombre_comercial' => 'Marca Comercial',
    ]);
    $examen = Examen::factory()->create(['nombre' => 'Examen individual']);
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $procedimiento = $orden->procedimientos()->firstOrFail();
    $parametro = Parametro::create([
        'nombre' => 'Resultado individual',
        'slug' => 'resultado-individual',
        'tipo_dato' => 'number',
        'examen_id' => $examen->id,
    ]);
    Resultado::create([
        'parametro_id' => $parametro->id,
        'procedimiento_id' => $procedimiento->id,
        'resultado' => '13.5',
    ]);

    $examenAdicional = Examen::factory()->create(['nombre' => 'Examen histórico']);
    $ordenAdicional = Orden::create([
        'sede_id' => $this->sede->id,
        'numero' => 873,
        'paciente_id' => $orden->paciente_id,
        'user_id' => $this->user->id,
        'total' => $examenAdicional->valor,
    ]);
    $procedimientoAdicional = $ordenAdicional->procedimientos()->create([
        'examen_id' => $examenAdicional->id,
        'fecha' => now(),
        'sede_id' => $this->sede->id,
        'diagnostico_principal' => 'Z017',
        'codigo_finalidad' => '15',
        'codigo_modalidad' => '01',
        'codigo_via_ingreso' => '01',
        'estado' => Estado::ENTREGADO->value,
    ]);
    $parametroAdicional = Parametro::create([
        'nombre' => 'Resultado histórico',
        'slug' => 'resultado-historico',
        'tipo_dato' => 'number',
        'examen_id' => $examenAdicional->id,
    ]);
    Resultado::create([
        'parametro_id' => $parametroAdicional->id,
        'procedimiento_id' => $procedimientoAdicional->id,
        'resultado' => '21',
    ]);

    $this->actingAs($this->user)
        ->get(route('resultados.show', $procedimiento))
        ->assertOk()
        ->assertSee('Resultados de orden 872')
        ->assertSee('Razón Social Legal S.A.S.')
        ->assertDontSee('Marca Comercial')
        ->assertSee('Resultado individual')
        ->assertSee('13.5');

    $response = $this->actingAs($this->user)
        ->post(route('resultados.historia_show', $orden->paciente), [
            'procedimientos' => [$procedimientoAdicional->id, $procedimiento->id],
        ])
        ->assertOk()
        ->assertSee('Historia clínica')
        ->assertSee('Resultado individual')
        ->assertSee('13.5')
        ->assertSee('Resultado histórico')
        ->assertSee('Razón Social Legal S.A.S.')
        ->assertDontSee('Marca Comercial')
        ->assertSee('21')
        ->assertSee('Orden Nº 872')
        ->assertSee('Orden Nº 873')
        ->assertSee('page-break-before: always');

    expect(substr_count($response->getContent(), 'class="orden-seccion"'))->toBe(2);
    expect(strpos($response->getContent(), 'Examen individual'))
        ->toBeLessThan(strpos($response->getContent(), 'Examen histórico'));
});

it('genera un PDF con los resultados terminados de una orden', function () {
    $examen = Examen::factory()->create();
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $procedimiento = $orden->procedimientos()->firstOrFail();
    $parametro = Parametro::create([
        'nombre' => 'Hemoglobina',
        'slug' => 'hemoglobina',
        'tipo_dato' => 'number',
        'examen_id' => $examen->id,
    ]);
    Resultado::create([
        'parametro_id' => $parametro->id,
        'procedimiento_id' => $procedimiento->id,
        'resultado' => '13.5',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('resultados.reporte_pdf', $orden));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('content-disposition', 'attachment; filename="resultados-orden-872.pdf"');

    expect(substr($response->getContent(), 0, 5))->toBe('%PDF-');
    $this->assertDatabaseHas('eventos_actividad', [
        'evento' => 'resultados.descargados',
        'usuario_id' => $this->user->id,
        'recurso_id' => $orden->id,
        'sede_id' => $this->sede->id,
    ]);
});

it('muestra el registro médico con la cédula del empleado prestador de la empresa', function () {
    $this->user->empleado->update([
        'cargo' => \App\Cargo::PRESTADOR->value,
        'numero_documento' => '12345678',
    ]);
    $examen = Examen::factory()->create();
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    Resultado::create([
        'parametro_id' => Parametro::create([
            'nombre' => 'Hemoglobina', 'slug' => 'hemoglobina', 'tipo_dato' => 'number', 'examen_id' => $examen->id,
        ])->id,
        'procedimiento_id' => $orden->procedimientos()->firstOrFail()->id,
        'resultado' => '13.5',
    ]);

    $this->actingAs($this->user)
        ->get(route('resultados.reporte_orden', $orden))
        ->assertOk()
        ->assertSee('RM 12345678')
        ->assertDontSee('Registro médico')
        ->assertDontSee('NIT ');
});

it('parte el nombre del laboratorio en dos líneas y muestra sede y REPS', function () {
    config(['instancias.cliente.titulo_profesional' => 'Dra.']);
    $this->sede->empresa->update(['razon_social' => 'LABORATORIO CLINICO CLAUDIA PATRICIA BUITRAGO HERNANDEZ']);
    $this->sede->update(['nombre' => 'Laboratorio', 'codigo_prestador' => '110010822701']);
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create());

    $this->actingAs($this->user)
        ->get(route('resultados.reporte_orden', $orden))
        ->assertOk()
        ->assertSee('<div class="lab-tipo">Laboratorio clínico</div>', false)
        ->assertSee('<div class="lab-nombre">Dra. Claudia Patricia Buitrago Hernandez</div>', false)
        ->assertSeeInOrder(['Sede', 'Laboratorio', 'REPS 110010822701']);
});

it('usa una sola línea cuando la razón social no empieza por laboratorio clínico', function () {
    $this->sede->empresa->update(['razon_social' => 'Biotek S.A.S.']);
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create());

    $this->actingAs($this->user)
        ->get(route('resultados.reporte_orden', $orden))
        ->assertOk()
        ->assertDontSee('class="lab-tipo"', false)
        ->assertSee('<div class="lab-nombre">Biotek S.A.S.</div>', false);
});

it('toma el registro médico del empleado con rol prestador si ningún cargo coincide', function () {
    $this->user->empleado->update(['cargo' => 'Bacteriologa', 'numero_documento' => '51934571']);
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create(), Estado::TERMINADO->value);

    expect(app(\App\Services\ConstruirReporteResultados::class)->paraOrden($orden)['laboratorio']['registro_medico'])
        ->toBe('51934571');
});

it('impide descargar un reporte desde una sede no autorizada', function () {
    $examen = Examen::factory()->create();
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $otraSede = Sede::factory()->create();
    $this->withSession(['sede' => $otraSede]);

    $this->actingAs($this->user)
        ->get(route('resultados.reporte_pdf', $orden))
        ->assertForbidden();
    $this->assertDatabaseMissing('eventos_actividad', ['evento' => 'resultados.descargados']);
});

it('deniega la descarga a usuarios sin permiso para ver resultados', function () {
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create());
    $usuarioContable = User::factory()->create([
        'email' => \Illuminate\Support\Str::uuid().'@example.test',
    ]);
    $usuarioContable->assignRole('contable');

    $this->actingAs($usuarioContable)
        ->get(route('resultados.reporte_pdf', $orden))
        ->assertForbidden();
    $this->assertDatabaseMissing('eventos_actividad', ['evento' => 'resultados.descargados']);
});

it('conserva las columnas de resultados de texto y carga firmas y logos por nombre de archivo', function () {
    \Illuminate\Support\Facades\Storage::fake('public');
    $imagen = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=');
    $disco = \Illuminate\Support\Facades\Storage::disk('public');
    \Illuminate\Support\Facades\Storage::fake('local');
    \Illuminate\Support\Facades\Storage::disk('local')->put('firmas/profesional.png', $imagen);
    $disco->put('logos/laboratorio.png', $imagen);
    $this->sede->update(['logo' => 'laboratorio.png']);
    $empleado = $this->user->empleado;
    $empleado->update(['firma' => 'profesional.png']);
    $examen = Examen::factory()->create();
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $procedimiento = $orden->procedimientos()->firstOrFail();
    $procedimiento->update(['empleado_id' => $empleado->id]);
    foreach (['text' => 'Aspecto', 'textarea' => 'Comentario'] as $tipo => $nombre) {
        $parametro = Parametro::create([
            'nombre' => $nombre,
            'slug' => strtolower($nombre),
            'tipo_dato' => $tipo,
            'unidades' => 'unidad',
            'examen_id' => $examen->id,
        ]);
        Resultado::create([
            'parametro_id' => $parametro->id,
            'procedimiento_id' => $procedimiento->id,
            'resultado' => 'Transparente',
        ]);
    }

    $servicio = app(\App\Services\ConstruirReporteResultados::class);
    $reporte = $servicio->paraOrden($orden);
    $uri = 'data:image/png;base64,'.base64_encode($imagen);
    expect($reporte['laboratorio']['logo_src'])->toBe($uri);
    expect($reporte['examenes'][0]['bacteriologo']['firma_src'])->toBe($uri);
    expect($reporte['examenes'][0]['grupos'][0]['parametros'][0]['tipo'])->toBe('valor');
    expect($reporte['examenes'][0]['grupos'][0]['parametros'][1]['tipo'])->toBe('texto');

    $this->actingAs($this->user)->get(route('resultados.show', $procedimiento))
        ->assertOk()->assertSee($uri, false)->assertSee('col-unidades', false);

    $empleado->update(['firma' => 'firmas/profesional.png']);
    expect($servicio->paraOrden($orden)['examenes'][0]['bacteriologo']['firma_src'])->toBe($uri);
});

it('reserva el destino de remisiones al personal y conserva el examen en el ticket', function (string $rol, bool $verDestino) {
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create());
    $orden->remisiones()->create([
        'cup' => '902299',
        'nombre' => 'Examen remitido en ticket',
        'valor' => 25,
        'destino' => 'Destino exclusivo del personal',
    ]);
    $this->user->syncRoles([$rol]);
    $this->user->givePermissionTo('ver_orden');

    $response = $this->actingAs($this->user)
        ->get(route('ordenes.show', $orden))
        ->assertOk()
        ->assertSee('Examen remitido en ticket')
        ->assertSee('Remisión · CUP 902299');

    if ($verDestino) {
        $response->assertSee('Destino exclusivo del personal');
    } else {
        $response->assertDontSee('Destino exclusivo del personal');
    }
})->with([
    ['prestador', true],
    ['agente', true],
    ['admin', true],
    ['contable', false],
    ['paciente', false],
]);

it('espera a las remisiones y reabre la orden cuando una vuelve a pendiente', function () {
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create(), Estado::TERMINADO->value);
    expect($orden->fresh()->terminada)->not->toBeNull();
    $remision = $orden->remisiones()->create([
        'cup' => '903895', 'nombre' => 'Creatinina', 'valor' => 20000, 'destino' => 'Laboratorio externo',
    ]);
    expect($orden->fresh()->terminada)->toBeNull();
    $remision->update(['estado' => Estado::TERMINADO]);
    expect($orden->fresh()->terminada)->not->toBeNull();
    $remision->update(['estado' => Estado::PROCESO]);
    expect($orden->fresh()->terminada)->toBeNull();
    $remision->delete();
    expect($orden->fresh()->terminada)->not->toBeNull();
});

it('termina una orden de solo remisiones cuando todas terminan, se entregan o se anulan', function () {
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create());
    $orden->procedimientos->each->delete();
    $remision = $orden->remisiones()->create([
        'cup' => '903895', 'nombre' => 'Creatinina', 'valor' => 20000, 'destino' => 'Laboratorio externo',
    ]);
    expect($orden->fresh()->terminada)->toBeNull();
    $remision->update(['estado' => Estado::ANULADO]);
    expect($orden->fresh()->terminada)->not->toBeNull();
    $remision->update(['estado' => Estado::ENTREGADO]);
    expect($orden->fresh()->terminada)->not->toBeNull();
});

it('permite controlar las remisiones desde el detalle y sincroniza el cierre', function () {
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create(), Estado::TERMINADO->value);
    $remision = $orden->remisiones()->create([
        'cup' => '903895', 'nombre' => 'Creatinina remitida', 'valor' => 20000, 'destino' => 'Laboratorio externo',
    ]);
    $url = route('ordenes.remisiones.estado', [$orden, $remision]);
    $this->actingAs($this->user)->get(route('ordenes.show', $orden))
        ->assertOk()->assertSee($url, false)->assertSee('0 de 1 finalizadas');

    foreach ([Estado::MUESTRA, Estado::PROCESO, Estado::TERMINADO] as $estado) {
        $this->patch($url, ['estado' => $estado->value])->assertRedirect(route('ordenes.show', $orden));
        expect($remision->fresh()->estado)->toBe($estado);
    }
    expect($orden->fresh()->terminada)->not->toBeNull();
    $this->get(route('ordenes.show', $orden))->assertSee('1 de 1 finalizadas');
    $this->patch($url, ['estado' => Estado::PENDIENTE->value])->assertRedirect();
    expect($orden->fresh()->terminada)->toBeNull();
    $this->patch($url, ['estado' => 'inexistente'])->assertSessionHasErrors('estado');
});

it('rechaza cambiar una remision de otra orden o sede y exige permiso', function () {
    $orden = crearOrdenParaEdicion($this, Examen::factory()->create());
    $otra = crearOrdenParaEdicion($this, Examen::factory()->create());
    $remision = $otra->remisiones()->create([
        'cup' => '903895', 'nombre' => 'Creatinina', 'valor' => 20000, 'destino' => 'Laboratorio externo',
    ]);
    $this->actingAs($this->user)->patch(route('ordenes.remisiones.estado', [$orden, $remision]), [
        'estado' => Estado::TERMINADO->value,
    ])->assertNotFound();

    $otra->update(['sede_id' => Sede::factory()->create()->id]);
    $this->patch(route('ordenes.remisiones.estado', [$otra, $remision]), [
        'estado' => Estado::TERMINADO->value,
    ])->assertForbidden();

    $otra->update(['sede_id' => $this->sede->id]);
    $this->user->syncRoles(['paciente']);
    $this->patch(route('ordenes.remisiones.estado', [$otra, $remision]), [
        'estado' => Estado::TERMINADO->value,
    ])->assertForbidden();
    expect($remision->fresh()->estado)->toBe(Estado::PENDIENTE);
});

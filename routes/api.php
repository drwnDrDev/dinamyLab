<?php

use App\Http\Controllers\Api\AfiliacionSaludController;
use App\Http\Controllers\Api\CodigoDiagnosticoController;
use App\Http\Controllers\Api\CupsController;
use App\Http\Controllers\Api\ExamenesController;
use App\Http\Controllers\Api\FrontendDataController;
use App\Http\Controllers\Api\ListaPersonasController;
use App\Http\Controllers\Api\MunicipioController;
use App\Http\Controllers\Api\OrdenController;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\Api\ResultadosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'api'])->group(function () {
    Route::get('/static-data-for-frontend', [FrontendDataController::class, 'getStaticData'])
        ->middleware('throttle:60,1');

    Route::middleware('can:ver_persona')->group(function () {
        Route::get('personas', [PersonaController::class, 'index']);
        Route::get('personas/{id}', [PersonaController::class, 'show']);
        Route::get('personas/buscar/{numero_documento}', [PersonaController::class, 'buscar']);
    });

    Route::middleware('can:crear_persona')->group(function () {
        Route::post('personas', [PersonaController::class, 'store']);
        Route::post('personas/parsear-lista', [ListaPersonasController::class, 'parsearLista']);
    });

    Route::middleware('can:editar_persona')->group(function () {
        Route::put('personas/{id}', [PersonaController::class, 'update']);
    });

    Route::delete('personas/{id}', [PersonaController::class, 'destroy'])
        ->middleware('can:eliminar_persona');

    Route::middleware('can:ver_orden')->group(function () {
        Route::get('orden/{id}', [OrdenController::class, 'show']);
        Route::get('ordenes/max-numero', [OrdenController::class, 'max_numero']);
        Route::get('ordenes/verificar-numero', [OrdenController::class, 'verificarNumero']);
    });

    Route::post('ordenes', [OrdenController::class, 'store'])->middleware('can:crear_orden');
    Route::patch('ordenes-medicas/{orden}', [OrdenController::class, 'add'])
        ->middleware('can:editar_orden')
        ->name('ordenes.add');
    Route::delete('ordenes/{id}', [OrdenController::class, 'destroy'])->middleware('can:eliminar_orden');

    Route::middleware('can:ver_examen')->group(function () {
        Route::get('cups', [CupsController::class, 'index']);
        Route::get('cups/buscar/{codigo}', [CupsController::class, 'buscarPorCodigo']);
        Route::get('cups/buscar', [CupsController::class, 'buscarPorNombre']);
        Route::get('cups/{codigo}', [CupsController::class, 'show']);
        Route::get('examenes', [ExamenesController::class, 'index']);
        Route::get('examenes/{id}', [ExamenesController::class, 'show']);
    });

    Route::post('cups', [CupsController::class, 'store'])->middleware('role:admin');
    Route::put('cups/{id}', [CupsController::class, 'update'])->middleware('role:admin');
    Route::put('cups/{id}/activar', [CupsController::class, 'activar'])->middleware('role:admin');
    Route::delete('cups/{id}', [CupsController::class, 'destroy'])->middleware('role:admin');
    Route::patch('examenes/{id}/update', [ExamenesController::class, 'update'])
        ->middleware('can:editar_descripcion_examen');

    Route::get('procedimientos/examen/{examenId}/pendientes', [ExamenesController::class, 'obtenerProcedimientosPendientes'])
        ->middleware('can:ver_procedimiento');
    Route::delete('procedimientos/{procedimiento}', [\App\Http\Controllers\Api\ProcedimientosController::class, 'destroy'])
        ->middleware('can:eliminar_procedimiento');
    Route::post('resultados/{procedimiento}', [ResultadosController::class, 'store'])
        ->middleware([\App\Http\Middleware\EnsurePrestador::class, 'can:registrar_resultado']);

    Route::middleware('can:ver_examen')->group(function () {
        Route::get('servicios-habilitados', [\App\Http\Controllers\Api\ServicioHabilitadoController::class, 'index']);
        Route::get('servicios-habilitados/{codigo}', [\App\Http\Controllers\Api\ServicioHabilitadoController::class, 'show']);
        Route::get('servicios-habilitados/buscar', [\App\Http\Controllers\Api\ServicioHabilitadoController::class, 'buscarPorNombre']);

        Route::get('modalidades-atencion', [\App\Http\Controllers\Api\ModalidadAtencionController::class, 'index']);
        Route::get('modalidades-atencion/{codigo}', [\App\Http\Controllers\Api\ModalidadAtencionController::class, 'show']);
        Route::get('modalidades-atencion/buscar', [\App\Http\Controllers\Api\ModalidadAtencionController::class, 'buscarPorNombre']);

        Route::get('finalidades', [\App\Http\Controllers\Api\FinalidadController::class, 'index']);
        Route::get('finalidades/{codigo}', [\App\Http\Controllers\Api\FinalidadController::class, 'show']);
        Route::get('finalidades/buscar', [\App\Http\Controllers\Api\FinalidadController::class, 'buscarPorNombre']);

        Route::get('causa-atencion', [\App\Http\Controllers\Api\CausaExternaController::class, 'index']);
        Route::get('causa-atencion/{codigo}', [\App\Http\Controllers\Api\CausaExternaController::class, 'show']);
        Route::get('causa-atencion/buscar', [\App\Http\Controllers\Api\CausaExternaController::class, 'buscarPorNombre']);

        Route::get('via-ingreso', [\App\Http\Controllers\Api\ViaIngresoController::class, 'index']);
        Route::get('via-ingreso/{codigo}', [\App\Http\Controllers\Api\ViaIngresoController::class, 'show']);

        Route::get('cie10', [CodigoDiagnosticoController::class, 'index']);
        Route::get('cie10/gestion', [CodigoDiagnosticoController::class, 'gestion']);
        Route::get('cie10/{id}', [CodigoDiagnosticoController::class, 'show']);
        Route::post('cie10/buscar', [CodigoDiagnosticoController::class, 'search']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::patch('servicios-habilitados/{codigo}/activar', [\App\Http\Controllers\Api\ServicioHabilitadoController::class, 'activar'])
            ->name('servicios-habilitados.activar');
        Route::patch('modalidades-atencion/{codigo}/activar', [\App\Http\Controllers\Api\ModalidadAtencionController::class, 'activar'])
            ->name('modalidades-atencion.activar');
        Route::patch('finalidades/{codigo}/activar', [\App\Http\Controllers\Api\FinalidadController::class, 'activar'])
            ->name('finalidades.activar');
        Route::patch('causa-atencion/{codigo}/activar', [\App\Http\Controllers\Api\CausaExternaController::class, 'activar'])
            ->name('causa-atencion.activar');
        Route::patch('via-ingreso/{codigo}/activar', [\App\Http\Controllers\Api\ViaIngresoController::class, 'activar'])
            ->name('vias-ingreso.activar');

        Route::post('cie10', [CodigoDiagnosticoController::class, 'store']);
        Route::patch('cie10/{codigo}/activar', [CodigoDiagnosticoController::class, 'activar']);

        Route::resource('tarifas', \App\Http\Controllers\Api\TarifaController::class);
    });

    Route::get('municipios', [MunicipioController::class, 'index']);
    Route::get('municipios/buscar', [MunicipioController::class, 'buscarMunicipioPorNombre']);
    Route::get('municipios/{id}', [MunicipioController::class, 'show']);
    Route::get('departamento/{departamento_id}', [MunicipioController::class, 'departamento']);

    Route::get('dashboard-data', [\App\Http\Controllers\Api\DashboardController::class, 'index'])
        ->middleware('can:ver_procedimiento');

    Route::middleware('can:ver_persona')->group(function () {
        Route::get('paises', [\App\Http\Controllers\Api\PaisController::class, 'index']);
        Route::get('afiliaciones-salud', [AfiliacionSaludController::class, 'index']);
        Route::get('afiliaciones-salud/{id}', [AfiliacionSaludController::class, 'show']);
        Route::get('tipos-afiliacion', [\App\Http\Controllers\Api\TipoAfiliacionController::class, 'index']);
        Route::get('tipos-afiliacion/{codigo}', [\App\Http\Controllers\Api\TipoAfiliacionController::class, 'show']);
    });

    Route::post('afiliaciones-salud', [AfiliacionSaludController::class, 'store'])
        ->middleware('can:crear_persona');
    Route::put('afiliaciones-salud/{id}', [AfiliacionSaludController::class, 'update'])
        ->middleware('can:editar_persona');
    Route::delete('afiliaciones-salud/{id}', [AfiliacionSaludController::class, 'destroy'])
        ->middleware('can:editar_persona');

    Route::patch('tipos-afiliacion/{codigo}/activar', [\App\Http\Controllers\Api\TipoAfiliacionController::class, 'activar'])
        ->middleware('role:admin')
        ->name('tipos-afiliacion.activar');
});

Route::get('user', fn (Request $request) => $request->user())->middleware('auth:sanctum');

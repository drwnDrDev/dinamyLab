<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CodigoDiagnosticoController;
use App\Http\Controllers\Api\CupsController;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\Api\MunicipioController;
use App\Http\Controllers\Api\FrontendDataController;
use App\Http\Controllers\Api\ExamenesController;
use App\Http\Controllers\Api\OrdenController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\Api\AfiliacionSaludController;
use App\Models\Factura;

Route::get('/static-data-for-frontend', [FrontendDataController::class, 'getStaticData']);

Route::get('cups', [CupsController::class, 'index']);
Route::get('cups/{codigo}', [CupsController::class, 'show']);
Route::get('cups/buscar/{codigo}', [CupsController::class, 'buscarPorCodigo']);
Route::get('cups/buscar', [CupsController::class, 'buscarPorNombre']);

Route::post('cups', [CupsController::class, 'store']);
Route::put('cups/{id}', [CupsController::class, 'update']);
Route::put('cups/{id}/activar', [CupsController::class, 'activar']);
Route::delete('cups/{id}', [CupsController::class, 'destroy']);

Route::get('servicios-habilitados', [\App\Http\Controllers\Api\ServicioHabilitadoController::class, 'index']);
Route::get('servicios-habilitados/{codigo}', [\App\Http\Controllers\Api\ServicioHabilitadoController::class, 'show']);
Route::get('servicios-habilitados/buscar', [\App\Http\Controllers\Api\ServicioHabilitadoController::class, 'buscarPorNombre']);
Route::patch('servicios-habilitados/{codigo}/activar', [\App\Http\Controllers\Api\ServicioHabilitadoController::class, 'activar'])->name('servicios-habilitados.activar');

Route::get('modalidades-atencion', [\App\Http\Controllers\Api\ModalidadAtencionController::class, 'index']);
Route::get('modalidades-atencion/{codigo}', [\App\Http\Controllers\Api\ModalidadAtencionController::class, 'show']);
Route::get('modalidades-atencion/buscar', [\App\Http\Controllers\Api\ModalidadAtencionController::class, 'buscarPorNombre']);
Route::patch('modalidades-atencion/{codigo}/activar', [\App\Http\Controllers\Api\ModalidadAtencionController::class, 'activar'])->name('modalidades-atencion.activar');

Route::get('finalidades', [\App\Http\Controllers\Api\FinalidadController::class, 'index']);
Route::get('finalidades/{codigo}', [\App\Http\Controllers\Api\FinalidadController::class, 'show']);
Route::get('finalidades/buscar', [\App\Http\Controllers\Api\FinalidadController::class, 'buscarPorNombre']);
Route::patch('finalidades/{codigo}/activar', [\App\Http\Controllers\Api\FinalidadController::class, 'activar'])->name('finalidades.activar');

Route::get('causa-atencion', [\App\Http\Controllers\Api\CausaExternaController::class, 'index']);
Route::get('causa-atencion/{codigo}', [\App\Http\Controllers\Api\CausaExternaController::class, 'show']);
Route::get('causa-atencion/buscar', [\App\Http\Controllers\Api\CausaExternaController::class, 'buscarPorNombre']);
Route::patch('causa-atencion/{codigo}/activar', [\App\Http\Controllers\Api\CausaExternaController::class, 'activar'])->name('causa-atencion.activar');

Route::get('via-ingreso', [\App\Http\Controllers\Api\ViaIngresoController::class, 'index']);
Route::get('via-ingreso/{codigo}', [\App\Http\Controllers\Api\ViaIngresoController::class, 'show']);
Route::patch('via-ingreso/{codigo}/activar', [\App\Http\Controllers\Api\ViaIngresoController::class, 'activar'])->name('vias-ingreso.activar');



Route::get('cie10', [CodigoDiagnosticoController::class, 'index']);
Route::get('cie10/{id}', [CodigoDiagnosticoController::class, 'show']);
Route::post('cie10', [CodigoDiagnosticoController::class, 'store']);
Route::patch('cie10/{id}', [CodigoDiagnosticoController::class, 'update']);
Route::patch('cie10/{id}/activar', [CodigoDiagnosticoController::class, 'activar'])->name('cie10.activar');

Route::get('cie10/buscar', [CodigoDiagnosticoController::class, 'buscarPorNombre']);
Route::get('cie10/buscar/{codigo}', [CodigoDiagnosticoController::class, 'buscarPorCodigo']);
Route::delete('cie10/{id}', [CodigoDiagnosticoController::class, 'destroy']);

Route::get('personas', [PersonaController::class, 'index']);
Route::get('personas/{id}', [PersonaController::class, 'show']);
Route::post('personas', [PersonaController::class, 'store']);
Route::put('personas/{id}', [PersonaController::class, 'update']);
Route::get('personas/buscar/{numero_documento}', [PersonaController::class, 'buscar']);

Route::get('examenes', [ExamenesController::class, 'index']);
Route::get('examenes/{id}', [ExamenesController::class, 'show']);

Route::get('municipios', [MunicipioController::class, 'index']);
Route::get('municipios/buscar', [MunicipioController::class, 'buscarMunicipioPorNombre']);
Route::get('municipios/{id}', [MunicipioController::class, 'show']);
Route::get('departamento/{departamento_id}', [MunicipioController::class, 'departamento']);

Route::get('afiliaciones-salud', [AfiliacionSaludController::class, 'index']);
Route::post('afiliaciones-salud', [AfiliacionSaludController::class, 'store']);
Route::get('afiliaciones-salud/{id}', [AfiliacionSaludController::class, 'show']);
Route::put('afiliaciones-salud/{id}', [AfiliacionSaludController::class, 'update']);
Route::delete('afiliaciones-salud/{id}', [AfiliacionSaludController::class, 'destroy']);

Route::get('tipos-afiliacion', [\App\Http\Controllers\Api\TipoAfiliacionController::class, 'index']);
Route::get('tipos-afiliacion/{codigo}', [\App\Http\Controllers\Api\TipoAfiliacionController::class, 'show']);
Route::patch('tipos-afiliacion/{codigo}/activar', [\App\Http\Controllers\Api\TipoAfiliacionController::class, 'activar'])->name('tipos-afiliacion.activar');

Route::get('paises', [\App\Http\Controllers\Api\PaisController::class, 'index']);

Route::get('orden/{id}', [OrdenController::class, 'show']);
Route::patch('/ordenes-medicas/{orden}',[OrdenController::class,'add'])->name('ordenes.add');



Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CodigoCupController;
use App\Http\Controllers\Api\CodigoDiagnosticoController;
use App\Http\Controllers\api\CupsController;
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
Route::get('cups/{id}', [CupsController::class, 'show']);
Route::get('cups/buscar/{codigo}', [CupsController::class, 'buscarPorCodigo']);
Route::get('cups/buscar', [CupsController::class, 'buscarPorNombre']);

Route::post('cups', [CupsController::class, 'store']);
Route::put('cups/{id}', [CupsController::class, 'update']);
Route::put('cups/{id}/activar', [CupsController::class, 'activar']);
Route::delete('cups/{id}', [CupsController::class, 'destroy']);

Route::get('cie10', [CodigoDiagnosticoController::class, 'index']);
Route::get('cie10/{id}', [CodigoDiagnosticoController::class, 'show']);
Route::post('cie10', [CodigoDiagnosticoController::class, 'store']);
Route::put('cie10/{id}', [CodigoDiagnosticoController::class, 'update']);
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

Route::get('paises', [\App\Http\Controllers\Api\PaisController::class, 'index']);

Route::get('orden/{id}', [OrdenController::class, 'show']);
Route::patch('/ordenes-medicas/{orden}',[OrdenController::class,'add'])->name('ordenes.add');



Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

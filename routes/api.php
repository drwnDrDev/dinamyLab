<?php

use App\Http\Controllers\Api\ExamenesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\Api\MunicipioController;


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

Route::get('paises', [\App\Http\Controllers\Api\PaisController::class, 'index']);


Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

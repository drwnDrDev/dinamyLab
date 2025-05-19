<?php

use App\Http\Controllers\Api\ExamenesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PersonaController;


Route::get('personas', [PersonaController::class, 'index']);
Route::get('personas/{id}', [PersonaController::class, 'show']);
Route::post('personas', [PersonaController::class, 'store']);

Route::get('examenes', [ExamenesController::class, 'index']);
Route::get('examenes/{id}', [ExamenesController::class, 'show']);

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

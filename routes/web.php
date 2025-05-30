<?php

use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProcedimientoController;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard',[EmpleadoController::class, 'dashboard'])->name('dashboard');
    Route::get('/personas',[PersonaController::class,'index'])->name('personas');
    Route::get('/personas/create',[PersonaController::class,'create'])->name('personas.create');
    Route::get('/personas/{persona}',[PersonaController::class,'show'])->name('personas.show');
    Route::post('/personas/store',[PersonaController::class,'store'])->name('personas.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/ordenes-medicas',[OrdenController::class,'index'])->name('ordenes');
    Route::get('/ordenes-medicas/create',[OrdenController::class,'create'])->name('ordenes.create');
    Route::post('/ordenes-medicas/store',[OrdenController::class,'store'])->name('ordenes.store');
    Route::get('/ordenes-medicas/{orden}/edit',[OrdenController::class,'edit'])->name('ordenes.edit');
    Route::put('/ordenes-medicas/{orden}',[OrdenController::class,'update'])->name('ordenes.update');
    Route::get('/ordenes-medicas/{orden}/ver',[OrdenController::class,'show'])->name('ordenes.show');
    Route::get('/facturas',[FacturaController::class,'index'])->name('facturas');
    Route::get('/facturas/{factura}',[FacturaController::class,'show'])->name('facturas.show');
    Route::get('/facturas/create',[FacturaController::class,'create'])->name('facturas.create');
    Route::post('/facturas/store',[FacturaController::class,'store'])->name('facturas.store');
    Route::get('/resultados/{orden}/{examen}',[OrdenController::class,'resultados'])->name('resultados.create');

    Route::get('/procedimientos/{procedimiento}',[ProcedimientoController::class,'show'])->name('procedimientos.show');

});
Route::middleware('auth', 'verified','can:eliminar_persona')->group(function () {
    Route::delete('/personas/{persona}',[PersonaController::class,'destroy'])->name('personas.destroy');
    Route::get('/personas/{persona}/edit',[PersonaController::class,'edit'])->name('personas.edit');
    Route::put('/personas/{persona}',[PersonaController::class,'update'])->name('personas.update');
    Route::delete('/ordenes-medicas/{orden}',[OrdenController::class,'destroy'])->name('ordenes.destroy');

});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\AdministracionController;
use App\Http\Controllers\ConvenioController;
use App\Http\Controllers\CodigoCupController;
use App\Http\Controllers\CodigoDiagnosticoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProcedimientoController;
use App\Http\Controllers\ResultadosController;
use App\Http\Controllers\ResolucionController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\SedeController;
use App\Models\Resultado;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard',[EmpleadoController::class, 'select'])->name('dashboard');
    Route::get('/inicio',[EmpleadoController::class, 'dashboard'])->name('inicio');
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

    Route::get('/resultados/{procedimiento}',[ResultadosController::class,'create'])->name('resultados.create');
    Route::post('/resultados/{procedimiento}/store',[ResultadosController::class,'store'])->name('resultados.store');
    Route::get('/resultados',[ResultadosController::class,'index'])->name('resultados');

    Route::get('/resultados/{persona}/historia',[ResultadosController::class,'historia'])->name('resultados.historia');
    Route::post('/resultados/{persona}/historia',[ResultadosController::class,'historia_show'])->name('resultados.historia_show');

    Route::get('/procedimientos',[ProcedimientoController::class,'index'])->name('procedimientos');
    Route::get('/procedimientos/{procedimiento}',[ProcedimientoController::class,'show'])->name('procedimientos.show');
    Route::patch('/procedimientos/{procedimiento}/estado',[ProcedimientoController::class,'observaciones'])->name('procedimientos.observaciones');


    Route::get('/caja',[AdministracionController::class,'caja'])->name('caja');
    Route::get('/caja/ingresos',[AdministracionController::class,'rips'])->name('rips');
    Route::get('/examenes',[ExamenController::class,'index'])->name('examenes');
    Route::get('/examenes/{examen}',[ExamenController::class,'show'])->name('examenes.show');

    Route::post('search',[SearchController::class,'search'])->name('search');
    Route::get('search',[SearchController::class,'search'])->name('search');

    Route::get('/resultados/{procedimiento}/ver',[ResultadosController::class,'show'])->name('resultados.show');

    Route::get('reportes',[ProcedimientoController::class,'reportes'])->name('reportes');

    Route::get('administracion/sede/{sede}',[SedeController::class,'elegirSede'])->name('elegir.sede');

});

Route::middleware('auth','verified','can:ver_facturas')->group(function () {
    Route::get('/convenios',[ConvenioController::class,'index'])->name('convenios.index');
    Route::get('/convenios/create',[ConvenioController::class,'create'])->name('convenios.create');
    Route::post('/convenios/store',[ConvenioController::class,'store'])->name('convenios.store');
    Route::get('/convenios/{convenio}',[ConvenioController::class,'show'])->name('convenios.show');
    Route::get('/convenios/{convenio}/edit',[ConvenioController::class,'edit'])->name('convenios.edit');
    Route::put('/convenios/{convenio}',[ConvenioController::class,'update'])->name('convenios.update');
    Route::delete('/convenios/{convenio}',[ConvenioController::class,'destroy'])->name('convenios.destroy');
    Route::get('/facturas',[FacturaController::class,'index'])->name('facturas');
    Route::get('/facturas/create/{persona}',[FacturaController::class,'create'])->name('facturas.create');
    Route::post('/facturas/store',[FacturaController::class,'store'])->name('facturas.store');
    Route::get('/facturas/{factura}',[FacturaController::class,'show'])->name('facturas.show');
    Route::get('/facturas/resoluciones/create',[ResolucionController::class,'create'])->name('resoluciones.create');
    Route::post('/facturas/resoluciones/store',[ResolucionController::class,'store'])->name('resoluciones.store');
    Route::get('/facturas/resoluciones',[ResolucionController::class,'index'])->name('resoluciones.index');
    Route::get('/facturas/resoluciones/{resolucion}',[ResolucionController::class,'show'])->name('resoluciones.show');
    Route::get('/facturas/resoluciones/{resolucion}/edit',[ResolucionController::class,'edit'])->name('resoluciones.edit');
    Route::put('/facturas/resoluciones/{resolucion}',[ResolucionController::class,'update'])->name('resoluciones.update');
    Route::delete('/facturas/resoluciones/{resolucion}',[ResolucionController::class,'destroy'])->name('resoluciones.destroy');

});

Route::middleware('auth', 'verified','can:eliminar_persona')->group(function () {

    Route::get('/cups',[CodigoCupController::class,'index'])->name('cups.index');
    Route::post('/cups/buscar',[CodigoCupController::class,'search'])->name('cups.search');
    Route::get('/cups/{codigoCup}',[CodigoCupController::class,'show'])->name('cups.show');
    Route::get('/cie10',[CodigoDiagnosticoController::class,'index'])->name('cie10.index');
    Route::post('/cie10/buscar',[CodigoDiagnosticoController::class,'search'])->name('cie10.search');
    Route::get('/cie10/{codigoDiagnostico}',[CodigoDiagnosticoController::class,'show'])->name('cie10.show');
    Route::get('/administracion/configuracion',[AdministracionController::class,'setup'])->name('empleados.index');
    Route::delete('/personas/{persona}',[PersonaController::class,'destroy'])->name('personas.destroy');
    Route::get('/personas/{persona}/edit',[PersonaController::class,'edit'])->name('personas.edit');
    Route::put('/personas/{persona}',[PersonaController::class,'update'])->name('personas.update');
    Route::delete('/ordenes-medicas/{orden}',[OrdenController::class,'destroy'])->name('ordenes.destroy');

});

require __DIR__.'/auth.php';

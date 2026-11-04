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
use App\Http\Controllers\ReprocesarController;
use App\Http\Controllers\ResultadosController;
use App\Http\Controllers\ResolucionController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\SedeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->to(route('dashboard', absolute: false))
    : view('welcome'));

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/reprocesar', [ReprocesarController::class, 'showUploadForm'])->name('reprocesar.upload');
    Route::post('/reprocesar', [ReprocesarController::class, 'upload'])->name('reprocesar.upload.post');
    Route::post('/reprocesar/reprocesar', [ReprocesarController::class, 'reprocesar'])->name('reprocesar.reprocesar');
    Route::get('/reprocesar/exportar', [ReprocesarController::class, 'exportar'])->name('reprocesar.exportar');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [EmpleadoController::class, 'select'])
        ->middleware('can:ver_persona')
        ->name('dashboard');
    Route::get('/inicio', [EmpleadoController::class, 'dashboard'])
        ->middleware('can:ver_persona')
        ->name('inicio');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('administracion/sede/{sede}', [SedeController::class, 'elegirSede'])->name('elegir.sede');

    Route::middleware('can:ver_persona')->group(function () {
        Route::get('/personas', [PersonaController::class, 'index'])->name('personas');
        Route::get('/personas/{persona}', [PersonaController::class, 'show'])->name('personas.show');
    });

    Route::middleware('can:crear_persona')->group(function () {
        Route::get('/personas/create', [PersonaController::class, 'create'])->name('personas.create');
        Route::post('/personas/store', [PersonaController::class, 'store'])->name('personas.store');
    });

    Route::middleware('can:editar_persona')->group(function () {
        Route::get('/personas/{persona}/edit', [PersonaController::class, 'edit'])->name('personas.edit');
        Route::put('/personas/{persona}', [PersonaController::class, 'update'])->name('personas.update');
    });

    Route::delete('/personas/{persona}', [PersonaController::class, 'destroy'])
        ->middleware('can:eliminar_persona')
        ->name('personas.destroy');

    Route::middleware('can:ver_orden')->group(function () {
        Route::get('/ordenes-medicas', [OrdenController::class, 'index'])->name('ordenes');
        Route::get('/ordenes-medicas/{orden}/ver', [OrdenController::class, 'show'])->name('ordenes.show');
    });

    Route::middleware('can:crear_orden')->group(function () {
        Route::get('/ordenes-medicas/create/{persona?}', [OrdenController::class, 'create'])->name('ordenes.create');
        Route::post('/ordenes-medicas/store', [OrdenController::class, 'store'])->name('ordenes.store');
    });

    Route::middleware('can:editar_orden')->group(function () {
        Route::get('/ordenes-medicas/{orden}/edit', [OrdenController::class, 'edit'])->name('ordenes.edit');
        Route::put('/ordenes-medicas/{orden}', [OrdenController::class, 'update'])->name('ordenes.update');
    });

    Route::delete('/ordenes-medicas/{orden}', [OrdenController::class, 'destroy'])
        ->middleware('can:eliminar_orden')
        ->name('ordenes.destroy');

    Route::middleware('can:ver_procedimiento')->group(function () {
        Route::get('/procedimientos', [ProcedimientoController::class, 'index'])->name('procedimientos');
        Route::get('/procedimientos/{procedimiento}', [ProcedimientoController::class, 'show'])->name('procedimientos.show');
    });

    Route::patch('/procedimientos/{procedimiento}/estado', [ProcedimientoController::class, 'observaciones'])
        ->middleware('can:gestionar_procedimiento')
        ->name('procedimientos.observaciones');

    Route::delete('/procedimientos/{procedimiento}', [ProcedimientoController::class, 'destroy'])
        ->middleware('can:eliminar_procedimiento')
        ->name('procedimientos.destroy');

    Route::middleware('can:ver_resultado')->group(function () {
        Route::get('/resultados', [ResultadosController::class, 'index'])->name('resultados');
        Route::get('/resultados/{procedimiento}/ver', [ResultadosController::class, 'show'])->name('resultados.show');
        Route::get('/resultados/{persona}/historia', [ResultadosController::class, 'historia'])->name('resultados.historia');
        Route::post('/resultados/{persona}/historia', [ResultadosController::class, 'historia_show'])->name('resultados.historia_show');
    });

    Route::middleware('can:registrar_resultado')->group(function () {
        Route::get('/resultados/{procedimiento}', [ResultadosController::class, 'create'])->name('resultados.create');
        Route::post('/resultados/{procedimiento}/store', [ResultadosController::class, 'store'])->name('resultados.store');
        Route::get('/examenes/{examen}/lote', [ExamenController::class, 'lote'])->name('examenes.lote');
        Route::post('/examenes/{examen}/lote/resultados', [ExamenController::class, 'guardarLote'])->name('examenes.lote.store');
    });

    Route::middleware('can:editar_resultado')->group(function () {
        Route::get('/resultados/{procedimiento}/edit', [ResultadosController::class, 'edit'])->name('resultados.edit');
        Route::put('/resultados/{procedimiento}/update', [ResultadosController::class, 'update'])->name('resultados.update');
    });

    Route::middleware('can:ver_examen')->group(function () {
        Route::get('/examenes', [ExamenController::class, 'index'])->name('examenes');
        Route::get('/examenes/{examen}', [ExamenController::class, 'show'])->name('examenes.show');
    });

    Route::middleware('role_or_permission:admin|ver_reportes')->group(function () {
        Route::get('reportes', [ProcedimientoController::class, 'reportes'])->name('reportes');
    });

    Route::match(['get', 'post'], 'search', [SearchController::class, 'search'])
        ->middleware('can:ver_persona')
        ->name('search');

    Route::middleware(['verified', 'role:admin'])->group(function () {
        Route::get('/caja', [AdministracionController::class, 'caja'])->name('caja');
        Route::get('/caja/ingresos', [AdministracionController::class, 'rips'])->name('rips');
        Route::post('reportes/rips', [ProcedimientoController::class, 'json_rips'])->name('descargar.rips');
        Route::get('parametros', [App\Http\Controllers\ParametroController::class, 'index'])->name('parametros.index');
        Route::get('admin/empresa', [App\Http\Controllers\EmpresaController::class, 'show'])->name('empresa.show');
        Route::get('/admin/dashboard', [EmpleadoController::class, 'dashboard'])->name('admin.dashboard');
        Route::resource('/admin/empleados', EmpleadoController::class)->except(['index']);
        Route::resource('/admin/sedes', SedeController::class)->except(['index']);
        Route::get('/cups', [CodigoCupController::class, 'index'])->name('cups.index');
        Route::post('/cups/buscar', [CodigoCupController::class, 'search'])->name('cups.search');
        Route::get('/cups/{codigoCup}', [CodigoCupController::class, 'show'])->name('cups.show');
        Route::get('/cie10', [CodigoDiagnosticoController::class, 'index'])->name('cie10.index');
        Route::post('/cie10/buscar', [CodigoDiagnosticoController::class, 'search'])->name('cie10.search');
        Route::get('/cie10/{codigoDiagnostico}', [CodigoDiagnosticoController::class, 'show'])->name('cie10.show');
        Route::get('/administracion/configuracion', [AdministracionController::class, 'setup'])->name('empleados.index');
        Route::resource('convenios', ConvenioController::class)->except(['index', 'show']);
        Route::get('/convenios', [ConvenioController::class, 'index'])->name('convenios.index');
        Route::get('/convenios/{convenio}', [ConvenioController::class, 'show'])
            ->whereNumber('convenio')
            ->name('convenios.show');
        Route::get('/facturas/create/{persona}', [FacturaController::class, 'create'])->name('facturas.create');
        Route::post('/facturas/store', [FacturaController::class, 'store'])->name('facturas.store');
        Route::get('/facturas/resoluciones/create', [ResolucionController::class, 'create'])->name('resoluciones.create');
        Route::post('/facturas/resoluciones/store', [ResolucionController::class, 'store'])->name('resoluciones.store');
        Route::get('/facturas/resoluciones', [ResolucionController::class, 'index'])->name('resoluciones.index');
        Route::get('/facturas/resoluciones/{resolucion}', [ResolucionController::class, 'show'])->name('resoluciones.show');
        Route::get('/facturas/resoluciones/{resolucion}/edit', [ResolucionController::class, 'edit'])->name('resoluciones.edit');
        Route::put('/facturas/resoluciones/{resolucion}', [ResolucionController::class, 'update'])->name('resoluciones.update');
        Route::delete('/facturas/resoluciones/{resolucion}', [ResolucionController::class, 'destroy'])->name('resoluciones.destroy');
    });

    Route::middleware('can:ver_facturas')->group(function () {
        Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas');
        Route::get('/facturas/{factura}', [FacturaController::class, 'show'])->name('facturas.show');
    });
});

require __DIR__.'/auth.php';

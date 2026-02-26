<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\Admin\DocumentoController;
use App\Http\Controllers\Admin\SubcategoriaController;
use App\Http\Controllers\Admin\MesController;

Route::middleware(['auth', 'verified'])->group(function () {

    // PORTAL
    Route::get('/', [PortalController::class, 'index'])->name('portal.index');
    Route::get('/categoria/{categoria:slug}', [PortalController::class, 'categoria'])->name('portal.categoria');
    Route::get('/categoria/{categoria:slug}/{subcategoria:slug}', [PortalController::class, 'subcategoria'])->name('portal.subcategoria');
    Route::get('/categoria/{categoria:slug}/{subcategoria:slug}/{mes}', [PortalController::class, 'mes'])->name('portal.mes');
    Route::get('/buscar', [PortalController::class, 'buscar'])->name('portal.buscar');
    Route::get('/descargar/{documento}', [PortalController::class, 'descargar'])->name('portal.descargar');

    // ADMIN
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', fn() => redirect()->route('admin.documentos.index'))->name('home');
        Route::resource('usuarios', UserController::class);

        // Rutas AJAX — van antes del resource
        Route::get('documentos-anios', [DocumentoController::class, 'getAnios'])->name('documentos.anios');
        Route::get('documentos-meses', [DocumentoController::class, 'getMeses'])->name('documentos.meses');

        Route::resource('documentos', DocumentoController::class);
        Route::resource('subcategorias', SubcategoriaController::class);
        Route::resource('meses', MesController::class);
    });
});

require __DIR__.'/auth.php';
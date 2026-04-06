<?php

use App\Http\Controllers\SlipGajiController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('pages.list');
    });

    Route::group(['prefix' => 'slip-gaji', 'as' => 'slip-gaji.'], function () {
        Route::get('/', [SlipGajiController::class, 'index'])->name('index');
        Route::get('/create', [SlipGajiController::class, 'create'])->name('create');
        Route::post('/store', [SlipGajiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SlipGajiController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [SlipGajiController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [SlipGajiController::class, 'destroy'])->name('destroy');
        Route::post('/import', [SlipGajiController::class, 'import'])->name('import');
        Route::get('/{id}/export-pdf', [SlipGajiController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/{id}/view-pdf', [SlipGajiController::class, 'viewPdf'])->name('view-pdf');
        Route::post('/{id}/broadcast', [SlipGajiController::class, 'broadcastSingle'])->name('broadcast-single');
        Route::post('/broadcast-bulk', [SlipGajiController::class, 'broadcastBulk'])->name('broadcast-bulk');
    });
});

<?php

use App\Http\Controllers\SlipGajiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KaryawanController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [SlipGajiController::class, 'index']);

    Route::group(['prefix' => 'slip-gaji', 'as' => 'slip-gaji.'], function () {
        Route::get('/', [SlipGajiController::class, 'index'])->name('index');
        Route::get('/create', [SlipGajiController::class, 'create'])->name('create');
        Route::get('/karyawan-details/{id}', [SlipGajiController::class, 'getKaryawanDetails'])->name('karyawan-details');
        Route::get('/calculate-pph21', [SlipGajiController::class, 'calculatePph21'])->name('calculate-pph21');
        Route::get('/calculate-gaji-resign', [SlipGajiController::class, 'calculateGajiResign'])->name('calculate-gaji-resign');
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

    Route::group(['prefix' => 'karyawan', 'as' => 'karyawan.'], function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('index');
        Route::get('/create', [KaryawanController::class, 'create'])->name('create');
        Route::post('/store', [KaryawanController::class, 'store'])->name('store');
        Route::get('/{id}', [KaryawanController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [KaryawanController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [KaryawanController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [KaryawanController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/kompensasi', [KaryawanController::class, 'saveKompensasi'])->name('kompensasi.store');
        Route::post('/{id}/bpjs', [KaryawanController::class, 'saveBpjs'])->name('bpjs.store');
        Route::post('/{id}/pph21', [KaryawanController::class, 'savePph21'])->name('pph21.store');
    });
});

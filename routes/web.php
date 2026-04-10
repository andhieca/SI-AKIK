<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BkuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PejabatController;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\DebugController;

Route::get('/test-zip', function () {
    if (class_exists('ZipArchive')) {
        return "Zip Extension is ENABLED ✅. PHP Version: " . phpversion();
    } else {
        return "Zip Extension is MISSING ❌. PHP Version: " . phpversion() . ". please restart server";
    }
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/display', [\App\Http\Controllers\DashboardController::class, 'display'])->name('display');
    Route::get('/debug', [DebugController::class, 'index'])->name('debug');

    // BKU Routes

    Route::post('/bku/{bku}/validasi', [BkuController::class, 'validasi'])->name('bku.validasi');
    Route::post('/bku/{bku}/batal-validasi', [BkuController::class, 'batalValidasi'])->name('bku.batal_validasi');
    Route::get('/bku/{bku}/print', [BkuController::class, 'print'])->name('bku.print');
    Route::get('/bku/verify/{hash}', [BkuController::class, 'verify'])->name('bku.verify');
    Route::get('/bku/cetak', [BkuController::class, 'cetak'])->name('bku.cetak');
    Route::get('/bku/download-template', [BkuController::class, 'downloadTemplate'])->name('bku.template');
    Route::post('/bku/import', [BkuController::class, 'import'])->name('bku.import');
    Route::get('/bku/generate-no-bukti', [BkuController::class, 'generateNoBukti'])->name('bku.generate_no_bukti');
    Route::resource('bku', BkuController::class);

    // Settings Routes
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');

    // Resource Routes for Strict MVC
    Route::post('/pejabat', [PejabatController::class, 'store'])->name('pejabat.store');
    Route::put('/pejabat/{pejabat}', [PejabatController::class, 'update'])->name('pejabat.update');
    Route::delete('/pejabat/{pejabat}', [\App\Http\Controllers\PejabatController::class, 'destroy'])->name('pejabat.destroy');

    Route::post('anggaran/import', [AnggaranController::class, 'import'])->name('anggaran.import');
    Route::delete('anggaran/delete-all', [AnggaranController::class, 'deleteAll'])->name('anggaran.deleteAll');
    Route::patch('anggaran/{anggaran}/update-pagu', [AnggaranController::class, 'updatePaguInline'])->name('anggaran.updatePagu');
    Route::resource('anggaran', AnggaranController::class);

    // User Management
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'show', 'edit']);
});

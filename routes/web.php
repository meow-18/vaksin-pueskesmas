<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\JenisVaksinController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PenyesuaianStokController;
use App\Http\Controllers\StokController;
use App\Http\Middleware\EnsureRole;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'form'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.proses');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    // Admin: pengguna dan master data (FR-01, FR-02, FR-13), audit log (FR-10).
    Route::middleware(EnsureRole::class . ':' . Role::ADMIN)->group(function () {
        Route::resource('pengguna', PenggunaController::class)->except(['show', 'destroy'])
            ->parameters(['pengguna' => 'pengguna']);
        Route::resource('jenis-vaksin', JenisVaksinController::class)->except(['show', 'destroy'])
            ->parameters(['jenis-vaksin' => 'jenisVaksin']);
        Route::resource('fasilitas', FasilitasController::class)->except(['show', 'destroy'])
            ->parameters(['fasilitas' => 'fasilitas']);
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
    });

    // Apoteker: penerimaan, stok, distribusi, nota, laporan, penyesuaian stok.
    Route::middleware(EnsureRole::class . ':' . Role::APOTEKER)->group(function () {
        Route::get('/penerimaan', [PenerimaanController::class, 'index'])->name('penerimaan.index');
        Route::get('/penerimaan/baru', [PenerimaanController::class, 'create'])->name('penerimaan.create');
        Route::post('/penerimaan', [PenerimaanController::class, 'store'])->name('penerimaan.store');

        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::get('/penyesuaian-stok', [PenyesuaianStokController::class, 'create'])->name('penyesuaian.create');
        Route::post('/penyesuaian-stok', [PenyesuaianStokController::class, 'store'])->name('penyesuaian.store');

        Route::get('/distribusi', [DistribusiController::class, 'index'])->name('distribusi.index');
        Route::get('/distribusi/baru', [DistribusiController::class, 'create'])->name('distribusi.create');
        Route::get('/distribusi/laporan', [DistribusiController::class, 'laporan'])->name('distribusi.laporan');
        Route::post('/distribusi', [DistribusiController::class, 'store'])->name('distribusi.store');
        Route::get('/distribusi/{distribusi}', [DistribusiController::class, 'show'])->name('distribusi.show');
        Route::post('/distribusi/{distribusi}/finalisasi', [DistribusiController::class, 'finalisasi'])->name('distribusi.finalisasi');
        Route::post('/distribusi/{distribusi}/batal', [DistribusiController::class, 'batal'])->name('distribusi.batal');
        Route::delete('/distribusi/{distribusi}', [DistribusiController::class, 'destroy'])->name('distribusi.destroy');
        Route::get('/distribusi/{distribusi}/nota', [NotaController::class, 'pdf'])->name('nota.pdf');
    });
});
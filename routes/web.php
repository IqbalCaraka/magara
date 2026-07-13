<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ScanningController;
use App\Http\Controllers\Admin\SinkroController;
use App\Http\Controllers\Admin\SisiArsipController;
use App\Http\Controllers\Admin\TakahController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/dashboard/chart', [DashboardController::class, 'chart'])->name('dashboard.chart');

    // Ganti Password - semua role
    Route::get('/change-password', [UserController::class, 'changePassword'])->name('change-password');
    Route::post('/change-password', [UserController::class, 'updatePassword'])->name('update-password');

    // Manajemen User - superadmin & admin
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::resource('/users', UserController::class)->except('show');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    // Sinkro DMS - superadmin only
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/sinkro', [SinkroController::class, 'index'])->name('sinkro.index');
        Route::post('/sinkro/instansi', [SinkroController::class, 'sinkroInstansi'])->name('sinkro.instansi');
        Route::post('/sinkro/arsip-pns', [SinkroController::class, 'sinkroArsipPns'])->name('sinkro.arsip-pns');
        Route::get('/sinkro/instansi/data', [SinkroController::class, 'instansiData'])->name('sinkro.instansi.data');
        Route::get('/sinkro/arsip-pns/data', [SinkroController::class, 'arsipPnsData'])->name('sinkro.arsip-pns.data');
    });

    // Pencatatan Takah
    Route::get('/takah/data', [TakahController::class, 'data'])->name('takah.data');
    Route::get('/takah/cari-asn', [TakahController::class, 'cariAsn'])->name('takah.cari-asn');
    Route::post('/takah/export-excel', [TakahController::class, 'exportExcel'])->name('takah.export-excel');
    Route::resource('/takah', TakahController::class)->except('show');

    // Scanning Dokumen
    Route::get('/scanning/{nip}', [ScanningController::class, 'show'])->name('scanning.show');
    Route::post('/scanning/{nip}', [ScanningController::class, 'store'])->name('scanning.store');
    Route::delete('/scanning/{scanning}', [ScanningController::class, 'destroy'])->name('scanning.destroy');

    // Daftar Sisi Arsip
    Route::get('/sisi-arsip', [SisiArsipController::class, 'index'])->name('sisi-arsip.index');
});

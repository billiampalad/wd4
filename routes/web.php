<?php

use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MitraController;
use App\Http\Controllers\Admin\JenisKerjasamaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardJurusanController;
use App\Http\Controllers\Jurusan\KerjasamaJurusanController;

/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| LOGIN USER
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

/*
|--------------------------------------------------------------------------
| LOGIN ADMIN
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminAuthController::class, 'loginForm']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| USER DASHBOARD (HARUS LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/pimpinan', [DashboardController::class, 'pimpinan'])->name('pimpinan.dashboard');
});

Route::middleware(['auth', 'role:jurusan'])->group(function () {
    Route::get('/jurusan', [DashboardJurusanController::class, 'index'])->name('jurusan.dashboard');

    // ─── Data Kerjasama CRUD ─────────────────────────────
    Route::get('/jurusan/data-kerjasama', [KerjasamaJurusanController::class, 'index'])->name('jurusan.dkerjasama');
    Route::get('/jurusan/data-kerjasama/create', [KerjasamaJurusanController::class, 'create'])->name('jurusan.kerjasama.create');
    Route::post('/jurusan/data-kerjasama', [KerjasamaJurusanController::class, 'store'])->name('jurusan.kerjasama.store');
    Route::get('/jurusan/data-kerjasama/{id}', [KerjasamaJurusanController::class, 'show'])->name('jurusan.kerjasama.show');
    Route::get('/jurusan/data-kerjasama/{id}/edit', [KerjasamaJurusanController::class, 'edit'])->name('jurusan.kerjasama.edit');
    Route::put('/jurusan/data-kerjasama/{id}', [KerjasamaJurusanController::class, 'update'])->name('jurusan.kerjasama.update');
    Route::delete('/jurusan/data-kerjasama/{id}', [KerjasamaJurusanController::class, 'destroy'])->name('jurusan.kerjasama.destroy');

    // ─── Sub-resource: Tujuan ────────────────────────────
    Route::post('/jurusan/data-kerjasama/{id}/tujuan', [KerjasamaJurusanController::class, 'storeTujuan'])->name('jurusan.kerjasama.tujuan.store');
    Route::put('/jurusan/data-kerjasama/{id}/tujuan/{tujuanId}', [KerjasamaJurusanController::class, 'updateTujuan'])->name('jurusan.kerjasama.tujuan.update');
    Route::delete('/jurusan/data-kerjasama/{id}/tujuan/{tujuanId}', [KerjasamaJurusanController::class, 'destroyTujuan'])->name('jurusan.kerjasama.tujuan.destroy');

    // ─── Sub-resource: Pelaksanaan ───────────────────────
    Route::post('/jurusan/data-kerjasama/{id}/pelaksanaan', [KerjasamaJurusanController::class, 'storePelaksanaan'])->name('jurusan.kerjasama.pelaksanaan.store');
    Route::put('/jurusan/data-kerjasama/{id}/pelaksanaan/{pelaksanaanId}', [KerjasamaJurusanController::class, 'updatePelaksanaan'])->name('jurusan.kerjasama.pelaksanaan.update');
    Route::delete('/jurusan/data-kerjasama/{id}/pelaksanaan/{pelaksanaanId}', [KerjasamaJurusanController::class, 'destroyPelaksanaan'])->name('jurusan.kerjasama.pelaksanaan.destroy');

    // ─── Sub-resource: Hasil ─────────────────────────────
    Route::post('/jurusan/data-kerjasama/{id}/hasil', [KerjasamaJurusanController::class, 'storeHasil'])->name('jurusan.kerjasama.hasil.store');
    Route::put('/jurusan/data-kerjasama/{id}/hasil/{hasilId}', [KerjasamaJurusanController::class, 'updateHasil'])->name('jurusan.kerjasama.hasil.update');
    Route::delete('/jurusan/data-kerjasama/{id}/hasil/{hasilId}', [KerjasamaJurusanController::class, 'destroyHasil'])->name('jurusan.kerjasama.hasil.destroy');

    // ─── Sub-resource: Dokumentasi ───────────────────────
    Route::post('/jurusan/data-kerjasama/{id}/dokumentasi', [KerjasamaJurusanController::class, 'storeDokumentasi'])->name('jurusan.kerjasama.dokumentasi.store');
    Route::put('/jurusan/data-kerjasama/{id}/dokumentasi/{dokId}', [KerjasamaJurusanController::class, 'updateDokumentasi'])->name('jurusan.kerjasama.dokumentasi.update');
    Route::delete('/jurusan/data-kerjasama/{id}/dokumentasi/{dokId}', [KerjasamaJurusanController::class, 'destroyDokumentasi'])->name('jurusan.kerjasama.dokumentasi.destroy');

    // ─── Sub-resource: Permasalahan & Solusi ──────────────
    Route::post('/jurusan/data-kerjasama/{id}/permasalahan', [KerjasamaJurusanController::class, 'storePermasalahan'])->name('jurusan.kerjasama.permasalahan.store');
    Route::put('/jurusan/data-kerjasama/{id}/permasalahan/{masalahId}', [KerjasamaJurusanController::class, 'updatePermasalahan'])->name('jurusan.kerjasama.permasalahan.update');
    Route::delete('/jurusan/data-kerjasama/{id}/permasalahan/{masalahId}', [KerjasamaJurusanController::class, 'destroyPermasalahan'])->name('jurusan.kerjasama.permasalahan.destroy');

    // ─── Submit to Pimpinan ──────────────────────────────
    Route::post('/jurusan/data-kerjasama/{id}/submit', [KerjasamaJurusanController::class, 'submitToPimpinan'])->name('jurusan.kerjasama.submit');

    // ─── Laporan Data ────────────────────────────────────
    Route::get('/jurusan/laporan', [App\Http\Controllers\Jurusan\LaporanJurusanController::class, 'index'])->name('jurusan.laporan');
    Route::get('/jurusan/laporan/preview', [App\Http\Controllers\Jurusan\LaporanJurusanController::class, 'preview'])->name('jurusan.laporan.preview');
    Route::get('/jurusan/laporan/excel', [App\Http\Controllers\Jurusan\LaporanJurusanController::class, 'exportExcel'])->name('jurusan.laporan.excel');
    Route::get('/jurusan/laporan/pdf', [App\Http\Controllers\Jurusan\LaporanJurusanController::class, 'exportPdf'])->name('jurusan.laporan.pdf');
});

Route::middleware(['auth', 'role:unit_kerja'])->group(function () {
    Route::get('/unit', [DashboardController::class, 'unit'])->name('unit.dashboard');
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'role:admin'])->name('admin.dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('mitra', MitraController::class);
    Route::resource('jkerjasama', JenisKerjasamaController::class);
    Route::get('/profiles', [DashboardController::class, 'profiles'])->name('admin.profiles');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route untuk user management
Route::get('/users', [UserController::class, 'index'])->name('users');

Route::get('/profiles', [DashboardController::class, 'profiles'])->name('profiles');

// Atau jika ingin route utama mengarah ke dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});
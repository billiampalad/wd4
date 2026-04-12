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
use App\Http\Controllers\Admin\UpelaksanaController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardJurusanController;
use App\Http\Controllers\Jurusan\KerjasamaJurusanController;
use App\Http\Controllers\Unit\KerjasamaUnitController;

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
| NOTIFIKASI API
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/api/notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index']);
    Route::post('/api/notifikasi/{id}/mark-read', [\App\Http\Controllers\NotifikasiController::class, 'markAsRead']);
    Route::post('/api/notifikasi/mark-all-read', [\App\Http\Controllers\NotifikasiController::class, 'markAllRead']);
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
    Route::get('/pimpinan/monitoring', [DashboardController::class, 'pimpinanMonitoring'])->name('pimpinan.monitoring');
    Route::get('/pimpinan/monitoring/{id}', [DashboardController::class, 'pimpinanMonitoringDetail'])->name('pimpinan.monitoring.detail');
    Route::get('/pimpinan/evaluasi', [DashboardController::class, 'pimpinanEvaluasi'])->name('pimpinan.evaluasi');
    Route::get('/pimpinan/evaluasi/{id}', [\App\Http\Controllers\Pimpinan\EvaluasiPimpinanController::class, 'show'])->name('pimpinan.evaluasi.show');
    Route::post('/pimpinan/evaluate/{id}', [\App\Http\Controllers\Pimpinan\EvaluasiPimpinanController::class, 'evaluate'])->name('pimpinan.evaluate');

    // ─── Laporan Global ────────────────────────────────────
    Route::get('/pimpinan/laporan', [\App\Http\Controllers\Pimpinan\LaporanPimpinanController::class, 'index'])->name('pimpinan.laporan');
    Route::get('/pimpinan/laporan/preview', [\App\Http\Controllers\Pimpinan\LaporanPimpinanController::class, 'preview'])->name('pimpinan.laporan.preview');
    Route::get('/pimpinan/laporan/pdf', [\App\Http\Controllers\Pimpinan\LaporanPimpinanController::class, 'exportPdf'])->name('pimpinan.laporan.pdf');
    Route::get('/pimpinan/laporan/excel', [\App\Http\Controllers\Pimpinan\LaporanPimpinanController::class, 'exportExcel'])->name('pimpinan.laporan.excel');
});

Route::middleware(['auth', 'role:jurusan'])->group(function () {
    Route::get('/jurusan', [DashboardJurusanController::class, 'index'])->name('jurusan.dashboard');

    // ─── Data Kerjasama CRUD ─────────────────────────────
    Route::get('/jurusan/data-kerjasama', [KerjasamaJurusanController::class, 'index'])->name('jurusan.dkerjasama');

    // ─── Hasil Evaluasi ─────────────────────────────────
    Route::get('/jurusan/hasil-evaluasi', [DashboardJurusanController::class, 'hasilEvaluasi'])->name('jurusan.hasil_evaluasi');
    Route::get('/jurusan/hasil-evaluasi/{id}', [DashboardJurusanController::class, 'formEvaluasi'])->name('jurusan.evaluasi.form');

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

    // ─── Data Kerjasama ──────────────────────────────────
    Route::get('/unit/data-kerjasama', [App\Http\Controllers\Unit\UnitPageController::class, 'dkerjasama'])->name('unit.dkerjasama');
    Route::get('/unit/data-kerjasama/create', [KerjasamaUnitController::class, 'create'])->name('unit.kerjasama.create');
    Route::post('/unit/data-kerjasama', [KerjasamaUnitController::class, 'store'])->name('unit.kerjasama.store');
    Route::get('/unit/data-kerjasama/{id}', [KerjasamaUnitController::class, 'show'])->name('unit.kerjasama.show');
    Route::get('/unit/data-kerjasama/{id}/edit', [KerjasamaUnitController::class, 'edit'])->name('unit.kerjasama.edit');
    Route::put('/unit/data-kerjasama/{id}', [KerjasamaUnitController::class, 'update'])->name('unit.kerjasama.update');
    Route::delete('/unit/data-kerjasama/{id}', [KerjasamaUnitController::class, 'destroy'])->name('unit.kerjasama.destroy');

    // ─── Sub-resource: Tujuan ────────────────────────────
    Route::post('/unit/data-kerjasama/{id}/tujuan', [KerjasamaUnitController::class, 'storeTujuan'])->name('unit.kerjasama.tujuan.store');
    Route::put('/unit/data-kerjasama/{id}/tujuan/{tujuanId}', [KerjasamaUnitController::class, 'updateTujuan'])->name('unit.kerjasama.tujuan.update');
    Route::delete('/unit/data-kerjasama/{id}/tujuan/{tujuanId}', [KerjasamaUnitController::class, 'destroyTujuan'])->name('unit.kerjasama.tujuan.destroy');

    // ─── Sub-resource: Pelaksanaan ───────────────────────
    Route::post('/unit/data-kerjasama/{id}/pelaksanaan', [KerjasamaUnitController::class, 'storePelaksanaan'])->name('unit.kerjasama.pelaksanaan.store');
    Route::delete('/unit/data-kerjasama/{id}/pelaksanaan/{pelaksanaanId}', [KerjasamaUnitController::class, 'destroyPelaksanaan'])->name('unit.kerjasama.pelaksanaan.destroy');

    // ─── Sub-resource: Hasil ─────────────────────────────
    Route::post('/unit/data-kerjasama/{id}/hasil', [KerjasamaUnitController::class, 'storeHasil'])->name('unit.kerjasama.hasil.store');
    Route::delete('/unit/data-kerjasama/{id}/hasil/{hasilId}', [KerjasamaUnitController::class, 'destroyHasil'])->name('unit.kerjasama.hasil.destroy');

    // ─── Sub-resource: Dokumentasi ───────────────────────
    Route::post('/unit/data-kerjasama/{id}/dokumentasi', [KerjasamaUnitController::class, 'storeDokumentasi'])->name('unit.kerjasama.dokumentasi.store');
    Route::delete('/unit/data-kerjasama/{id}/dokumentasi/{dokId}', [KerjasamaUnitController::class, 'destroyDokumentasi'])->name('unit.kerjasama.dokumentasi.destroy');

    // ─── Sub-resource: Permasalahan & Solusi ──────────────
    Route::post('/unit/data-kerjasama/{id}/permasalahan', [KerjasamaUnitController::class, 'storePermasalahan'])->name('unit.kerjasama.permasalahan.store');
    Route::delete('/unit/data-kerjasama/{id}/permasalahan/{masalahId}', [KerjasamaUnitController::class, 'destroyPermasalahan'])->name('unit.kerjasama.permasalahan.destroy');

    // ─── Submit to Pimpinan ──────────────────────────────
    Route::post('/unit/data-kerjasama/{id}/submit', [KerjasamaUnitController::class, 'submitToPimpinan'])->name('unit.kerjasama.submit');

    // ─── Evaluasi Kinerja ────────────────────────────────
    Route::get('/unit/evaluasi', [App\Http\Controllers\Unit\UnitPageController::class, 'evaluasi'])->name('unit.evaluasi');
    Route::get('/unit/evaluasi/{id}', [App\Http\Controllers\Unit\UnitPageController::class, 'formEvaluasi'])->name('unit.evaluasi.form');
    Route::post('/unit/evaluasi/{id}', [App\Http\Controllers\Unit\UnitPageController::class, 'storeEvaluasi'])->name('unit.evaluasi.store');
    Route::put('/unit/evaluasi/{id}', [App\Http\Controllers\Unit\UnitPageController::class, 'updateEvaluasi'])->name('unit.evaluasi.update');
    Route::post('/unit/evaluasi/{id}/submit', [App\Http\Controllers\Unit\UnitPageController::class, 'submitEvaluasiToPimpinan'])->name('unit.evaluasi.submit');

    // ─── Laporan Data ────────────────────────────────────
    Route::get('/unit/laporan', [App\Http\Controllers\Unit\UnitPageController::class, 'laporan'])->name('unit.laporan');
    Route::get('/unit/laporan/preview', [App\Http\Controllers\Unit\UnitPageController::class, 'laporanPreview'])->name('unit.laporan.preview');
    Route::get('/unit/laporan/pdf', [App\Http\Controllers\Unit\UnitPageController::class, 'laporanPdf'])->name('unit.laporan.pdf');
    Route::get('/unit/laporan/excel', [App\Http\Controllers\Unit\UnitPageController::class, 'laporanExcel'])->name('unit.laporan.excel');
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
    Route::resource('upelaksana', UpelaksanaController::class);
    Route::resource('jurusan', JurusanController::class);
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
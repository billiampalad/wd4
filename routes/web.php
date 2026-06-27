<?php

use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PublicLandingController;
use App\Http\Controllers\PublicPengajuanKerjasamaController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MitraController;
use App\Http\Controllers\Admin\JenisKerjasamaController;
use App\Http\Controllers\Admin\UpelaksanaController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\ProdiController;
use App\Http\Controllers\Admin\KlasifikasiController;
use App\Http\Controllers\Admin\UpaController;
use App\Http\Controllers\Admin\PusatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardJurusanController;
use App\Http\Controllers\Jurusan\JurusanPageController;
use App\Http\Controllers\Jurusan\KerjasamaJurusanController;
use App\Http\Controllers\Pimpinan\PengajuanKerjasamaMitraController;
use App\Http\Controllers\Unit\KerjasamaUnitController;
use App\Http\Controllers\Upa\UpaPageController;
use App\Http\Controllers\Upa\KerjasamaUpaController;
use App\Http\Controllers\Pusat\PusatPageController;
use App\Http\Controllers\Pusat\KerjasamaPusatController;

/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicLandingController::class, 'index']);
Route::get('/pengajuan-kerjasama', [PublicPengajuanKerjasamaController::class, 'create'])->name('pengajuan.kerjasama.create');
Route::post('/pengajuan-kerjasama', [PublicPengajuanKerjasamaController::class, 'store'])->name('pengajuan.kerjasama.store');
Route::get('/perpanjangan-kerjasama', [PublicPengajuanKerjasamaController::class, 'createPerpanjangan'])->name('pengajuan.perpanjangan.create');
Route::post('/perpanjangan-kerjasama', [PublicPengajuanKerjasamaController::class, 'storePerpanjangan'])->name('pengajuan.perpanjangan.store');

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

Route::middleware('guest')->group(function () {
    Route::get('/lupa-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'request'])
        ->name('password.request');
    Route::post('/lupa-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'email'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])
        ->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'update'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

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
Route::post('/session/heartbeat', [LoginController::class, 'heartbeat'])
    ->middleware('auth')
    ->name('session.heartbeat');

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
    Route::get('/pimpinan/pengajuan-mitra', [PengajuanKerjasamaMitraController::class, 'index'])->name('pimpinan.pengajuan_mitra');
    Route::post('/pimpinan/pengajuan-mitra/{id}/review', [PengajuanKerjasamaMitraController::class, 'review'])->name('pimpinan.pengajuan_mitra.review');
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

    Route::get('/jurusan/analitik/status-kerjasama', [JurusanPageController::class, 'statusKerjasama'])->name('jurusan.analitik.status-kerjasama');
    Route::get('/jurusan/analitik/klasifikasi-mitra', [JurusanPageController::class, 'klasifikasiMitra'])->name('jurusan.analitik.klasifikasi-mitra');
    Route::get('/jurusan/analitik/geo-mitra', [JurusanPageController::class, 'geoMitra'])->name('jurusan.analitik.geo-mitra');
    Route::get('/jurusan/institusi', [JurusanPageController::class, 'institusi'])->name('jurusan.institusi');
    Route::get('/jurusan/referensi/bentuk-kegiatan', [JurusanPageController::class, 'bentukKegiatan'])->name('jurusan.referensi.bentuk-kegiatan');
    Route::get('/jurusan/referensi/status-kerjasama', [JurusanPageController::class, 'statusKerjasamaReferensi'])->name('jurusan.referensi.status-kerjasama');
    Route::get('/jurusan/referensi/status-evaluasi', [JurusanPageController::class, 'statusEvaluasiReferensi'])->name('jurusan.referensi.status-evaluasi');
    Route::get('/jurusan/referensi/kriteria-mitra', [JurusanPageController::class, 'kriteriaMitraReferensi'])->name('jurusan.referensi.kriteria-mitra');

    // ─── Data Kerjasama CRUD ─────────────────────────────
    Route::get('/jurusan/data-kerjasama', [JurusanPageController::class, 'dkerjasama'])->name('jurusan.dkerjasama');
    Route::get('/jurusan/data-kerjasama/preview', [JurusanPageController::class, 'dkerjasamaPreview'])->name('jurusan.dkerjasama.preview');
    Route::get('/jurusan/data-kerjasama/pdf', [JurusanPageController::class, 'dkerjasamaPdf'])->name('jurusan.dkerjasama.pdf');
    Route::get('/jurusan/data-kerjasama/excel', [JurusanPageController::class, 'dkerjasamaExcel'])->name('jurusan.dkerjasama.excel');

    // ─── Hasil Evaluasi ─────────────────────────────────
    Route::get('/jurusan/hasil-evaluasi', [DashboardJurusanController::class, 'hasilEvaluasi'])->name('jurusan.hasil_evaluasi');
    Route::get('/jurusan/hasil-evaluasi/{id}', [DashboardJurusanController::class, 'formEvaluasi'])->name('jurusan.evaluasi.form');

    Route::get('/jurusan/data-kerjasama/create', [KerjasamaJurusanController::class, 'create'])->name('jurusan.kerjasama.create');
    Route::post('/jurusan/data-kerjasama', [KerjasamaJurusanController::class, 'store'])->name('jurusan.kerjasama.store');
    Route::get('/jurusan/data-kerjasama/{id}', [KerjasamaJurusanController::class, 'show'])->name('jurusan.kerjasama.show');
    Route::get('/jurusan/data-kerjasama/{id}/edit', [KerjasamaJurusanController::class, 'edit'])->name('jurusan.kerjasama.edit');
    Route::put('/jurusan/data-kerjasama/{id}', [KerjasamaJurusanController::class, 'update'])->name('jurusan.kerjasama.update');
    Route::post('/jurusan/data-kerjasama/{id}/document-link', [KerjasamaJurusanController::class, 'updateDocumentLink'])->name('jurusan.kerjasama.document-link.update');
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

    Route::get('/jurusan/mitra', [JurusanPageController::class, 'mitra'])->name('jurusan.mitra');
    Route::get('/jurusan/mitra/create', [JurusanPageController::class, 'mitraCreate'])->name('jurusan.mitra.create');
    Route::post('/jurusan/mitra', [JurusanPageController::class, 'mitraStore'])->name('jurusan.mitra.store');
    Route::get('/jurusan/mitra/{id}', [JurusanPageController::class, 'mitraShow'])->name('jurusan.mitra.show');
    Route::get('/jurusan/mitra/{id}/edit', [JurusanPageController::class, 'mitraEdit'])->name('jurusan.mitra.edit');
    Route::put('/jurusan/mitra/{id}', [JurusanPageController::class, 'mitraUpdate'])->name('jurusan.mitra.update');
    Route::delete('/jurusan/mitra/{id}', [JurusanPageController::class, 'mitraDestroy'])->name('jurusan.mitra.destroy');

    Route::get('/jurusan/evaluasi', [JurusanPageController::class, 'evaluasi'])->name('jurusan.evaluasi');
    Route::get('/jurusan/evaluasi/{id}', [JurusanPageController::class, 'formEvaluasi'])->name('jurusan.evaluasi.form_unit');
    Route::post('/jurusan/evaluasi/{id}', [JurusanPageController::class, 'storeEvaluasi'])->name('jurusan.evaluasi.store');
    Route::put('/jurusan/evaluasi/{id}', [JurusanPageController::class, 'updateEvaluasi'])->name('jurusan.evaluasi.update');
    Route::post('/jurusan/evaluasi/{id}/submit', [JurusanPageController::class, 'submitEvaluasiToPimpinan'])->name('jurusan.evaluasi.submit');

    Route::get('/jurusan/form-laporan', [JurusanPageController::class, 'formLaporan'])->name('jurusan.form');
    Route::post('/jurusan/form-laporan', [JurusanPageController::class, 'formLaporanStore'])->name('jurusan.form.store');
    Route::delete('/jurusan/form-laporan/{id}', [JurusanPageController::class, 'formLaporanDestroy'])->name('jurusan.form.destroy');

    // ─── Laporan Data ────────────────────────────────────
    Route::get('/jurusan/laporan', [JurusanPageController::class, 'laporan'])->name('jurusan.laporan');
    Route::get('/jurusan/laporan/preview', [JurusanPageController::class, 'laporanPreview'])->name('jurusan.laporan.preview');
    Route::get('/jurusan/laporan/excel', [JurusanPageController::class, 'laporanExcel'])->name('jurusan.laporan.excel');
    Route::get('/jurusan/laporan/pdf', [JurusanPageController::class, 'laporanPdf'])->name('jurusan.laporan.pdf');
});

Route::middleware(['auth', 'role:unit_kerja'])->group(function () {
    Route::get('/unit', [DashboardController::class, 'unit'])->name('unit.dashboard');

    // analitik
    Route::get('/unit/analitik/status-kerjasama', [App\Http\Controllers\Unit\UnitPageController::class, 'statusKerjasama'])->name('unit.analitik.status-kerjasama');
    Route::get('/unit/analitik/klasifikasi-mitra', [App\Http\Controllers\Unit\UnitPageController::class, 'klasifikasiMitra'])->name('unit.analitik.klasifikasi-mitra');
    Route::get('/unit/analitik/geo-mitra', [App\Http\Controllers\Unit\UnitPageController::class, 'geoMitra'])->name('unit.analitik.geo-mitra');

    // institusi
    Route::get('/unit/institusi', [App\Http\Controllers\Unit\UnitPageController::class, 'institusi'])->name('unit.institusi');

    // referensi
    Route::get('/unit/referensi/bentuk-kegiatan', [App\Http\Controllers\Unit\UnitPageController::class, 'bentukKegiatan'])->name('unit.referensi.bentuk-kegiatan');
    Route::get('/unit/referensi/status-kerjasama', [App\Http\Controllers\Unit\UnitPageController::class, 'statusKerjasamaReferensi'])->name('unit.referensi.status-kerjasama');
    Route::get('/unit/referensi/status-evaluasi', [App\Http\Controllers\Unit\UnitPageController::class, 'statusEvaluasiReferensi'])->name('unit.referensi.status-evaluasi');
    Route::get('/unit/referensi/kriteria-mitra', [App\Http\Controllers\Unit\UnitPageController::class, 'kriteriaMitraReferensi'])->name('unit.referensi.kriteria-mitra');

    // ─── Data Kerjasama ──────────────────────────────────
    Route::get('/unit/data-kerjasama', [App\Http\Controllers\Unit\UnitPageController::class, 'dkerjasama'])->name('unit.dkerjasama');
    Route::get('/unit/data-kerjasama/preview', [App\Http\Controllers\Unit\UnitPageController::class, 'dkerjasamaPreview'])->name('unit.dkerjasama.preview');
    Route::get('/unit/data-kerjasama/pdf', [App\Http\Controllers\Unit\UnitPageController::class, 'dkerjasamaPdf'])->name('unit.dkerjasama.pdf');
    Route::get('/unit/data-kerjasama/excel', [App\Http\Controllers\Unit\UnitPageController::class, 'dkerjasamaExcel'])->name('unit.dkerjasama.excel');

    // ─── Mitra Unit CRUD ─────────────────────────────────
    Route::get('/unit/mitra', [App\Http\Controllers\Unit\UnitPageController::class, 'mitra'])->name('unit.mitra');
    Route::get('/unit/mitra/create', [App\Http\Controllers\Unit\UnitPageController::class, 'mitraCreate'])->name('unit.mitra.create');
    Route::post('/unit/mitra', [App\Http\Controllers\Unit\UnitPageController::class, 'mitraStore'])->name('unit.mitra.store');
    Route::get('/unit/mitra/{id}', [App\Http\Controllers\Unit\UnitPageController::class, 'mitraShow'])->name('unit.mitra.show');
    Route::get('/unit/mitra/{id}/edit', [App\Http\Controllers\Unit\UnitPageController::class, 'mitraEdit'])->name('unit.mitra.edit');
    Route::put('/unit/mitra/{id}', [App\Http\Controllers\Unit\UnitPageController::class, 'mitraUpdate'])->name('unit.mitra.update');
    Route::delete('/unit/mitra/{id}', [App\Http\Controllers\Unit\UnitPageController::class, 'mitraDestroy'])->name('unit.mitra.destroy');

    Route::get('/unit/data-kerjasama/create', [KerjasamaUnitController::class, 'create'])->name('unit.kerjasama.create');
    Route::post('/unit/data-kerjasama', [KerjasamaUnitController::class, 'store'])->name('unit.kerjasama.store');
    Route::get('/unit/data-kerjasama/{id}', [KerjasamaUnitController::class, 'show'])->name('unit.kerjasama.show');
    Route::get('/unit/data-kerjasama/{id}/edit', [KerjasamaUnitController::class, 'edit'])->name('unit.kerjasama.edit');
    Route::put('/unit/data-kerjasama/{id}', [KerjasamaUnitController::class, 'update'])->name('unit.kerjasama.update');
    Route::post('/unit/data-kerjasama/{id}/document-link', [KerjasamaUnitController::class, 'updateDocumentLink'])->name('unit.kerjasama.document-link.update');
    Route::delete('/unit/data-kerjasama/{id}', [KerjasamaUnitController::class, 'destroy'])->name('unit.kerjasama.destroy');

    // ─── Submit to Pimpinan ──────────────────────────────
    Route::post('/unit/data-kerjasama/{id}/submit', [KerjasamaUnitController::class, 'submitToPimpinan'])->name('unit.kerjasama.submit');

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

    // ─── Form Laporan (PDF/Word Upload) ───────────────────
    Route::get('/unit/form-laporan', [App\Http\Controllers\Unit\UnitPageController::class, 'formLaporan'])->name('unit.form');
    Route::post('/unit/form-laporan', [App\Http\Controllers\Unit\UnitPageController::class, 'formLaporanStore'])->name('unit.form.store');
    Route::delete('/unit/form-laporan/{id}', [App\Http\Controllers\Unit\UnitPageController::class, 'formLaporanDestroy'])->name('unit.form.destroy');
});

Route::middleware(['auth', 'role:upa'])->group(function () {
    Route::get('/upa', [DashboardController::class, 'upa'])->name('upa.dashboard');

    Route::get('/upa/analitik/status-kerjasama', [UpaPageController::class, 'statusKerjasama'])->name('upa.analitik.status-kerjasama');
    Route::get('/upa/analitik/klasifikasi-mitra', [UpaPageController::class, 'klasifikasiMitra'])->name('upa.analitik.klasifikasi-mitra');
    Route::get('/upa/analitik/geo-mitra', [UpaPageController::class, 'geoMitra'])->name('upa.analitik.geo-mitra');
    Route::get('/upa/institusi', [UpaPageController::class, 'institusi'])->name('upa.institusi');
    Route::get('/upa/referensi/bentuk-kegiatan', [UpaPageController::class, 'bentukKegiatan'])->name('upa.referensi.bentuk-kegiatan');
    Route::get('/upa/referensi/status-kerjasama', [UpaPageController::class, 'statusKerjasamaReferensi'])->name('upa.referensi.status-kerjasama');
    Route::get('/upa/referensi/status-evaluasi', [UpaPageController::class, 'statusEvaluasiReferensi'])->name('upa.referensi.status-evaluasi');
    Route::get('/upa/referensi/kriteria-mitra', [UpaPageController::class, 'kriteriaMitraReferensi'])->name('upa.referensi.kriteria-mitra');

    Route::get('/upa/data-kerjasama', [UpaPageController::class, 'dkerjasama'])->name('upa.dkerjasama');
    Route::get('/upa/data-kerjasama/preview', [UpaPageController::class, 'dkerjasamaPreview'])->name('upa.dkerjasama.preview');
    Route::get('/upa/data-kerjasama/pdf', [UpaPageController::class, 'dkerjasamaPdf'])->name('upa.dkerjasama.pdf');
    Route::get('/upa/data-kerjasama/excel', [UpaPageController::class, 'dkerjasamaExcel'])->name('upa.dkerjasama.excel');

    Route::get('/upa/mitra', [UpaPageController::class, 'mitra'])->name('upa.mitra');
    Route::get('/upa/mitra/create', [UpaPageController::class, 'mitraCreate'])->name('upa.mitra.create');
    Route::post('/upa/mitra', [UpaPageController::class, 'mitraStore'])->name('upa.mitra.store');
    Route::get('/upa/mitra/{id}', [UpaPageController::class, 'mitraShow'])->name('upa.mitra.show');
    Route::get('/upa/mitra/{id}/edit', [UpaPageController::class, 'mitraEdit'])->name('upa.mitra.edit');
    Route::put('/upa/mitra/{id}', [UpaPageController::class, 'mitraUpdate'])->name('upa.mitra.update');
    Route::delete('/upa/mitra/{id}', [UpaPageController::class, 'mitraDestroy'])->name('upa.mitra.destroy');

    Route::get('/upa/data-kerjasama/create', [KerjasamaUpaController::class, 'create'])->name('upa.kerjasama.create');
    Route::post('/upa/data-kerjasama', [KerjasamaUpaController::class, 'store'])->name('upa.kerjasama.store');
    Route::get('/upa/data-kerjasama/{id}', [KerjasamaUpaController::class, 'show'])->name('upa.kerjasama.show');
    Route::get('/upa/data-kerjasama/{id}/edit', [KerjasamaUpaController::class, 'edit'])->name('upa.kerjasama.edit');
    Route::put('/upa/data-kerjasama/{id}', [KerjasamaUpaController::class, 'update'])->name('upa.kerjasama.update');
    Route::post('/upa/data-kerjasama/{id}/document-link', [KerjasamaUpaController::class, 'updateDocumentLink'])->name('upa.kerjasama.document-link.update');
    Route::delete('/upa/data-kerjasama/{id}', [KerjasamaUpaController::class, 'destroy'])->name('upa.kerjasama.destroy');

    Route::post('/upa/data-kerjasama/{id}/tujuan', [KerjasamaUpaController::class, 'storeTujuan'])->name('upa.kerjasama.tujuan.store');
    Route::put('/upa/data-kerjasama/{id}/tujuan/{tujuanId}', [KerjasamaUpaController::class, 'updateTujuan'])->name('upa.kerjasama.tujuan.update');
    Route::delete('/upa/data-kerjasama/{id}/tujuan/{tujuanId}', [KerjasamaUpaController::class, 'destroyTujuan'])->name('upa.kerjasama.tujuan.destroy');
    Route::post('/upa/data-kerjasama/{id}/pelaksanaan', [KerjasamaUpaController::class, 'storePelaksanaan'])->name('upa.kerjasama.pelaksanaan.store');
    Route::put('/upa/data-kerjasama/{id}/pelaksanaan/{pelaksanaanId}', [KerjasamaUpaController::class, 'updatePelaksanaan'])->name('upa.kerjasama.pelaksanaan.update');
    Route::delete('/upa/data-kerjasama/{id}/pelaksanaan/{pelaksanaanId}', [KerjasamaUpaController::class, 'destroyPelaksanaan'])->name('upa.kerjasama.pelaksanaan.destroy');
    Route::post('/upa/data-kerjasama/{id}/hasil', [KerjasamaUpaController::class, 'storeHasil'])->name('upa.kerjasama.hasil.store');
    Route::put('/upa/data-kerjasama/{id}/hasil/{hasilId}', [KerjasamaUpaController::class, 'updateHasil'])->name('upa.kerjasama.hasil.update');
    Route::delete('/upa/data-kerjasama/{id}/hasil/{hasilId}', [KerjasamaUpaController::class, 'destroyHasil'])->name('upa.kerjasama.hasil.destroy');
    Route::post('/upa/data-kerjasama/{id}/dokumentasi', [KerjasamaUpaController::class, 'storeDokumentasi'])->name('upa.kerjasama.dokumentasi.store');
    Route::put('/upa/data-kerjasama/{id}/dokumentasi/{dokId}', [KerjasamaUpaController::class, 'updateDokumentasi'])->name('upa.kerjasama.dokumentasi.update');
    Route::delete('/upa/data-kerjasama/{id}/dokumentasi/{dokId}', [KerjasamaUpaController::class, 'destroyDokumentasi'])->name('upa.kerjasama.dokumentasi.destroy');
    Route::post('/upa/data-kerjasama/{id}/permasalahan', [KerjasamaUpaController::class, 'storePermasalahan'])->name('upa.kerjasama.permasalahan.store');
    Route::put('/upa/data-kerjasama/{id}/permasalahan/{masalahId}', [KerjasamaUpaController::class, 'updatePermasalahan'])->name('upa.kerjasama.permasalahan.update');
    Route::delete('/upa/data-kerjasama/{id}/permasalahan/{masalahId}', [KerjasamaUpaController::class, 'destroyPermasalahan'])->name('upa.kerjasama.permasalahan.destroy');
    Route::post('/upa/data-kerjasama/{id}/submit', [KerjasamaUpaController::class, 'submitToPimpinan'])->name('upa.kerjasama.submit');

    Route::get('/upa/evaluasi', [UpaPageController::class, 'evaluasi'])->name('upa.evaluasi');
    Route::get('/upa/hasil-evaluasi', [UpaPageController::class, 'evaluasi'])->name('upa.hasil_evaluasi');
    Route::get('/upa/hasil-evaluasi/{id}', [UpaPageController::class, 'formEvaluasi'])->name('upa.evaluasi.form');
    Route::get('/upa/evaluasi/{id}', [UpaPageController::class, 'formEvaluasi'])->name('upa.evaluasi.form_unit');
    Route::post('/upa/evaluasi/{id}', [UpaPageController::class, 'storeEvaluasi'])->name('upa.evaluasi.store');
    Route::put('/upa/evaluasi/{id}', [UpaPageController::class, 'updateEvaluasi'])->name('upa.evaluasi.update');
    Route::post('/upa/evaluasi/{id}/submit', [UpaPageController::class, 'submitEvaluasiToPimpinan'])->name('upa.evaluasi.submit');

    Route::get('/upa/laporan', [UpaPageController::class, 'laporan'])->name('upa.laporan');
    Route::get('/upa/laporan/preview', [UpaPageController::class, 'laporanPreview'])->name('upa.laporan.preview');
    Route::get('/upa/laporan/pdf', [UpaPageController::class, 'laporanPdf'])->name('upa.laporan.pdf');
    Route::get('/upa/laporan/excel', [UpaPageController::class, 'laporanExcel'])->name('upa.laporan.excel');

    Route::get('/upa/form-laporan', [UpaPageController::class, 'formLaporan'])->name('upa.form');
    Route::post('/upa/form-laporan', [UpaPageController::class, 'formLaporanStore'])->name('upa.form.store');
    Route::delete('/upa/form-laporan/{id}', [UpaPageController::class, 'formLaporanDestroy'])->name('upa.form.destroy');
});

Route::middleware(['auth', 'role:pusat'])->group(function () {
    Route::get('/pusat', [DashboardController::class, 'pusat'])->name('pusat.dashboard');

    Route::get('/pusat/analitik/status-kerjasama', [PusatPageController::class, 'statusKerjasama'])->name('pusat.analitik.status-kerjasama');
    Route::get('/pusat/analitik/klasifikasi-mitra', [PusatPageController::class, 'klasifikasiMitra'])->name('pusat.analitik.klasifikasi-mitra');
    Route::get('/pusat/analitik/geo-mitra', [PusatPageController::class, 'geoMitra'])->name('pusat.analitik.geo-mitra');
    Route::get('/pusat/institusi', [PusatPageController::class, 'institusi'])->name('pusat.institusi');
    Route::get('/pusat/referensi/bentuk-kegiatan', [PusatPageController::class, 'bentukKegiatan'])->name('pusat.referensi.bentuk-kegiatan');
    Route::get('/pusat/referensi/status-kerjasama', [PusatPageController::class, 'statusKerjasamaReferensi'])->name('pusat.referensi.status-kerjasama');
    Route::get('/pusat/referensi/status-evaluasi', [PusatPageController::class, 'statusEvaluasiReferensi'])->name('pusat.referensi.status-evaluasi');
    Route::get('/pusat/referensi/kriteria-mitra', [PusatPageController::class, 'kriteriaMitraReferensi'])->name('pusat.referensi.kriteria-mitra');

    Route::get('/pusat/data-kerjasama', [PusatPageController::class, 'dkerjasama'])->name('pusat.dkerjasama');
    Route::get('/pusat/data-kerjasama/preview', [PusatPageController::class, 'dkerjasamaPreview'])->name('pusat.dkerjasama.preview');
    Route::get('/pusat/data-kerjasama/pdf', [PusatPageController::class, 'dkerjasamaPdf'])->name('pusat.dkerjasama.pdf');
    Route::get('/pusat/data-kerjasama/excel', [PusatPageController::class, 'dkerjasamaExcel'])->name('pusat.dkerjasama.excel');

    Route::get('/pusat/mitra', [PusatPageController::class, 'mitra'])->name('pusat.mitra');
    Route::get('/pusat/mitra/create', [PusatPageController::class, 'mitraCreate'])->name('pusat.mitra.create');
    Route::post('/pusat/mitra', [PusatPageController::class, 'mitraStore'])->name('pusat.mitra.store');
    Route::get('/pusat/mitra/{id}', [PusatPageController::class, 'mitraShow'])->name('pusat.mitra.show');
    Route::get('/pusat/mitra/{id}/edit', [PusatPageController::class, 'mitraEdit'])->name('pusat.mitra.edit');
    Route::put('/pusat/mitra/{id}', [PusatPageController::class, 'mitraUpdate'])->name('pusat.mitra.update');
    Route::delete('/pusat/mitra/{id}', [PusatPageController::class, 'mitraDestroy'])->name('pusat.mitra.destroy');

    Route::get('/pusat/data-kerjasama/create', [KerjasamaPusatController::class, 'create'])->name('pusat.kerjasama.create');
    Route::post('/pusat/data-kerjasama', [KerjasamaPusatController::class, 'store'])->name('pusat.kerjasama.store');
    Route::get('/pusat/data-kerjasama/{id}', [KerjasamaPusatController::class, 'show'])->name('pusat.kerjasama.show');
    Route::get('/pusat/data-kerjasama/{id}/edit', [KerjasamaPusatController::class, 'edit'])->name('pusat.kerjasama.edit');
    Route::put('/pusat/data-kerjasama/{id}', [KerjasamaPusatController::class, 'update'])->name('pusat.kerjasama.update');
    Route::post('/pusat/data-kerjasama/{id}/document-link', [KerjasamaPusatController::class, 'updateDocumentLink'])->name('pusat.kerjasama.document-link.update');
    Route::delete('/pusat/data-kerjasama/{id}', [KerjasamaPusatController::class, 'destroy'])->name('pusat.kerjasama.destroy');

    Route::post('/pusat/data-kerjasama/{id}/tujuan', [KerjasamaPusatController::class, 'storeTujuan'])->name('pusat.kerjasama.tujuan.store');
    Route::put('/pusat/data-kerjasama/{id}/tujuan/{tujuanId}', [KerjasamaPusatController::class, 'updateTujuan'])->name('pusat.kerjasama.tujuan.update');
    Route::delete('/pusat/data-kerjasama/{id}/tujuan/{tujuanId}', [KerjasamaPusatController::class, 'destroyTujuan'])->name('pusat.kerjasama.tujuan.destroy');
    Route::post('/pusat/data-kerjasama/{id}/pelaksanaan', [KerjasamaPusatController::class, 'storePelaksanaan'])->name('pusat.kerjasama.pelaksanaan.store');
    Route::put('/pusat/data-kerjasama/{id}/pelaksanaan/{pelaksanaanId}', [KerjasamaPusatController::class, 'updatePelaksanaan'])->name('pusat.kerjasama.pelaksanaan.update');
    Route::delete('/pusat/data-kerjasama/{id}/pelaksanaan/{pelaksanaanId}', [KerjasamaPusatController::class, 'destroyPelaksanaan'])->name('pusat.kerjasama.pelaksanaan.destroy');
    Route::post('/pusat/data-kerjasama/{id}/hasil', [KerjasamaPusatController::class, 'storeHasil'])->name('pusat.kerjasama.hasil.store');
    Route::put('/pusat/data-kerjasama/{id}/hasil/{hasilId}', [KerjasamaPusatController::class, 'updateHasil'])->name('pusat.kerjasama.hasil.update');
    Route::delete('/pusat/data-kerjasama/{id}/hasil/{hasilId}', [KerjasamaPusatController::class, 'destroyHasil'])->name('pusat.kerjasama.hasil.destroy');
    Route::post('/pusat/data-kerjasama/{id}/dokumentasi', [KerjasamaPusatController::class, 'storeDokumentasi'])->name('pusat.kerjasama.dokumentasi.store');
    Route::put('/pusat/data-kerjasama/{id}/dokumentasi/{dokId}', [KerjasamaPusatController::class, 'updateDokumentasi'])->name('pusat.kerjasama.dokumentasi.update');
    Route::delete('/pusat/data-kerjasama/{id}/dokumentasi/{dokId}', [KerjasamaPusatController::class, 'destroyDokumentasi'])->name('pusat.kerjasama.dokumentasi.destroy');
    Route::post('/pusat/data-kerjasama/{id}/permasalahan', [KerjasamaPusatController::class, 'storePermasalahan'])->name('pusat.kerjasama.permasalahan.store');
    Route::put('/pusat/data-kerjasama/{id}/permasalahan/{masalahId}', [KerjasamaPusatController::class, 'updatePermasalahan'])->name('pusat.kerjasama.permasalahan.update');
    Route::delete('/pusat/data-kerjasama/{id}/permasalahan/{masalahId}', [KerjasamaPusatController::class, 'destroyPermasalahan'])->name('pusat.kerjasama.permasalahan.destroy');
    Route::post('/pusat/data-kerjasama/{id}/submit', [KerjasamaPusatController::class, 'submitToPimpinan'])->name('pusat.kerjasama.submit');

    Route::get('/pusat/evaluasi', [PusatPageController::class, 'evaluasi'])->name('pusat.evaluasi');
    Route::get('/pusat/hasil-evaluasi', [PusatPageController::class, 'evaluasi'])->name('pusat.hasil_evaluasi');
    Route::get('/pusat/hasil-evaluasi/{id}', [PusatPageController::class, 'formEvaluasi'])->name('pusat.evaluasi.form');
    Route::get('/pusat/evaluasi/{id}', [PusatPageController::class, 'formEvaluasi'])->name('pusat.evaluasi.form_unit');
    Route::post('/pusat/evaluasi/{id}', [PusatPageController::class, 'storeEvaluasi'])->name('pusat.evaluasi.store');
    Route::put('/pusat/evaluasi/{id}', [PusatPageController::class, 'updateEvaluasi'])->name('pusat.evaluasi.update');
    Route::post('/pusat/evaluasi/{id}/submit', [PusatPageController::class, 'submitEvaluasiToPimpinan'])->name('pusat.evaluasi.submit');

    Route::get('/pusat/laporan', [PusatPageController::class, 'laporan'])->name('pusat.laporan');
    Route::get('/pusat/laporan/preview', [PusatPageController::class, 'laporanPreview'])->name('pusat.laporan.preview');
    Route::get('/pusat/laporan/pdf', [PusatPageController::class, 'laporanPdf'])->name('pusat.laporan.pdf');
    Route::get('/pusat/laporan/excel', [PusatPageController::class, 'laporanExcel'])->name('pusat.laporan.excel');

    Route::get('/pusat/form-laporan', [PusatPageController::class, 'formLaporan'])->name('pusat.form');
    Route::post('/pusat/form-laporan', [PusatPageController::class, 'formLaporanStore'])->name('pusat.form.store');
    Route::delete('/pusat/form-laporan/{id}', [PusatPageController::class, 'formLaporanDestroy'])->name('pusat.form.destroy');
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
    Route::resource('prodi', ProdiController::class);
    Route::resource('klasifikasi', KlasifikasiController::class);
    Route::resource('upa', UpaController::class);
    Route::resource('pusat', PusatController::class);
    Route::get('/profiles', [DashboardController::class, 'profiles'])->name('admin.profiles');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route untuk user management
Route::get('/users', [UserController::class, 'index'])->name('users');

Route::get('/profiles', [DashboardController::class, 'profiles'])->name('profiles');

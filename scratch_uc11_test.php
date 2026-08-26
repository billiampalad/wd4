<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;
use App\Models\Profile;
use App\Models\Mitra;
use App\Models\Jurusan;
use App\Models\UnitKerja;
use App\Models\Cooperation;
use App\Http\Controllers\Pimpinan\EvaluasiPimpinanController;

echo "====================================================\n";
echo "  PENGUJIAN UC11: EVALUASI PIMPINAN\n";
echo "====================================================\n\n";

DB::beginTransaction();
try {
    $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Pengujian UC11'], ['status_akses' => 'Aktif', 'negara' => 'Indonesia', 'kategori' => 'nasional']);

    // Pastikan role pengusul ada
    $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
    $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
    
    // Pastikan pimpinan role
    $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
    $userPimpinan = User::create([
        'name' => 'Bapak Pimpinan', 
        'email' => 'pimpinan_uc11@wd4.com', 
        'password' => bcrypt('password'), 
        'role_id' => $rolePimpinan->id
    ]);
    Profile::create(['user_id' => $userPimpinan->id]);
    
    auth()->login($userPimpinan);

    // ==========================================
    // 1. PENGUJIAN EVALUASI LAYAK (DISAHKAN) - DARI JURUSAN
    // ==========================================
    echo "\n▶ 1. PENGUJIAN EVALUASI LAYAK (DISAHKAN)\n";
    $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Jurusan Submit Test']);
    
    // Create cooperation that is waiting for evaluation
    $coopLayak = Cooperation::create([
        'judul' => 'Draft Layak Test',
        'jenis' => 'MoA',
        'status_dokumen' => 'Menunggu Evaluasi', // Must be waiting
        'status_berlaku' => 'aktif',
        'mitra_id' => $mitra->id,
        'tingkat' => 'Jurusan',
        'jurusan_id' => $jurusan->id
    ]);
    $coopLayak->jurusans()->sync([$jurusan->id]);

    $reqLayak = Request::create('/pimpinan/evaluate/' . $coopLayak->id, 'POST', [
        'status_validasi' => 'layak',
        'ringkasan' => 'Bagus sekali',
        'saran' => 'Lanjutkan',
        'tindak_lanjut' => 'Tanda tangan',
        'sesuai_rencana' => 5,
        'kualitas' => 5,
        'keterlibatan' => 5,
        'efisiensi' => 5,
        'kepuasan' => 5,
    ]);
    $reqLayak->setLaravelSession($app['session']->driver());
    
    $controllerPimpinan = new EvaluasiPimpinanController();
    $resLayak = $controllerPimpinan->evaluate($reqLayak, $coopLayak->id);
    
    if ($resLayak->getSession()->has('error')) {
        echo "   [✗] GAGAL (" . $resLayak->getSession()->get('error') . ")\n";
    } elseif ($resLayak->getSession()->has('success')) {
        $coopLayak->refresh();
        if ($coopLayak->status_dokumen === 'Disahkan' && strtolower($coopLayak->status_berlaku) === 'aktif') {
            echo "   [✓] BERHASIL (Status dokumen: Disahkan, Status berlaku: aktif)\n";
        } else {
            echo "   [✗] GAGAL (Sukses tapi status tidak sesuai, masih: " . $coopLayak->status_dokumen . " / " . $coopLayak->status_berlaku . ")\n";
        }
    }

    // ==========================================
    // 2. PENGUJIAN EVALUASI REVISI - DARI HUMAS
    // ==========================================
    echo "\n▶ 2. PENGUJIAN EVALUASI REVISI\n";
    $unit = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas Submit Test']);
    
    // Create cooperation that is waiting for evaluation
    $coopRevisi = Cooperation::create([
        'judul' => 'Draft Revisi Test',
        'jenis' => 'MoU',
        'status_dokumen' => 'Menunggu Evaluasi',
        'status_berlaku' => 'aktif',
        'mitra_id' => $mitra->id,
        'tingkat' => 'Institusi'
    ]);
    $coopRevisi->upas()->sync([]);

    $reqRevisi = Request::create('/pimpinan/evaluate/' . $coopRevisi->id, 'POST', [
        'status_validasi' => 'revisi',
        'ringkasan' => 'Ada yang salah',
        'saran' => 'Perbaiki judul',
        'tindak_lanjut' => 'Kirim balik ke Humas'
        // Jika revisi, metrik (sesuai_rencana dll) tidak wajib
    ]);
    $reqRevisi->setLaravelSession($app['session']->driver());
    
    $resRevisi = $controllerPimpinan->evaluate($reqRevisi, $coopRevisi->id);
    
    if ($resRevisi->getSession()->has('error')) {
        echo "   [✗] GAGAL (" . $resRevisi->getSession()->get('error') . ")\n";
    } elseif ($resRevisi->getSession()->has('success')) {
        $coopRevisi->refresh();
        if ($coopRevisi->status_dokumen === 'Revisi' && strtolower($coopRevisi->status_berlaku) === 'aktif') {
            echo "   [✓] BERHASIL (Status dokumen: Revisi)\n";
        } else {
            echo "   [✗] GAGAL (Sukses tapi status tidak sesuai, masih: " . $coopRevisi->status_dokumen . ")\n";
        }
    }

} catch (\Exception $e) {
    echo "\n[!] TERJADI KESALAHAN: " . $e->getMessage() . " di " . $e->getFile() . ":" . $e->getLine() . "\n";
} finally {
    DB::rollBack();
    echo "\n====================================================\n";
    echo "  PENGUJIAN SELESAI (Database Di-Rollback Aman)\n";
    echo "====================================================\n";
}

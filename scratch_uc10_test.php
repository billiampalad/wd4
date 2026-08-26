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
use App\Models\Upa;
use App\Models\Pusat;
use App\Models\UnitKerja;
use App\Models\Cooperation;
use App\Http\Controllers\Jurusan\KerjasamaJurusanController;
use App\Http\Controllers\Upa\KerjasamaUpaController;
use App\Http\Controllers\Pusat\KerjasamaPusatController;
use App\Http\Controllers\Unit\KerjasamaUnitController;

echo "====================================================\n";
echo "  PENGUJIAN UC10: SUBMIT DOKUMEN KE PIMPINAN\n";
echo "====================================================\n\n";

DB::beginTransaction();
try {
    $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Pengujian UC10'], ['status_akses' => 'Aktif', 'negara' => 'Indonesia', 'kategori' => 'nasional']);

    // Pastikan role pimpinan ada
    Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);

    // ==========================================
    // 1. PENGUJIAN JURUSAN
    // ==========================================
    echo "\n▶ 1. PENGUJIAN JURUSAN\n";
    $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
    $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Jurusan Submit Test']);
    $userJurusan = User::create(['name' => 'Kajur Submit', 'email' => 'kajur_submit@wd4.com', 'password' => bcrypt('password'), 'role_id' => $roleJurusan->id]);
    Profile::create(['user_id' => $userJurusan->id, 'jurusan_id' => $jurusan->id]);
    auth()->login($userJurusan);

    $coopJurusan = Cooperation::create([
        'judul' => 'Draft Jurusan Submit',
        'jenis' => 'MoA',
        'status_dokumen' => 'Draft',
        'status_berlaku' => 'aktif',
        'mitra_id' => $mitra->id,
        'tingkat' => 'Jurusan',
        'jurusan_id' => $jurusan->id
    ]);
    $coopJurusan->jurusans()->sync([$jurusan->id]);

    $reqJurusan = Request::create('/jurusan/data-kerjasama/' . $coopJurusan->id . '/submit', 'POST');
    $reqJurusan->setLaravelSession($app['session']->driver());
    
    $controllerJurusan = new KerjasamaJurusanController();
    $resJurusan = $controllerJurusan->submitToPimpinan($coopJurusan->id);
    
    if ($resJurusan->getSession()->has('error')) {
        echo "   [✗] GAGAL (" . $resJurusan->getSession()->get('error') . ")\n";
    } elseif ($resJurusan->getSession()->has('success')) {
        $coopJurusan->refresh();
        if ($coopJurusan->status_dokumen === 'Menunggu Evaluasi') {
            echo "   [✓] BERHASIL (Status berubah ke Menunggu Evaluasi)\n";
        } else {
            echo "   [✗] GAGAL (Sukses tapi status tidak berubah, masih: " . $coopJurusan->status_dokumen . ")\n";
        }
    }

    // ==========================================
    // 2. PENGUJIAN UPA
    // ==========================================
    echo "\n▶ 2. PENGUJIAN UPA\n";
    $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
    $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Submit Test']);
    $userUpa = User::create(['name' => 'UPA Submit', 'email' => 'upa_submit@wd4.com', 'password' => bcrypt('password'), 'role_id' => $roleUpa->id]);
    Profile::create(['user_id' => $userUpa->id, 'upa_id' => $upa->id]);
    auth()->login($userUpa);

    $coopUpa = Cooperation::create([
        'judul' => 'Draft UPA Submit',
        'jenis' => 'IA',
        'status_dokumen' => 'Draft',
        'status_berlaku' => 'aktif',
        'mitra_id' => $mitra->id,
        'tingkat' => 'Pusat/UPA',
        'upa_id' => $upa->id
    ]);
    $coopUpa->upas()->sync([$upa->id]);

    $reqUpa = Request::create('/upa/data-kerjasama/' . $coopUpa->id . '/submit', 'POST');
    $reqUpa->setLaravelSession($app['session']->driver());
    
    $controllerUpa = new KerjasamaUpaController();
    $resUpa = $controllerUpa->submitToPimpinan($coopUpa->id);
    
    if ($resUpa->getSession()->has('error')) {
        echo "   [✗] GAGAL (" . $resUpa->getSession()->get('error') . ")\n";
    } elseif ($resUpa->getSession()->has('success')) {
        $coopUpa->refresh();
        if ($coopUpa->status_dokumen === 'Menunggu Evaluasi') {
            echo "   [✓] BERHASIL (Status berubah ke Menunggu Evaluasi)\n";
        } else {
            echo "   [✗] GAGAL (Sukses tapi status tidak berubah, masih: " . $coopUpa->status_dokumen . ")\n";
        }
    }

    // ==========================================
    // 3. PENGUJIAN PUSAT
    // ==========================================
    echo "\n▶ 3. PENGUJIAN PUSAT\n";
    $rolePusat = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
    $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Submit Test']);
    $userPusat = User::create(['name' => 'Pusat Submit', 'email' => 'pusat_submit@wd4.com', 'password' => bcrypt('password'), 'role_id' => $rolePusat->id]);
    Profile::create(['user_id' => $userPusat->id, 'pusat_id' => $pusat->id]);
    auth()->login($userPusat);

    $coopPusat = Cooperation::create([
        'judul' => 'Draft Pusat Submit',
        'jenis' => 'MoU',
        'status_dokumen' => 'Draft',
        'status_berlaku' => 'aktif',
        'mitra_id' => $mitra->id,
        'tingkat' => 'Pusat/UPA',
        'pusat_id' => $pusat->id
    ]);
    $coopPusat->pusats()->sync([$pusat->id]);

    $reqPusat = Request::create('/pusat/data-kerjasama/' . $coopPusat->id . '/submit', 'POST');
    $reqPusat->setLaravelSession($app['session']->driver());
    
    $controllerPusat = new KerjasamaPusatController();
    $resPusat = $controllerPusat->submitToPimpinan($coopPusat->id);
    
    if ($resPusat->getSession()->has('error')) {
        echo "   [✗] GAGAL (" . $resPusat->getSession()->get('error') . ")\n";
    } elseif ($resPusat->getSession()->has('success')) {
        $coopPusat->refresh();
        if ($coopPusat->status_dokumen === 'Menunggu Evaluasi') {
            echo "   [✓] BERHASIL (Status berubah ke Menunggu Evaluasi)\n";
        } else {
            echo "   [✗] GAGAL (Sukses tapi status tidak berubah, masih: " . $coopPusat->status_dokumen . ")\n";
        }
    }

    // ==========================================
    // 4. PENGUJIAN HUMAS (UNIT KERJA)
    // ==========================================
    echo "\n▶ 4. PENGUJIAN HUMAS (UNIT KERJA)\n";
    $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
    $unit = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas Submit Test']);
    $userUnit = User::create(['name' => 'Humas Submit', 'email' => 'humas_submit@wd4.com', 'password' => bcrypt('password'), 'role_id' => $roleUnit->id]);
    Profile::create(['user_id' => $userUnit->id, 'unit_kerja_id' => $unit->id]);
    auth()->login($userUnit);

    $coopUnit = Cooperation::create([
        'judul' => 'Draft Humas Submit',
        'jenis' => 'MoU',
        'status_dokumen' => 'Draft',
        'status_berlaku' => 'aktif',
        'mitra_id' => $mitra->id,
        'tingkat' => 'Institusi'
    ]);

    $reqUnit = Request::create('/unit/data-kerjasama/' . $coopUnit->id . '/submit', 'POST');
    $reqUnit->setLaravelSession($app['session']->driver());
    
    $controllerUnit = new KerjasamaUnitController();
    $resUnit = $controllerUnit->submitToPimpinan($coopUnit->id);
    
    if ($resUnit->getSession()->has('error')) {
        echo "   [✗] GAGAL (" . $resUnit->getSession()->get('error') . ")\n";
    } elseif ($resUnit->getSession()->has('success')) {
        $coopUnit->refresh();
        if ($coopUnit->status_dokumen === 'Menunggu Evaluasi') {
            echo "   [✓] BERHASIL (Status berubah ke Menunggu Evaluasi)\n";
        } else {
            echo "   [✗] GAGAL (Sukses tapi status tidak berubah, masih: " . $coopUnit->status_dokumen . ")\n";
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

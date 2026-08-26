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
echo "  PENGUJIAN UC08: INPUT DOKUMEN KERJA SAMA\n";
echo "====================================================\n\n";

DB::beginTransaction();
try {
    // 0. Setup Mitra
    $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Pengujian UC08'], ['status_akses' => 'Aktif', 'negara' => 'Indonesia', 'kategori' => 'nasional']);

    // ==========================================
    // 1. PENGUJIAN JURUSAN
    // ==========================================
    echo "\n▶ 1. PENGUJIAN JURUSAN\n";
    $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
    $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Jurusan Pengujian']);
    $userJurusan = User::create(['name' => 'Kajur Test', 'email' => 'kajur_test@wd4.com', 'password' => bcrypt('password'), 'role_id' => $roleJurusan->id]);
    Profile::create(['user_id' => $userJurusan->id, 'jurusan_id' => $jurusan->id]);

    auth()->login($userJurusan);
    
    $reqJurusan = Request::create('/jurusan/data-kerjasama', 'POST', [
        'title' => 'MoA Jurusan Pengujian & Mitra',
        'jenis' => 'MoA (Memorandum of Agreement)',
        'doc_number' => 'MOA/JRS/001',
        'tipe_pelaksana' => 'jurusan',
        'pelaksana_jurusan_ids' => [$jurusan->id],
        'penggiat_mitra_ids' => [$mitra->id],
        'penggiat' => [['nama_penandatangan' => 'Budi Mitra', 'jabatan_penandatangan' => 'Direktur']],
        'nama_penandatangan' => 'Kajur Test',
        'document_link' => 'https://drive.google.com/test-jurusan'
    ]);
    $reqJurusan->setLaravelSession($app['session']->driver());
    
    $controllerJurusan = new KerjasamaJurusanController();
    $resJurusan = $controllerJurusan->store($reqJurusan);
    
    if ($resJurusan->getSession()->has('error')) {
        echo "   [✗] STATUS: GAGAL (" . $resJurusan->getSession()->get('error') . ")\n";
    } elseif ($resJurusan->getSession()->has('success')) {
        $coop = Cooperation::where('judul', 'MoA Jurusan Pengujian & Mitra')->first();
        if ($coop && $coop->jurusans->contains($jurusan->id)) {
            echo "   [✓] STATUS: BERHASIL (Data tersimpan & terhubung ke Jurusan)\n";
        } else {
            echo "   [✗] STATUS: GAGAL (Data tersimpan tapi tidak terhubung ke Jurusan)\n";
        }
    } else {
        echo "   [?] STATUS: TIDAK DIKETAHUI (No success/error message)\n";
    }

    // ==========================================
    // 2. PENGUJIAN UPA
    // ==========================================
    echo "\n▶ 2. PENGUJIAN UPA\n";
    $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
    $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Pengujian']);
    $userUpa = User::create(['name' => 'Kepala UPA Test', 'email' => 'upa_test@wd4.com', 'password' => bcrypt('password'), 'role_id' => $roleUpa->id]);
    Profile::create(['user_id' => $userUpa->id, 'upa_id' => $upa->id]);

    auth()->login($userUpa);
    
    $reqUpa = Request::create('/upa/data-kerjasama', 'POST', [
        'title' => 'IA UPA Pengujian & Mitra',
        'jenis' => 'IA (Implementation Agreement)',
        'doc_number' => 'IA/UPA/001',
        'tipe_pelaksana' => 'upa',
        'pelaksana_upa_ids' => [$upa->id],
        'penggiat_mitra_ids' => [$mitra->id],
        'penggiat' => [['nama_penandatangan' => 'Budi Mitra', 'jabatan_penandatangan' => 'Direktur']],
        'nama_penandatangan' => 'Kepala UPA Test',
        'document_link' => 'https://drive.google.com/test-upa'
    ]);
    $reqUpa->setLaravelSession($app['session']->driver());
    
    $controllerUpa = new KerjasamaUpaController();
    $resUpa = $controllerUpa->store($reqUpa);
    
    if ($resUpa->getSession()->has('error')) {
        echo "   [✗] STATUS: GAGAL (" . $resUpa->getSession()->get('error') . ")\n";
    } elseif ($resUpa->getSession()->has('success')) {
        $coop = Cooperation::where('judul', 'IA UPA Pengujian & Mitra')->first();
        if ($coop && $coop->upas->contains($upa->id)) {
            echo "   [✓] STATUS: BERHASIL (Data tersimpan & terhubung ke UPA)\n";
        } else {
            echo "   [✗] STATUS: GAGAL (Data tersimpan tapi tidak terhubung ke UPA)\n";
        }
    } else {
        echo "   [?] STATUS: TIDAK DIKETAHUI (No success/error message)\n";
    }

    // ==========================================
    // 3. PENGUJIAN PUSAT
    // ==========================================
    echo "\n▶ 3. PENGUJIAN PUSAT\n";
    $rolePusat = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
    $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Pengujian']);
    $userPusat = User::create(['name' => 'Kepala Pusat Test', 'email' => 'pusat_test@wd4.com', 'password' => bcrypt('password'), 'role_id' => $rolePusat->id]);
    Profile::create(['user_id' => $userPusat->id, 'pusat_id' => $pusat->id]);

    auth()->login($userPusat);
    
    $reqPusat = Request::create('/pusat/data-kerjasama', 'POST', [
        'title' => 'MoU Pusat Pengujian & Mitra',
        'jenis' => 'MoU (Memorandum of Understanding)',
        'doc_number' => 'MOU/PST/001',
        'tipe_pelaksana' => 'pusat',
        'pelaksana_pusat_ids' => [$pusat->id],
        'penggiat_mitra_ids' => [$mitra->id],
        'penggiat' => [['nama_penandatangan' => 'Budi Mitra', 'jabatan_penandatangan' => 'Direktur']],
        'nama_penandatangan' => 'Kepala Pusat Test',
        'document_link' => 'https://drive.google.com/test-pusat'
    ]);
    $reqPusat->setLaravelSession($app['session']->driver());
    
    $controllerPusat = new KerjasamaPusatController();
    $resPusat = $controllerPusat->store($reqPusat);
    
    if ($resPusat->getSession()->has('error')) {
        echo "   [✗] STATUS: GAGAL (" . $resPusat->getSession()->get('error') . ")\n";
    } elseif ($resPusat->getSession()->has('success')) {
        $coop = Cooperation::where('judul', 'MoU Pusat Pengujian & Mitra')->first();
        if ($coop && $coop->pusats->contains($pusat->id)) {
            echo "   [✓] STATUS: BERHASIL (Data tersimpan & terhubung ke Pusat)\n";
        } else {
            echo "   [✗] STATUS: GAGAL (Data tersimpan tapi tidak terhubung ke Pusat)\n";
        }
    } else {
        echo "   [?] STATUS: TIDAK DIKETAHUI (No success/error message)\n";
    }

    // ==========================================
    // 4. PENGUJIAN HUMAS (UNIT KERJA)
    // ==========================================
    echo "\n▶ 4. PENGUJIAN HUMAS (UNIT KERJA)\n";
    $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
    $unit = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas Pengujian']);
    $userUnit = User::create(['name' => 'Kepala Humas Test', 'email' => 'humas_test@wd4.com', 'password' => bcrypt('password'), 'role_id' => $roleUnit->id]);
    Profile::create(['user_id' => $userUnit->id, 'unit_kerja_id' => $unit->id]);

    auth()->login($userUnit);
    
    $reqUnit = Request::create('/unit/data-kerjasama', 'POST', [
        'title' => 'MoU Humas Pengujian & Mitra',
        'jenis' => 'MoU (Memorandum of Understanding)',
        'doc_number' => 'MOU/HMS/001',
        'tipe_pelaksana' => 'unit', // is it unit? wait, I will check the controller. Usually Humas inputs for Politeknik level without specific pelaksana, or 'unit'
        'pelaksana_unit_ids' => [$unit->id],
        'penggiat_mitra_ids' => [$mitra->id],
        'penggiat' => [['nama_penandatangan' => 'Budi Mitra', 'jabatan_penandatangan' => 'Direktur']],
        'nama_penandatangan' => 'Kepala Humas Test',
        'document_link' => 'https://drive.google.com/test-humas'
    ]);
    $reqUnit->setLaravelSession($app['session']->driver());
    
    $controllerUnit = new KerjasamaUnitController();
    $resUnit = $controllerUnit->store($reqUnit);
    
    if ($resUnit->getSession()->has('error')) {
        echo "   [✗] STATUS: GAGAL (" . $resUnit->getSession()->get('error') . ")\n";
    } elseif ($resUnit->getSession()->has('success')) {
        $coop = Cooperation::where('judul', 'MoU Humas Pengujian & Mitra')->first();
        if ($coop) {
            echo "   [✓] STATUS: BERHASIL (Data tersimpan)\n";
        } else {
            echo "   [✗] STATUS: GAGAL (Data tersimpan tapi tidak ditemukan)\n";
        }
    } else {
        echo "   [?] STATUS: TIDAK DIKETAHUI (No success/error message)\n";
    }

} catch (\Exception $e) {
    echo "\n[!] TERJADI KESALAHAN: " . $e->getMessage() . " di " . $e->getFile() . ":" . $e->getLine() . "\n";
} finally {
    DB::rollBack();
    echo "\n====================================================\n";
    echo "  PENGUJIAN SELESAI (Database Di-Rollback Aman)\n";
    echo "====================================================\n";
}

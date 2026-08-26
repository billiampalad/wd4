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
echo "  PENGUJIAN UC09: EDIT DOKUMEN KERJA SAMA\n";
echo "====================================================\n\n";

DB::beginTransaction();
try {
    $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Pengujian UC09'], ['status_akses' => 'Aktif', 'negara' => 'Indonesia', 'kategori' => 'nasional']);

    // ==========================================
    // 1. PENGUJIAN JURUSAN
    // ==========================================
    echo "\n▶ 1. PENGUJIAN JURUSAN\n";
    $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
    $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Jurusan Pengujian Edit']);
    $userJurusan = User::create(['name' => 'Kajur Edit', 'email' => 'kajur_edit@wd4.com', 'password' => bcrypt('password'), 'role_id' => $roleJurusan->id]);
    Profile::create(['user_id' => $userJurusan->id, 'jurusan_id' => $jurusan->id]);
    auth()->login($userJurusan);

    // Create Draft Cooperation
    $coopJurusan = Cooperation::create([
        'judul' => 'Draft Jurusan Asli',
        'jenis' => 'MoA',
        'status_dokumen' => 'Draft',
        'status_berlaku' => 'aktif',
        'mitra_id' => $mitra->id,
        'tingkat' => 'Jurusan',
        'jurusan_id' => $jurusan->id
    ]);
    $coopJurusan->jurusans()->sync([$jurusan->id]);

    $reqJurusan = Request::create('/jurusan/data-kerjasama/' . $coopJurusan->id, 'PUT', [
        'title' => 'Draft Jurusan Diedit',
        'jenis' => 'MoA (Memorandum of Agreement)',
        'doc_number' => 'MOA/JRS/EDIT',
        'tipe_pelaksana' => 'jurusan',
        'pelaksana_jurusan_ids' => [$jurusan->id],
        'penggiat_mitra_ids' => [$mitra->id],
        'penggiat' => [
            [
                'nama_penandatangan' => 'Budi',
                'jabatan_penandatangan' => 'Direktur',
                'nama_pj' => 'Andi',
                'jabatan_pj' => 'Manajer',
            ]
        ],
        'document_link' => 'https://drive.google.com/test-jurusan-edit'
    ]);
    $reqJurusan->setLaravelSession($app['session']->driver());
    
    $controllerJurusan = new KerjasamaJurusanController();
    $resJurusan = $controllerJurusan->update($reqJurusan, $coopJurusan->id);
    
    if ($resJurusan->getSession()->has('error')) {
        echo "   [✗] GAGAL (" . $resJurusan->getSession()->get('error') . ")\n";
    } elseif ($resJurusan->getSession()->has('success')) {
        $coopJurusan->refresh();
        if ($coopJurusan->judul === 'Draft Jurusan Diedit') {
            echo "   [✓] BERHASIL (Data terupdate: " . $coopJurusan->judul . ")\n";
        } else {
            echo "   [✗] GAGAL (Sukses tapi data tidak berubah)\n";
        }
    }

    // ==========================================
    // 2. PENGUJIAN UPA
    // ==========================================
    echo "\n▶ 2. PENGUJIAN UPA\n";
    $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
    $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Pengujian Edit']);
    $userUpa = User::create(['name' => 'UPA Edit', 'email' => 'upa_edit@wd4.com', 'password' => bcrypt('password'), 'role_id' => $roleUpa->id]);
    Profile::create(['user_id' => $userUpa->id, 'upa_id' => $upa->id]);
    auth()->login($userUpa);

    $coopUpa = Cooperation::create([
        'judul' => 'Draft UPA Asli',
        'jenis' => 'IA',
        'status_dokumen' => 'Draft',
        'status_berlaku' => 'aktif',
        'mitra_id' => $mitra->id,
        'tingkat' => 'Pusat/UPA',
        'upa_id' => $upa->id
    ]);
    $coopUpa->upas()->sync([$upa->id]);

    $reqUpa = Request::create('/upa/data-kerjasama/' . $coopUpa->id, 'PUT', [
        'title' => 'Draft UPA Diedit',
        'jenis' => 'IA (Implementation Agreement)',
        'doc_number' => 'IA/UPA/EDIT',
        'tipe_pelaksana' => 'upa',
        'pelaksana_upa_ids' => [$upa->id],
        'penggiat_mitra_ids' => [$mitra->id],
        'penggiat' => [
            [
                'nama_penandatangan' => 'Budi',
                'jabatan_penandatangan' => 'Direktur',
                'nama_pj' => 'Andi',
                'jabatan_pj' => 'Manajer',
            ]
        ],
        'document_link' => 'https://drive.google.com/test-upa-edit'
    ]);
    $reqUpa->setLaravelSession($app['session']->driver());
    
    $controllerUpa = new KerjasamaUpaController();
    $resUpa = $controllerUpa->update($reqUpa, $coopUpa->id);
    
    if ($resUpa->getSession()->has('error')) {
        echo "   [✗] GAGAL (" . $resUpa->getSession()->get('error') . ")\n";
    } elseif ($resUpa->getSession()->has('success')) {
        $coopUpa->refresh();
        if ($coopUpa->judul === 'Draft UPA Diedit') {
            echo "   [✓] BERHASIL (Data terupdate: " . $coopUpa->judul . ")\n";
        } else {
            echo "   [✗] GAGAL (Sukses tapi data tidak berubah)\n";
        }
    }

    // ==========================================
    // 3. PENGUJIAN PUSAT
    // ==========================================
    echo "\n▶ 3. PENGUJIAN PUSAT\n";
    $rolePusat = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
    $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Pengujian Edit']);
    $userPusat = User::create(['name' => 'Pusat Edit', 'email' => 'pusat_edit@wd4.com', 'password' => bcrypt('password'), 'role_id' => $rolePusat->id]);
    Profile::create(['user_id' => $userPusat->id, 'pusat_id' => $pusat->id]);
    auth()->login($userPusat);

    $coopPusat = Cooperation::create([
        'judul' => 'Draft Pusat Asli',
        'jenis' => 'MoU',
        'status_dokumen' => 'Draft',
        'status_berlaku' => 'aktif',
        'mitra_id' => $mitra->id,
        'tingkat' => 'Pusat/UPA',
        'pusat_id' => $pusat->id
    ]);
    $coopPusat->pusats()->sync([$pusat->id]);

    $reqPusat = Request::create('/pusat/data-kerjasama/' . $coopPusat->id, 'PUT', [
        'title' => 'Draft Pusat Diedit',
        'jenis' => 'MoU (Memorandum of Understanding)',
        'doc_number' => 'MOU/PST/EDIT',
        'tipe_pelaksana' => 'pusat',
        'pelaksana_pusat_ids' => [$pusat->id],
        'penggiat_mitra_ids' => [$mitra->id],
        'penggiat' => [
            [
                'nama_penandatangan' => 'Budi',
                'jabatan_penandatangan' => 'Direktur',
                'nama_pj' => 'Andi',
                'jabatan_pj' => 'Manajer',
            ]
        ],
        'document_link' => 'https://drive.google.com/test-pusat-edit'
    ]);
    $reqPusat->setLaravelSession($app['session']->driver());
    
    $controllerPusat = new KerjasamaPusatController();
    $resPusat = $controllerPusat->update($reqPusat, $coopPusat->id);
    
    if ($resPusat->getSession()->has('error')) {
        echo "   [✗] GAGAL (" . $resPusat->getSession()->get('error') . ")\n";
    } elseif ($resPusat->getSession()->has('success')) {
        $coopPusat->refresh();
        if ($coopPusat->judul === 'Draft Pusat Diedit') {
            echo "   [✓] BERHASIL (Data terupdate: " . $coopPusat->judul . ")\n";
        } else {
            echo "   [✗] GAGAL (Sukses tapi data tidak berubah)\n";
        }
    }

    // ==========================================
    // 4. PENGUJIAN HUMAS (UNIT KERJA)
    // ==========================================
    echo "\n▶ 4. PENGUJIAN HUMAS (UNIT KERJA)\n";
    $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
    $unit = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas Pengujian Edit']);
    $userUnit = User::create(['name' => 'Humas Edit', 'email' => 'humas_edit@wd4.com', 'password' => bcrypt('password'), 'role_id' => $roleUnit->id]);
    Profile::create(['user_id' => $userUnit->id, 'unit_kerja_id' => $unit->id]);
    auth()->login($userUnit);

    $coopUnit = Cooperation::create([
        'judul' => 'Draft Humas Asli',
        'jenis' => 'MoU',
        'status_dokumen' => 'Draft',
        'status_berlaku' => 'aktif',
        'mitra_id' => $mitra->id,
        'tingkat' => 'Institusi'
    ]);

    $reqUnit = Request::create('/unit/data-kerjasama/' . $coopUnit->id, 'PUT', [
        'title' => 'Draft Humas Diedit',
        'jenis' => 'MoU (Memorandum of Understanding)',
        'doc_number' => 'MOU/HMS/EDIT',
        'tipe_pelaksana' => ['unit'], // Unit controller expects array sometimes
        'pelaksana_unit_ids' => [$unit->id],
        'penggiat_mitra_ids' => [$mitra->id],
        'penggiat' => [
            [
                'nama_penandatangan' => 'Budi',
                'jabatan_penandatangan' => 'Direktur',
                'nama_pj' => 'Andi',
                'jabatan_pj' => 'Manajer',
            ]
        ],
        'document_link' => 'https://drive.google.com/test-humas-edit'
    ]);
    $reqUnit->setLaravelSession($app['session']->driver());
    
    $controllerUnit = new KerjasamaUnitController();
    $resUnit = $controllerUnit->update($reqUnit, $coopUnit->id);
    
    if ($resUnit->getSession()->has('error')) {
        echo "   [✗] GAGAL (" . $resUnit->getSession()->get('error') . ")\n";
    } elseif ($resUnit->getSession()->has('success')) {
        $coopUnit->refresh();
        if ($coopUnit->judul === 'Draft Humas Diedit') {
            echo "   [✓] BERHASIL (Data terupdate: " . $coopUnit->judul . ")\n";
        } else {
            echo "   [✗] GAGAL (Sukses tapi data tidak berubah)\n";
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

<?php

require __DIR__ . '/../../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\ProdiController;
use App\Http\Controllers\Admin\UpaController;
use App\Http\Controllers\Admin\PusatController;
use App\Http\Controllers\Admin\UpelaksanaController;
use Illuminate\Http\Request;
use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\Upa;
use App\Models\Pusat;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\DB;

echo "====================================================\n";
echo "  PENGUJIAN CRUD UNIT PELAKSANA (JUR, PRD, UPA, PST)\n";
echo "====================================================\n\n";

DB::beginTransaction();

try {
    // 1. JURUSAN
    echo "▶ 1. PENGUJIAN JURUSAN:\n";
    $jurCtrl = new JurusanController();
    $req = Request::create('/admin/jurusan', 'POST', [
        'kode_jurusan' => 'JUR-TEST',
        'nama_jurusan' => 'Jurusan Testing'
    ]);
    $req->setLaravelSession($app['session']->driver());
    $jurCtrl->store($req);
    $jur = Jurusan::where('kode_jurusan', 'JUR-TEST')->first();
    if ($jur) echo "   [✓] Jurusan Tersimpan (ID: {$jur->id})\n";
    else echo "   [✗] GAGAL SIMPAN JURUSAN\n";

    // 2. PRODI
    echo "▶ 2. PENGUJIAN PRODI:\n";
    $prdCtrl = new ProdiController();
    $req2 = Request::create('/admin/prodi', 'POST', [
        'jurusan_id' => $jur->id,
        'kode_prodi' => 'PRD-TEST',
        'nama_prodi' => 'Prodi Testing',
        'jenjang' => 'D4'
    ]);
    $req2->setLaravelSession($app['session']->driver());
    $prdCtrl->store($req2);
    $prd = Prodi::where('kode_prodi', 'PRD-TEST')->first();
    if ($prd) echo "   [✓] Prodi Tersimpan (ID: {$prd->id}, Jurusan ID: {$prd->jurusan_id})\n";
    else echo "   [✗] GAGAL SIMPAN PRODI\n";

    // 3. UPA
    echo "▶ 3. PENGUJIAN UPA:\n";
    $upaCtrl = new UpaController();
    $req3 = Request::create('/admin/upa', 'POST', ['nama_upa' => 'UPA Testing']);
    $req3->setLaravelSession($app['session']->driver());
    $upaCtrl->store($req3);
    $upa = Upa::where('nama_upa', 'UPA Testing')->first();
    if ($upa) echo "   [✓] UPA Tersimpan (ID: {$upa->id})\n";
    else echo "   [✗] GAGAL SIMPAN UPA\n";

    // 4. PUSAT
    echo "▶ 4. PENGUJIAN PUSAT:\n";
    $pstCtrl = new PusatController();
    $req4 = Request::create('/admin/pusat', 'POST', ['nama_pusat' => 'Pusat Testing']);
    $req4->setLaravelSession($app['session']->driver());
    $pstCtrl->store($req4);
    $pst = Pusat::where('nama_pusat', 'Pusat Testing')->first();
    if ($pst) echo "   [✓] Pusat Tersimpan (ID: {$pst->id})\n";
    else echo "   [✗] GAGAL SIMPAN PUSAT\n";

    // 5. UNIT KERJA (HUMAS)
    echo "▶ 5. PENGUJIAN UNIT KERJA (HUMAS):\n";
    $upCtrl = new UpelaksanaController();
    $req5 = Request::create('/admin/upelaksana', 'POST', ['nama_unit_pelaksana' => 'Unit Kerja Testing']);
    $req5->setLaravelSession($app['session']->driver());
    $upCtrl->store($req5);
    $up = UnitKerja::where('nama_unit_pelaksana', 'Unit Kerja Testing')->first();
    if ($up) echo "   [✓] Unit Kerja Tersimpan (ID: {$up->id})\n";
    else echo "   [✗] GAGAL SIMPAN UNIT KERJA\n";

    // 6. DELETE TEST (CONSTRAINT CHECK JURUSAN -> PRODI CASCADE)
    echo "▶ 6. PENGUJIAN HAPUS JURUSAN (CASCADE PRODI):\n";
    $jurCtrl->destroy($jur->id);
    
    $checkJur = Jurusan::find($jur->id);
    $checkPrd = Prodi::find($prd->id);

    if (!$checkJur && !$checkPrd) {
        echo "   [✓] Jurusan Terhapus, dan Prodi yang terkait juga otomatis terhapus (CASCADE ON DELETE bekerja!).\n";
    } else {
        echo "   [✗] GAGAL HAPUS JURUSAN ATAU PRODI MASIH ADA\n";
    }

} catch (\Exception $e) {
    echo "\n[!] TERJADI KESALAHAN: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "\n====================================================\n";
    echo "  PENGUJIAN SELESAI (Database Di-Rollback Aman)\n";
    echo "====================================================\n";
}

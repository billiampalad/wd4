<?php

require __DIR__ . '/../../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\KlasifikasiController;
use Illuminate\Http\Request;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use Illuminate\Support\Facades\DB;

echo "====================================================\n";
echo "  PENGUJIAN CRUD KLASIFIKASI MITRA (ADMIN)\n";
echo "====================================================\n\n";

DB::beginTransaction();

try {
    $controller = new KlasifikasiController();

    // 1. TAMBAH DATA (CREATE / STORE)
    echo "▶ 1. PENGUJIAN TAMBAH DATA (STORE):\n";
    $req = Request::create('/admin/klasifikasi', 'POST', [
        'nama' => 'Klasifikasi Testing ' . rand(100, 999),
    ]);
    $req->setLaravelSession($app['session']->driver());
    $controller->store($req);
    $newKlas = Klasifikasi::where('nama', $req->nama)->first();
    if ($newKlas) echo "   [✓] STATUS: BERHASIL (ID: {$newKlas->id})\n";
    else echo "   [✗] STATUS: GAGAL MENYIMPAN\n";
    echo "\n";

    // 2. CEGAT DUPLIKAT
    echo "▶ 2. PENGUJIAN VALIDASI UNIK (DUPLIKAT):\n";
    $reqDup = Request::create('/admin/klasifikasi', 'POST', [
        'nama' => $req->nama,
    ]);
    $reqDup->setLaravelSession($app['session']->driver());
    try {
        $controller->store($reqDup);
        echo "   [✗] STATUS: GAGAL (Data duplikat berhasil tersimpan padahal harusnya dilarang!)\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        if (array_key_exists('nama', $e->errors())) {
            echo "   [✓] STATUS: BERHASIL (Sistem mencegah duplikasi)\n";
        }
    }
    echo "\n";

    // 3. EDIT DATA (UPDATE)
    echo "▶ 3. PENGUJIAN EDIT DATA (UPDATE):\n";
    $updatedName = $req->nama . ' Updated';
    $reqUp = Request::create('/admin/klasifikasi/' . $newKlas->id, 'PUT', [
        'nama' => $updatedName,
    ]);
    $reqUp->setLaravelSession($app['session']->driver());
    $controller->update($reqUp, $newKlas);
    $newKlas->refresh();
    if ($newKlas->nama === $updatedName) echo "   [✓] STATUS: BERHASIL UPDATE (Nama Baru: {$newKlas->nama})\n";
    else echo "   [✗] STATUS: GAGAL UPDATE\n";
    echo "\n";

    // 4. HAPUS DATA (SET NULL CONSTRAINT)
    echo "▶ 4. PENGUJIAN HAPUS DATA (ON DELETE SET NULL PADA MITRA):\n";
    // Buat Mitra dummy yang terikat ke Klasifikasi ini
    $mitra = Mitra::create([
        'nama_mitra' => 'Mitra Klasifikasi Test',
        'klasifikasi_id' => $newKlas->id,
        'negara' => 'Indonesia'
    ]);
    
    // Hapus klasifikasi
    $controller->destroy($newKlas);
    
    // Cek apakah Mitra masih ada dan klasifikasi_id menjadi NULL
    $mitra->refresh();
    if ($mitra && $mitra->klasifikasi_id === null) {
        echo "   [✓] STATUS: BERHASIL!\n";
        echo "       Klasifikasi terhapus, dan Mitra ID {$mitra->id} kini tidak memiliki klasifikasi (klasifikasi_id = NULL).\n";
    } else {
        echo "   [✗] STATUS: GAGAL. (Mitra mungkin terhapus atau klasifikasi_id tidak NULL)\n";
    }

} catch (\Exception $e) {
    echo "\n[!] TERJADI KESALAHAN: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "\n====================================================\n";
    echo "  PENGUJIAN SELESAI (Database Di-Rollback Aman)\n";
    echo "====================================================\n";
}

<?php

require __DIR__ . '/../../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\MitraController;
use Illuminate\Http\Request;
use App\Models\Mitra;
use App\Models\Klasifikasi;
use Illuminate\Support\Facades\DB;

echo "====================================================\n";
echo "  PENGUJIAN MENYELURUH CRUD DATA MITRA (ADMIN)\n";
echo "====================================================\n\n";

DB::beginTransaction();

try {
    $controller = new MitraController();
    $klas = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
    $mitraNameTest = 'PT Mitra Tes ' . rand(100, 999);

    // 1. TAMBAH DATA (CREATE / STORE)
    echo "▶ 1. PENGUJIAN TAMBAH DATA (STORE):\n";
    $storeRequest = Request::create('/admin/mitra', 'POST', [
        'nama_mitra' => $mitraNameTest,
        'id_klasifikasi' => $klas->id,
        'kategori' => 'nasional',
        'negara' => 'Indonesia',
        'alamat' => 'Jl. Test No. 1',
        'telp' => '081234567890',
        'website' => 'https://mitrates.com'
    ]);
    $storeRequest->setLaravelSession($app['session']->driver());
    
    $responseStore = $controller->store($storeRequest);
    $newMitra = Mitra::where('nama_mitra', $mitraNameTest)->first();
    
    if ($newMitra) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Data Tersimpan di Database:\n";
        echo "       - ID           : {$newMitra->id}\n";
        echo "       - Nama Mitra   : {$newMitra->nama_mitra}\n";
        echo "       - Telepon      : {$newMitra->telepon}\n";
        echo "       - Klasifikasi  : {$newMitra->klasifikasi_id}\n";
        echo "       - Negara       : {$newMitra->negara}\n";
    } else {
        echo "   [✗] STATUS: GAGAL MENYIMPAN\n";
    }
    echo "\n";

    // 2. LIHAT / DETAIL DATA (READ / INDEX)
    echo "▶ 2. PENGUJIAN LIHAT DATA (READ):\n";
    $viewIndex = $controller->index();
    $mitrasInList = $viewIndex->getData()['mitras'];
    $foundInList = $mitrasInList->firstWhere('id', $newMitra->id);

    $viewEdit = $controller->edit($newMitra);
    $mitraInEdit = $viewEdit->getData()['mitra'];

    if ($foundInList && $mitraInEdit) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Verifikasi Tampilan List & Edit:\n";
        echo "       - Ditemukan di Index List : Ya (Nama: {$foundInList->nama_mitra})\n";
        echo "       - Ditemukan di Form Edit  : Ya (ID: {$mitraInEdit->id})\n";
    } else {
        echo "   [✗] STATUS: GAGAL MEMUAT DETAIL\n";
    }
    echo "\n";

    // 3. EDIT DATA (UPDATE)
    echo "▶ 3. PENGUJIAN EDIT DATA (UPDATE):\n";
    $updatedName = $mitraNameTest . ' (Updated)';
    $updateRequest = Request::create('/admin/mitra/' . $newMitra->id, 'PUT', [
        'nama_mitra' => $updatedName,
        'id_klasifikasi' => $klas->id,
        'kategori' => 'internasional',
        'negara' => 'Singapura',
        'alamat' => 'Jl. Test No. 2',
        'telp' => '081234567891',
        'website' => 'https://mitrates.sg'
    ]);
    $updateRequest->setLaravelSession($app['session']->driver());

    $responseUpdate = $controller->update($updateRequest, $newMitra);
    $newMitra->refresh();

    if ($newMitra->nama_mitra === $updatedName && $newMitra->negara === 'Singapura' && $newMitra->telepon === '081234567891') {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Data Setelah Diperbarui:\n";
        echo "       - Nama Baru    : {$newMitra->nama_mitra}\n";
        echo "       - Negara Baru  : {$newMitra->negara}\n";
        echo "       - Telepon Baru : {$newMitra->telepon}\n";
    } else {
        echo "   [✗] STATUS: GAGAL MEMPERBARUI\n";
    }
    echo "\n";

    // 4. HAPUS DATA (DESTROY)
    echo "▶ 4. PENGUJIAN HAPUS DATA (DELETE):\n";
    $deletedId = $newMitra->id;
    $responseDestroy = $controller->destroy($newMitra);

    $checkDeleted = Mitra::find($deletedId);
    if (!$checkDeleted) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Mitra ID {$deletedId} telah berhasil dihapus dari database.\n";
    } else {
        echo "   [✗] STATUS: GAGAL MENGHAPUS\n";
    }

    echo "\n▶ 5. PENGUJIAN HAPUS DATA YANG SEDANG DIGUNAKAN (DELETE CONSTRAINTS):\n";
    // Setup dependency
    $usedMitra = Mitra::create([
        'nama_mitra' => 'Mitra Dipakai',
        'negara' => 'Indonesia',
        'telepon' => '123'
    ]);
    
    // Create dummy Cooperation
    DB::table('cooperations')->insert([
        'mitra_id' => $usedMitra->id,
        'doc_number' => 'DOC/123/2026',
        'judul' => 'Test Cooperation',
        'jenis' => 'MOU',
        'status_berlaku' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $controller->destroy($usedMitra);
    $checkUsedDeleted = Mitra::find($usedMitra->id);
    if ($checkUsedDeleted) {
        echo "   [✓] STATUS: BERHASIL MENCEGAH PENGHAPUSAN\n";
        echo "       Mitra ID {$usedMitra->id} tetap ada di DB karena dipakai di tabel cooperations.\n";
    } else {
        echo "   [✗] STATUS: GAGAL (DATA MALAH TERHAPUS!)\n";
    }

} catch (\Exception $e) {
    echo "\n[!] TERJADI KESALAHAN: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "\n====================================================\n";
    echo "  PENGUJIAN SELESAI (Database Di-Rollback Aman)\n";
    echo "====================================================\n";
}

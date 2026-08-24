<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\JenisKerjasamaController;
use Illuminate\Http\Request;
use App\Models\JenisKerjasama;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;

echo "====================================================\n";
echo "  PENGUJIAN MENYELURUH CRUD JENIS KERJA SAMA (ADMIN)\n";
echo "====================================================\n\n";

DB::beginTransaction();

try {
    $controller = new JenisKerjasamaController();
    $jkNameTest = 'Kerja Sama Tes ' . rand(100, 999);

    // 1. TAMBAH DATA (CREATE / STORE)
    echo "▶ 1. PENGUJIAN TAMBAH DATA (STORE):\n";
    $storeRequest = Request::create('/admin/jkerjasama', 'POST', [
        'nama_kerjasama' => $jkNameTest,
    ]);
    $storeRequest->setLaravelSession($app['session']->driver());
    
    $responseStore = $controller->store($storeRequest);
    $newJk = JenisKerjasama::where('nama_kerjasama', $jkNameTest)->first();
    
    if ($newJk) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Data Tersimpan di Database:\n";
        echo "       - ID             : {$newJk->id}\n";
        echo "       - Nama Kerjasama : {$newJk->nama_kerjasama}\n";
    } else {
        echo "   [✗] STATUS: GAGAL MENYIMPAN\n";
    }
    echo "\n";

    // 2. LIHAT / DETAIL DATA (READ / INDEX)
    echo "▶ 2. PENGUJIAN LIHAT DATA (READ):\n";
    $viewIndex = $controller->index();
    $jksInList = $viewIndex->getData()['jenisKerjasamas'];
    $foundInList = $jksInList->firstWhere('id', $newJk->id);

    $viewEdit = $controller->edit($newJk);
    $jkInEdit = $viewEdit->getData()['jkerjasama'];

    if ($foundInList && $jkInEdit) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Verifikasi Tampilan List & Detail:\n";
        echo "       - Ditemukan di Index List : Ya (Nama: {$foundInList->nama_kerjasama})\n";
        echo "       - Ditemukan di Form Edit  : Ya (ID: {$jkInEdit->id})\n";
    } else {
        echo "   [✗] STATUS: GAGAL MEMUAT DETAIL\n";
    }
    echo "\n";

    // 3. EDIT DATA (UPDATE)
    echo "▶ 3. PENGUJIAN EDIT DATA (UPDATE):\n";
    $updatedName = $jkNameTest . ' (Updated)';
    $updateRequest = Request::create('/admin/jkerjasama/' . $newJk->id, 'PUT', [
        'nama_kerjasama' => $updatedName,
    ]);
    $updateRequest->setLaravelSession($app['session']->driver());

    $responseUpdate = $controller->update($updateRequest, $newJk);
    $newJk->refresh();

    if ($newJk->nama_kerjasama === $updatedName) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Data Setelah Diperbarui:\n";
        echo "       - Nama Baru : {$newJk->nama_kerjasama}\n";
    } else {
        echo "   [✗] STATUS: GAGAL MEMPERBARUI\n";
    }
    echo "\n";

    // 4. HAPUS DATA (DESTROY)
    echo "▶ 4. PENGUJIAN HAPUS DATA (DELETE):\n";
    $deletedId = $newJk->id;
    $responseDestroy = $controller->destroy($newJk);

    $checkDeleted = JenisKerjasama::find($deletedId);
    if (!$checkDeleted) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Jenis Kerjasama ID {$deletedId} telah berhasil dihapus dari database.\n";
    } else {
        echo "   [✗] STATUS: GAGAL MENGHAPUS\n";
    }

    echo "\n▶ 5. PENGUJIAN HAPUS DATA YANG SEDANG DIGUNAKAN (DELETE CONSTRAINTS):\n";
    // Setup dependency
    $usedJk = JenisKerjasama::create(['nama' => 'Dipakai Kegiatan']);
    

    
    // Attach to Jenis Kerjasama in detail_kegiatans table
    DB::table('detail_kegiatans')->insert([
        'jenis_kerjasama_id' => $usedJk->id,
    ]);

    try {
        $controller->destroy($usedJk);
        $checkUsedDeleted = JenisKerjasama::find($usedJk->id);
        if (!$checkUsedDeleted) {
            echo "   [!] WARNING: Data Terhapus! (Constraint tidak aktif atau Cascade berlaku!)\n";
            echo "       Data ID {$usedJk->id} terhapus, padahal sedang digunakan oleh Kegiatan ID {$kegiatanId}.\n";
        } else {
            echo "   [✓] STATUS: GAGAL DIHAPUS SEBAGAIMANA MESTINYA (TETAP ADA DI DB)\n";
        }
    } catch (\Exception $e) {
        echo "   [✓] STATUS: DITOLAK KARENA EXCEPTION\n";
        echo "       Pesan: " . $e->getMessage() . "\n";
    }

} catch (\Exception $e) {
    echo "\n[!] TERJADI KESALAHAN: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "\n====================================================\n";
    echo "  PENGUJIAN SELESAI (Database Di-Rollback Aman)\n";
    echo "====================================================\n";
}

<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\RoleController;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

echo "====================================================\n";
echo "  PENGUJIAN MENYELURUH CRUD ROLE (ROLE ADMIN)\n";
echo "====================================================\n\n";

DB::beginTransaction();

try {
    $controller = new RoleController();
    $roleNameTest = 'Admin Keuangan '.rand(100, 999);

    // 1. TAMBAH DATA (CREATE / STORE)
    echo "▶ 1. PENGUJIAN TAMBAH DATA (STORE):\n";
    $storeRequest = Request::create('/admin/roles', 'POST', [
        'role_name' => $roleNameTest,
    ]);
    $storeRequest->setLaravelSession($app['session']->driver());
    
    $responseStore = $controller->store($storeRequest);
    $newRole = Role::where('name', $roleNameTest)->first();
    
    if ($newRole) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Data Tersimpan di Database:\n";
        echo "       - ID       : {$newRole->id}\n";
        echo "       - Name     : {$newRole->name}\n";
        echo "       - RoleName (Virtual): {$newRole->role_name}\n";
    } else {
        echo "   [✗] STATUS: GAGAL MENYIMPAN\n";
    }
    echo "\n";

    // 2. LIHAT / DETAIL DATA (READ / INDEX)
    echo "▶ 2. PENGUJIAN LIHAT DATA (READ):\n";
    $viewIndex = $controller->index();
    $rolesInList = $viewIndex->getData()['roles'];
    $foundInList = $rolesInList->firstWhere('id', $newRole->id);

    $viewEdit = $controller->edit($newRole);
    $roleInEdit = $viewEdit->getData()['role'];

    if ($foundInList && $roleInEdit) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Verifikasi Tampilan List & Detail:\n";
        echo "       - Ditemukan di Index List : Ya (Nama: {$foundInList->name})\n";
        echo "       - Ditemukan di Form Edit  : Ya (ID: {$roleInEdit->id})\n";
    } else {
        echo "   [✗] STATUS: GAGAL MEMUAT DETAIL\n";
    }
    echo "\n";

    // 3. EDIT DATA (UPDATE)
    echo "▶ 3. PENGUJIAN EDIT DATA (UPDATE):\n";
    $updatedName = $roleNameTest . ' (Updated)';
    $updateRequest = Request::create('/admin/roles/' . $newRole->id, 'PUT', [
        'role_name' => $updatedName,
    ]);
    $updateRequest->setLaravelSession($app['session']->driver());

    $responseUpdate = $controller->update($updateRequest, $newRole);
    $newRole->refresh();

    if ($newRole->name === $updatedName) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Data Setelah Diperbarui:\n";
        echo "       - Nama Baru (name)      : {$newRole->name}\n";
        echo "       - Nama Baru (role_name) : {$newRole->role_name}\n";
    } else {
        echo "   [✗] STATUS: GAGAL MEMPERBARUI\n";
    }
    echo "\n";

    // 4. HAPUS DATA (DESTROY)
    echo "▶ 4. PENGUJIAN HAPUS DATA (DELETE):\n";
    $deletedId = $newRole->id;
    $responseDestroy = $controller->destroy($newRole);

    $checkDeleted = Role::find($deletedId);
    if (!$checkDeleted) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Role ID {$deletedId} telah berhasil dihapus dari database.\n";
    } else {
        echo "   [✗] STATUS: GAGAL MENGHAPUS\n";
    }

} catch (\Exception $e) {
    echo "\n[!] TERJADI KESALAHAN: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "\n====================================================\n";
    echo "  PENGUJIAN SELESAI (Database Di-Rollback Aman)\n";
    echo "====================================================\n";
}

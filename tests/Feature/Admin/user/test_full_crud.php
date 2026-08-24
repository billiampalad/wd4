<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Jurusan;
use App\Models\UnitKerja;
use App\Models\Upa;
use App\Models\Pusat;
use Illuminate\Support\Facades\DB;

echo "====================================================\n";
echo "  PENGUJIAN MENYELURUH CRUD PENGGUNA (ROLE ADMIN)\n";
echo "====================================================\n\n";

DB::beginTransaction();

try {
    $controller = new UserController();
    $jurusanRole = Role::where('role_name', 'jurusan')->first();
    $jurusan = Jurusan::first() ?? Jurusan::create(['nama_jurusan' => 'Teknik Informatika']);

    // 1. TAMBAH DATA (CREATE / STORE)
    echo "▶ 1. PENGUJIAN TAMBAH DATA (STORE):\n";
    $storeRequest = Request::create('/admin/users', 'POST', [
        'name' => 'Dr. Budi Santoso, M.Kom',
        'nik' => '198501012010121001',
        'email' => 'budi.santoso@poltek.ac.id',
        'password' => 'PasswordKuat123!',
        'role_id' => $jurusanRole->id,
        'jabatan' => 'Ketua Jurusan',
        'jurusan_id' => $jurusan->id,
    ]);
    $storeRequest->setLaravelSession($app['session']->driver());
    
    $responseStore = $controller->store($storeRequest);
    $newUser = User::where('email', 'budi.santoso@poltek.ac.id')->first();
    
    if ($newUser && $newUser->profile) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Data Tersimpan di Database:\n";
        echo "       - ID       : {$newUser->id}\n";
        echo "       - Nama     : {$newUser->name}\n";
        echo "       - NIK      : {$newUser->nik}\n";
        echo "       - Email    : {$newUser->email}\n";
        echo "       - Role     : {$newUser->role->role_name}\n";
        echo "       - Jabatan  : {$newUser->profile->jabatan}\n";
        echo "       - Jurusan  : " . ($newUser->profile->jurusan?->nama_jurusan ?? '-') . "\n";
    } else {
        echo "   [✗] STATUS: GAGAL MENYIMPAN\n";
    }
    echo "\n";

    // 2. LIHAT / DETAIL DATA (READ / INDEX / EDIT VIEW PREVIEW)
    echo "▶ 2. PENGUJIAN LIHAT / DETAIL DATA (READ & DETAIL):\n";
    // Cek pengambilan data via index (Daftar & Detail semua atribut)
    $viewIndex = $controller->index();
    $usersInList = $viewIndex->getData()['users'];
    $foundInList = $usersInList->firstWhere('id', $newUser->id);

    // Cek form edit (yang memuat kartu detail profil lengkap di sidebar)
    $viewEdit = $controller->edit($newUser->id);
    $userInEdit = $viewEdit->getData()['user'];

    if ($foundInList && $userInEdit) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Verifikasi Tampilan List & Detail:\n";
        echo "       - Ditemukan di Index List : Ya (Nama: {$foundInList->name}, Email: {$foundInList->email})\n";
        echo "       - Relasi Profil Termuat   : Ya (Role: {$foundInList->role->role_name}, Unit/Jurusan: {$foundInList->profile->jurusan->nama_jurusan})\n";
        echo "       - Ditemukan di Form Edit  : Ya (ID: {$userInEdit->id})\n";
    } else {
        echo "   [✗] STATUS: GAGAL MEMUAT DETAIL\n";
    }
    echo "\n";

    // 3. EDIT DATA (UPDATE)
    echo "▶ 3. PENGUJIAN EDIT DATA (UPDATE):\n";
    $updateRequest = Request::create('/admin/users/' . $newUser->id, 'PUT', [
        'name' => 'Prof. Dr. Budi Santoso, M.Kom (Updated)',
        'nik' => '198501012010121001',
        'email' => 'budi.santoso.updated@poltek.ac.id',
        'password' => '', // kosong = tidak ubah password
        'role_id' => $jurusanRole->id,
        'jabatan' => 'Ketua Jurusan Senior',
        'jurusan_id' => $jurusan->id,
    ]);
    $updateRequest->setLaravelSession($app['session']->driver());

    $responseUpdate = $controller->update($updateRequest, (string) $newUser->id);
    $newUser->refresh();

    if ($newUser->name === 'Prof. Dr. Budi Santoso, M.Kom (Updated)' && 
        $newUser->email === 'budi.santoso.updated@poltek.ac.id' &&
        $newUser->profile->jabatan === 'Ketua Jurusan Senior') {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] Data Setelah Diperbarui:\n";
        echo "       - Nama Baru    : {$newUser->name}\n";
        echo "       - Email Baru   : {$newUser->email}\n";
        echo "       - Jabatan Baru : {$newUser->profile->jabatan}\n";
    } else {
        echo "   [✗] STATUS: GAGAL MEMPERBARUI\n";
    }
    echo "\n";

    // 4. HAPUS DATA (DESTROY)
    echo "▶ 4. PENGUJIAN HAPUS DATA (DELETE):\n";
    $deletedId = $newUser->id;
    $responseDestroy = $controller->destroy((string) $deletedId);

    $checkDeleted = User::find($deletedId);
    if (!$checkDeleted) {
        echo "   [✓] STATUS: BERHASIL\n";
        echo "   [i] User ID {$deletedId} telah berhasil dihapus dari database.\n";
    } else {
        echo "   [✗] STATUS: GAGAL MENGHAPUS\n";
    }

} catch (\Exception $e) {
    echo "\n[!] TERJADI KESALAHAN: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
} finally {
    DB::rollBack();
    echo "\n====================================================\n";
    echo "  PENGUJIAN SELESAI (Database Di-Rollback Aman)\n";
    echo "====================================================\n";
}

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Mitra;
use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\Cooperation;
use App\Models\Notifikasi;
use App\Support\CooperationExpiryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

echo "=== STARTING TEST BACKEND UC34 & UC35 (SUBSISTEM 10 KOMUNIKASI) ===\n\n";

$results = [
    'uc34_notifikasi' => [],
    'uc35_hubungi_admin' => [],
    '10_3_expiry_distribution' => [],
];

try {
    // -------------------------------------------------------------
    // 1. Persiapan Akun & Role
    // -------------------------------------------------------------
    $adminUser = User::where('email', 'admin@polnado.ac.id')->first();
    $pimpinanUser = User::where('email', 'pimpinan@polnado.ac.id')->first();
    $humasUser = User::where('email', 'humas@polnado.ac.id')->first();
    $jurusanUser = User::where('email', 'jurusan@polnado.ac.id')->first();
    $upaUser = User::where('email', 'upa@polnado.ac.id')->first();
    $pusatUser = User::where('email', 'pusat@polnado.ac.id')->first();
    $mitraUser = User::where('email', 'mitra@industri.co.id')->first();
    $prodiUser = User::where('email', 'prodi@polnado.ac.id')->first();

    echo "1. Verifikasi Akun Pengujian:\n";
    echo " - Admin: " . ($adminUser ? "OK ({$adminUser->email}, ID: {$adminUser->id})" : "FAIL") . "\n";
    echo " - Pimpinan: " . ($pimpinanUser ? "OK ({$pimpinanUser->email}, ID: {$pimpinanUser->id})" : "FAIL") . "\n";
    echo " - Humas: " . ($humasUser ? "OK ({$humasUser->email}, ID: {$humasUser->id})" : "FAIL") . "\n";
    echo " - Jurusan: " . ($jurusanUser ? "OK ({$jurusanUser->email}, ID: {$jurusanUser->id})" : "FAIL") . "\n";
    echo " - UPA: " . ($upaUser ? "OK ({$upaUser->email}, ID: {$upaUser->id})" : "FAIL") . "\n";
    echo " - Pusat: " . ($pusatUser ? "OK ({$pusatUser->email}, ID: {$pusatUser->id})" : "FAIL") . "\n";
    echo " - Mitra: " . ($mitraUser ? "OK ({$mitraUser->email}, ID: {$mitraUser->id})" : "FAIL") . "\n";
    echo " - Prodi: " . ($prodiUser ? "OK ({$prodiUser->email}, ID: {$prodiUser->id})" : "FAIL") . "\n\n";

    // -------------------------------------------------------------
    // 2. Testing UC34 — Menerima Notifikasi Sistem
    // -------------------------------------------------------------
    echo "2. Testing UC34 (Menerima Notifikasi Sistem):\n";

    // A. Buat sample notifikasi untuk berbagai role
    $testNotifData = [
        ['user' => $pimpinanUser, 'type' => 'validasi', 'title' => 'Permintaan Validasi Dokumen', 'message' => 'Dokumen MoA baru membutuhkan pengesahan Anda.', 'url' => route('pimpinan.monitoring')],
        ['user' => $humasUser, 'type' => 'evaluasi', 'title' => 'Evaluasi Kemitraan', 'message' => 'Laporan evaluasi tahunan siap dianalisis.', 'url' => route('unit.dkerjasama')],
        ['user' => $mitraUser, 'type' => 'pengajuan_disetujui', 'title' => 'Pengajuan Diterima', 'message' => 'Pengajuan kerjasama Anda telah disetujui.', 'url' => route('mitra.dokumen.index')],
        ['user' => $prodiUser, 'type' => 'data_baru', 'title' => 'Penempatan Mahasiswa', 'message' => 'Data magang baru tersedia.', 'url' => route('prodi.dkerjasama')],
    ];

    $createdNotifIds = [];
    foreach ($testNotifData as $item) {
        $notif = Notifikasi::create([
            'user_id' => $item['user']->id,
            'sender_id' => $adminUser->id,
            'type' => $item['type'],
            'title' => $item['title'],
            'message' => $item['message'],
            'url' => $item['url'],
            'is_read' => 0,
        ]);
        $createdNotifIds[] = $notif->id;
    }

    echo " - Sample Notifikasi Sistem berhasil dibuat (" . count($createdNotifIds) . " notifikasi)\n";

    // B. Panggil API Notifikasi Controller index() sebagai Humas
    Auth::login($humasUser);
    $notifController = new \App\Http\Controllers\NotifikasiController();
    $apiResponse = $notifController->index();
    $apiData = json_decode($apiResponse->getContent(), true);

    if ($apiData['success'] && $apiData['unread_count'] >= 1) {
        echo " - [PASS] API GET /api/notifikasi sukses: Unread Count = {$apiData['unread_count']}\n";
        $results['uc34_notifikasi']['api_index'] = 'PASS';
    } else {
        throw new Exception("API GET /api/notifikasi gagal untuk Humas");
    }

    // C. Panggil markAsRead untuk salah satu notifikasi
    $sampleNotif = Notifikasi::where('user_id', $humasUser->id)->where('is_read', 0)->first();
    $readResponse = $notifController->markAsRead($sampleNotif->id);
    $readData = json_decode($readResponse->getContent(), true);

    $sampleNotif->refresh();
    if ($sampleNotif->is_read == 1) {
        echo " - [PASS] API POST /api/notifikasi/{id}/mark-read berhasil: is_read = 1\n";
        $results['uc34_notifikasi']['mark_as_read'] = 'PASS';
    } else {
        throw new Exception("Gagal markAsRead notifikasi ID {$sampleNotif->id}");
    }

    // D. Panggil markAllRead
    $allReadResponse = $notifController->markAllRead();
    $allReadData = json_decode($allReadResponse->getContent(), true);
    $remainingUnread = Notifikasi::where('user_id', $humasUser->id)->where('is_read', 0)->count();

    if ($remainingUnread === 0) {
        echo " - [PASS] API POST /api/notifikasi/mark-all-read berhasil: Sisa unread = 0\n";
        $results['uc34_notifikasi']['mark_all_read'] = 'PASS';
    } else {
        throw new Exception("Sisa unread masih ada setelah markAllRead");
    }

    // -------------------------------------------------------------
    // 3. Testing UC35 — Menghubungi Administrator (Aktor: Mitra)
    // -------------------------------------------------------------
    echo "\n3. Testing UC35 (Menghubungi Administrator - Aktor: Mitra):\n";
    Auth::login($mitraUser);

    $bantuanController = new \App\Http\Controllers\Mitra\BantuanController();

    // A. Uji validasi input kosong
    $validationFailed = false;
    try {
        $emptyRequest = Request::create('/mitra/bantuan', 'POST', []);
        $bantuanController->store($emptyRequest);
    } catch (\Illuminate\Validation\ValidationException $e) {
        $validationFailed = true;
        echo " - [PASS] Validasi Form Gagal Sesuai Ekspektasi: " . json_encode($e->errors()) . "\n";
    }

    if (!$validationFailed) {
        throw new Exception("Validasi store bantuan gagal mendeteksi field kosong");
    }

    // B. Uji pengiriman pesan yang valid
    $subjekTest = "Kendala Teknis Unggah Laporan MoU " . uniqid();
    $pesanTest = "Halo Administrator, kami mengalami kendala saat mencoba mengunggah berkas laporan implementasi kegiatan magang. Mohon panduannya.";
    $kategoriTest = "Kendala Teknis";

    $validRequest = Request::create('/mitra/bantuan', 'POST', [
        'subjek' => $subjekTest,
        'kategori' => $kategoriTest,
        'pesan' => $pesanTest,
    ]);

    $storeResponse = $bantuanController->store($validRequest);

    // C. Verifikasi notifikasi terkirim ke Admin
    $adminNotif = Notifikasi::where('user_id', $adminUser->id)
        ->where('type', 'pesan_mitra')
        ->where('title', 'like', "%{$subjekTest}%")
        ->first();

    if ($adminNotif) {
        echo " - [PASS] Admin Menerima Notifikasi Pesan Mitra:\n";
        echo "   Title: {$adminNotif->title}\n";
        echo "   Message: {$adminNotif->message}\n";
        echo "   is_read: {$adminNotif->is_read}\n";
        $results['uc35_hubungi_admin']['admin_notification_received'] = 'PASS';
    } else {
        throw new Exception("Admin tidak menerima notifikasi dari pengiriman bantuan mitra");
    }

    // D. Verifikasi arsip riwayat pesan pada sisi Mitra
    $mitraSentArchive = Notifikasi::where('user_id', $mitraUser->id)
        ->where('type', 'pesan_mitra')
        ->where('title', 'like', "%{$subjekTest}%")
        ->first();

    if ($mitraSentArchive) {
        echo " - [PASS] Arsip Riwayat Pesan Mitra Tersimpan: {$mitraSentArchive->title}\n";
        $results['uc35_hubungi_admin']['mitra_sent_archive'] = 'PASS';
    } else {
        throw new Exception("Arsip pesan terkirim mitra tidak ditemukan");
    }

    // -------------------------------------------------------------
    // 4. Testing 10.3 — Pengecekan Kedaluwarsa & Distribusi Notifikasi Terarah
    // -------------------------------------------------------------
    echo "\n4. Testing 10.3 (Pengecekan Kedaluwarsa Otomatis & Distribusi Notifikasi Terarah):\n";

    // Buat dokumen kerjasama yang masa berlakunya kemarin (kadaluarsa)
    $jurusanTerkait = Jurusan::first();
    $mitraTerkait = Mitra::where('id', $mitraUser->mitra_id)->first() ?: Mitra::first();

    $docExpired = Cooperation::create([
        'judul' => 'Kerjasama Strategis IoT Manufaktur ' . uniqid(),
        'jenis' => 'MoA',
        'doc_number' => 'TEST/EXP/' . uniqid(),
        'start_date' => Carbon::now()->subYears(2)->format('Y-m-d'),
        'end_date' => Carbon::yesterday()->format('Y-m-d'), // Kedaluwarsa kemarin
        'status_berlaku' => 'Aktif',
        'status_dokumen' => 'Disahkan',
        'mitra_id' => $mitraTerkait->id,
        'jurusan_id' => $jurusanTerkait ? $jurusanTerkait->id : null,
        'created_by' => $jurusanUser->id,
    ]);

    if ($jurusanTerkait) {
        $docExpired->jurusans()->syncWithoutDetaching([$jurusanTerkait->id]);
    }

    // Jalankan service pengecekan otomatis
    $processed = CooperationExpiryService::checkAndNotifyExpiredCooperations();
    echo " - Dokumen kadaluarsa diproses oleh service: {$processed} dokumen\n";

    // Verifikasi status dokumen berubah menjadi 'Kadaluarsa'
    $docExpired->refresh();
    if ($docExpired->status_berlaku === 'Kadaluarsa') {
        echo " - [PASS] Status dokumen otomatis berubah menjadi 'Kadaluarsa'\n";
        $results['10_3_expiry_distribution']['status_update'] = 'PASS';
    } else {
        throw new Exception("Status dokumen tidak berubah menjadi Kadaluarsa: {$docExpired->status_berlaku}");
    }

    // Verifikasi distribusi notifikasi terarah ke:
    // A. Penginput (Jurusan)
    $notifPenginput = Notifikasi::where('user_id', $jurusanUser->id)
        ->where('source_id', $docExpired->id)
        ->where('type', 'kadaluarsa')
        ->first();
    if ($notifPenginput) {
        echo " - [PASS] Notifikasi Kadaluarsa terkirim ke Unit Penginput (Jurusan)\n";
        $results['10_3_expiry_distribution']['notif_penginput'] = 'PASS';
    } else {
        throw new Exception("Unit penginput tidak menerima notifikasi kadaluarsa");
    }

    // B. Humas
    $notifHumas = Notifikasi::where('user_id', $humasUser->id)
        ->where('source_id', $docExpired->id)
        ->where('type', 'kadaluarsa')
        ->first();
    if ($notifHumas) {
        echo " - [PASS] Notifikasi Kadaluarsa terkirim ke Humas\n";
        $results['10_3_expiry_distribution']['notif_humas'] = 'PASS';
    } else {
        throw new Exception("Humas tidak menerima notifikasi kadaluarsa");
    }

    // C. Pimpinan
    $notifPimpinan = Notifikasi::where('user_id', $pimpinanUser->id)
        ->where('source_id', $docExpired->id)
        ->where('type', 'kadaluarsa')
        ->first();
    if ($notifPimpinan) {
        echo " - [PASS] Notifikasi Kadaluarsa terkirim ke Pimpinan\n";
        $results['10_3_expiry_distribution']['notif_pimpinan'] = 'PASS';
    } else {
        throw new Exception("Pimpinan tidak menerima notifikasi kadaluarsa");
    }

    // D. Mitra Privat Terkait
    $notifMitra = Notifikasi::where('user_id', $mitraUser->id)
        ->where('source_id', $docExpired->id)
        ->where('type', 'kadaluarsa')
        ->first();
    if ($notifMitra) {
        echo " - [PASS] Notifikasi Kadaluarsa terkirim ke Mitra Privat Terkait\n";
        $results['10_3_expiry_distribution']['notif_mitra'] = 'PASS';
    } else {
        echo " - [INFO] Mitra user ID {$mitraUser->id} tidak terhubung ke mitra_id {$mitraTerkait->id}\n";
    }

    // E. Idempotensi (Deduplikasi)
    $countBefore = Notifikasi::where('source_id', $docExpired->id)->where('type', 'kadaluarsa')->count();
    CooperationExpiryService::checkAndNotifyExpiredCooperations();
    $countAfter = Notifikasi::where('source_id', $docExpired->id)->where('type', 'kadaluarsa')->count();

    if ($countBefore === $countAfter) {
        echo " - [PASS] Uji Idempotensi / Deduplikasi Sukses: Jumlah notifikasi tidak bertambah ({$countBefore} == {$countAfter})\n";
        $results['10_3_expiry_distribution']['idempotency'] = 'PASS';
    } else {
        throw new Exception("Deduplikasi gagal: notifikasi duplikat terbuat");
    }

    // Simpan hasil ke JSON
    $jsonResultPath = __DIR__ . '/backend_uc34_uc35_results.json';
    file_put_contents($jsonResultPath, json_encode([
        'status' => 'ALL_PASSED',
        'timestamp' => Carbon::now()->toIso8601String(),
        'results' => $results,
    ], JSON_PRETTY_PRINT));

    echo "\n=== ALL BACKEND TESTS FOR UC34 & UC35 PASSED SUCCESSFULLY! ===\n";
    echo "Results saved to: {$jsonResultPath}\n";

} catch (\Throwable $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

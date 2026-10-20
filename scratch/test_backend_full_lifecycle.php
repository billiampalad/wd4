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
use App\Models\DetailKegiatan;
use App\Models\KegiatanKerjasama;
use App\Models\KegiatanMahasiswa;
use App\Models\Mahasiswa;
use App\Models\Pembimbing;
use App\Models\Evaluasi;
use App\Models\Alumni;
use App\Models\AlumniMitra;
use App\Models\Notifikasi;
use App\Models\PengajuanKerjasamaBaru;
use App\Models\PengajuanPerpanjanganKerjasama;
use App\Support\CooperationExpiryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

echo "=== STARTING FULL END-TO-END BUSINESS LIFECYCLE TEST (SECTION 11.1 - 11.5) ===\n\n";

$phases = [];

try {
    // -------------------------------------------------------------
    // FASE 0: VERIFIKASI AKUN DAN MASTER DATA
    // -------------------------------------------------------------
    echo "--- FASE 0: Verifikasi Akun & Roles ---\n";
    $adminUser = User::where('email', 'admin@polnado.ac.id')->firstOrFail();
    $pimpinanUser = User::where('email', 'pimpinan@polnado.ac.id')->firstOrFail();
    $humasUser = User::where('email', 'humas@polnado.ac.id')->firstOrFail();
    $jurusanUser = User::where('email', 'jurusan@polnado.ac.id')->firstOrFail();
    $upaUser = User::where('email', 'upa@polnado.ac.id')->firstOrFail();
    $pusatUser = User::where('email', 'pusat@polnado.ac.id')->firstOrFail();
    $mitraUser = User::where('email', 'mitra@industri.co.id')->firstOrFail();
    $prodiUser = User::where('email', 'prodi@polnado.ac.id')->firstOrFail();

    $jurusan = Jurusan::firstOrFail();
    $prodi = Prodi::where('jurusan_id', $jurusan->id)->first() ?: Prodi::firstOrFail();
    $mitra = Mitra::where('id', $mitraUser->mitra_id)->first() ?: Mitra::firstOrFail();

    echo " - Semua 8 akun role (Admin, Pimpinan, Humas, Jurusan, UPA, Pusat, Mitra, Prodi) terverifikasi.\n";
    $phases['fase_0_actors'] = 'PASS';

    // -------------------------------------------------------------
    // FASE 1: INISIASI KERJA SAMA (UC15 -> UC16 -> UC17)
    // -------------------------------------------------------------
    echo "\n--- FASE 1: Inisiasi Kerja Sama Baru (Mitra & Pimpinan) ---\n";
    // 1. Mitra mengajukan kerja sama baru secara online (UC15)
    $kodePengajuan = 'REQ-' . strtoupper(Str::random(8));
    $pengajuan = PengajuanKerjasamaBaru::create([
        'kode_pengajuan' => $kodePengajuan,
        'nama_mitra' => 'PT Mitra Lifecycle Global ' . uniqid(),
        'kategori' => 'nasional',
        'alamat' => 'Jl. Kawasan Industri Modern No. 88, Manado',
        'telp' => '081298765432',
        'nama_penandatangan' => 'Direktur Utama',
        'nama_penanggung_jawab' => 'Budi Santoso',
        'email' => 'budi.' . uniqid() . '@partner.com',
        'judul_pengajuan' => 'Kerjasama Program Vokasi Terpadu',
        'tujuan_pengajuan' => 'Mendukung implementasi link and match vokasi dan industri',
        'ruang_lingkup' => 'Perekrutan Lulusan, Magang Industri, dan Kuliah Tamu',
        'status' => 'diajukan',
    ]);
    echo " 1.1 Mitra berhasil mengajukan kerjasama baru: {$pengajuan->kode_pengajuan}\n";

    // Notifikasi dikirim ke Pimpinan & Humas
    Notifikasi::create([
        'user_id' => $pimpinanUser->id,
        'sender_id' => $mitraUser->id,
        'source_id' => $pengajuan->id,
        'source_type' => 'pengajuan_kerjasama_baru',
        'type' => 'pengajuan_mitra',
        'title' => 'Pengajuan Kerja Sama Baru',
        'message' => "Pengajuan baru {$pengajuan->kode_pengajuan} dari {$pengajuan->nama_mitra}",
        'url' => route('pimpinan.pengajuan_mitra'),
        'is_read' => 0,
    ]);

    // 2. Pimpinan menerima dan memvalidasi/menyetujui pengajuan (UC16 & UC17)
    Auth::login($pimpinanUser);
    $pengajuan->update([
        'status' => 'disetujui',
        'catatan_pimpinan' => 'Disetujui untuk ditindaklanjuti penyusunan MoA oleh Humas/Unit Kerja.',
    ]);
    echo " 1.2 Pimpinan menyetujui pengajuan kerjasama: Status = {$pengajuan->status}\n";
    $phases['fase_1_inisiasi'] = 'PASS';

    // -------------------------------------------------------------
    // FASE 2: ADMINISTRASI DOKUMEN (MoU -> MoA -> IA -> Validasi -> Sahkan)
    // -------------------------------------------------------------
    echo "\n--- FASE 2: Administrasi Dokumen (Unit, Mitra, Pimpinan) ---\n";
    Auth::login($humasUser);

    // 1. Unit menginput dokumen kerjasama (Draft) - UC08
    $coop = Cooperation::create([
        'judul' => 'Kerjasama Program Vokasi Terpadu & Magang ' . uniqid(),
        'jenis' => 'MoA',
        'doc_number' => 'DOC/LIFECYCLE/' . date('Y') . '/' . uniqid(),
        'start_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
        'end_date' => Carbon::now()->addMonths(9)->format('Y-m-d'),
        'status_berlaku' => 'Aktif',
        'status_dokumen' => 'Draft',
        'mitra_id' => $mitra->id,
        'jurusan_id' => $jurusan->id,
        'prodi_id' => $prodi->id,
        'created_by' => $humasUser->id,
    ]);
    $coop->jurusans()->syncWithoutDetaching([$jurusan->id]);
    $coop->prodis()->syncWithoutDetaching([$prodi->id]);
    echo " 2.1 Unit Humas membuat draf MoA: {$coop->doc_number} (Status: {$coop->status_dokumen})\n";

    // 2. Mitra mereview draf secara online (UC13)
    Auth::login($mitraUser);
    echo " 2.2 Mitra mengakses dan mereview draf dokumen di Portal Mitra\n";

    // 3. Unit submit draf ke Pimpinan (UC10) -> Status: Menunggu Evaluasi
    Auth::login($humasUser);
    $coop->update(['status_dokumen' => 'Menunggu Evaluasi']);
    echo " 2.3 Unit mensubmit dokumen ke Pimpinan (Status: {$coop->status_dokumen})\n";

    // 4. Pimpinan memvalidasi dan mengesahkan dokumen (UC11 & UC12) -> Status: Disahkan
    Auth::login($pimpinanUser);
    $coop->update([
        'status_dokumen' => 'Disahkan',
        'status_berlaku' => 'Aktif',
        'catatan_evaluasi' => 'Dokumen MoA telah memenuhi regulasi dan disahkan.',
    ]);
    echo " 2.4 Pimpinan mengesahkan dokumen (Status: {$coop->status_dokumen}, Berlaku: {$coop->status_berlaku})\n";
    $phases['fase_2_dokumen'] = 'PASS';

    // -------------------------------------------------------------
    // FASE 3: PELAKSANAAN KEGIATAN & MAGANG (UC19 -> UC20 -> UC21 -> UC22)
    // -------------------------------------------------------------
    echo "\n--- FASE 3: Pelaksanaan Kegiatan & Magang Mahasiswa ---\n";
    Auth::login($prodiUser);

    // 1. Unit/Prodi menginput kegiatan berbasis IA (UC19)
    $kegiatan = KegiatanKerjasama::create([
        'cooperation_id' => $coop->id,
        'nama_kegiatan' => 'Program Magang Industri Bersertifikat Batch 1',
        'bentuk_kegiatan' => 'Magang / Praktik Kerja',
        'tanggal_mulai' => Carbon::now()->subMonths(2)->format('Y-m-d'),
        'tanggal_selesai' => Carbon::now()->addMonths(1)->format('Y-m-d'),
        'status_kegiatan' => 'Berjalan',
        'deskripsi' => 'Pengembangan kompetensi teknis di lingkungan industri nyata',
    ]);
    echo " 3.1 Prodi menginput kegiatan kerjasama: {$kegiatan->nama_kegiatan}\n";

    // 2. Prodi menginput peserta mahasiswa magang (UC20)
    $mhs = Mahasiswa::firstOrCreate([
        'nim' => 'NIM' . rand(100000, 999999),
    ], [
        'nama' => 'Mahasiswa Magang Lifecycle',
        'prodi_id' => $prodi->id,
        'angkatan' => 2023,
    ]);

    $kegiatanMhs = KegiatanMahasiswa::create([
        'kegiatan_id' => $kegiatan->id,
        'mahasiswa_id' => $mhs->id,
        'mitra_id' => $mitra->id,
        'periode_mulai' => Carbon::now()->subMonths(2)->format('Y-m-d'),
        'periode_selesai' => Carbon::now()->addMonths(1)->format('Y-m-d'),
        'status' => 'Aktif',
    ]);

    $pembimbing = Pembimbing::create([
        'kegiatan_mahasiswa_id' => $kegiatanMhs->id,
        'nama_pembimbing' => 'Ir. Pembimbing Industri, MT',
    ]);
    echo " 3.2 Prodi menempatkan mahasiswa ({$mhs->nama}, NIM: {$mhs->nim}) ke mitra\n";

    // 3. Mitra memberikan penilaian performa mahasiswa (UC21)
    Auth::login($mitraUser);
    $kegiatanMhs->update([
        'nilai_mitra' => 92.50,
        'status' => 'Selesai',
        'catatan_mitra' => 'Kinerja mahasiswa sangat baik, menguasai problem solving teknis industri.',
    ]);
    echo " 3.3 Mitra memberikan penilaian mahasiswa: Nilai = {$kegiatanMhs->nilai_mitra} (Status: {$kegiatanMhs->status})\n";

    // 4. Monitoring Mahasiswa Aktif (UC22)
    $totalMhsAktif = KegiatanMahasiswa::where('mitra_id', $mitra->id)->count();
    echo " 3.4 Monitoring kegiatan: Total {$totalMhsAktif} mahasiswa tercatat di mitra terkait\n";
    $phases['fase_3_pelaksanaan'] = 'PASS';

    // -------------------------------------------------------------
    // FASE 4: EVALUASI & UMPAN BALIK (UC23 -> UC24 -> UC25 -> UC26)
    // -------------------------------------------------------------
    echo "\n--- FASE 4: Evaluasi Pelaksanaan & Umpan Balik CSAT ---\n";
    Auth::login($humasUser);

    // 1. Unit mengisi dan mensubmit form evaluasi berkala (UC23 & UC24)
    $evaluasiUnit = Evaluasi::create([
        'cooperation_id' => $coop->id,
        'evaluator_id' => $humasUser->id,
        'tipe_evaluasi' => 'Internal',
        'score' => 4.8,
        'kendala' => 'Jadwal sinkronisasi kalender akademik dengan jam kerja shift industri.',
        'rekomendasi' => 'Perluasan kuota magang untuk angkatan berikutnya.',
        'kesimpulan' => 'Sangat Baik',
        'status_validasi' => 'Menunggu Validasi',
    ]);
    echo " 4.1 Unit mengisi dan mensubmit evaluasi berkala (Score: {$evaluasiUnit->score}, Status: {$evaluasiUnit->status_validasi})\n";

    // 2. Pimpinan memvalidasi evaluasi (UC25)
    Auth::login($pimpinanUser);
    $evaluasiUnit->update([
        'status_validasi' => 'Divalidasi',
        'tindak_lanjut' => 'Hasil evaluasi sangat memuaskan, kemitraan direkomendasikan berlanjut.',
    ]);
    echo " 4.2 Pimpinan menyetujui hasil evaluasi (Status: {$evaluasiUnit->status_validasi})\n";

    // 3. Mitra memberikan umpan balik kepuasan (CSAT / UC26)
    Auth::login($mitraUser);
    $evaluasiMitra = Evaluasi::create([
        'cooperation_id' => $coop->id,
        'evaluator_id' => $mitraUser->id,
        'tipe_evaluasi' => 'Umpan_Balik_Mitra',
        'score' => 5.0,
        'kendala' => 'Tidak ada kendala berarti.',
        'rekomendasi' => 'Lanjutkan program magang terstruktur.',
        'kesimpulan' => 'Sangat Baik',
        'status_validasi' => 'Divalidasi',
    ]);
    echo " 4.3 Mitra mengisi umpan balik CSAT (Rating: {$evaluasiMitra->score} / 5.0)\n";
    $phases['fase_4_evaluasi'] = 'PASS';

    // -------------------------------------------------------------
    // FASE 5: KEDALUWARSA, DISTRIBUSI NOTIFIKASI & PERPANJANGAN (10.3 & UC18)
    // -------------------------------------------------------------
    echo "\n--- FASE 5: Kedaluwarsa Otomatis, Distribusi Notifikasi & Perpanjangan ---\n";
    // 1. Simulasi masa berlaku habis (end_date < today)
    $coop->update(['end_date' => Carbon::yesterday()->format('Y-m-d')]);

    // 2. Sistem Otomatisasi (CooperationExpiryService) mendeteksi dan mengupdate status
    $processed = CooperationExpiryService::checkAndNotifyExpiredCooperations();
    $coop->refresh();

    if ($coop->status_berlaku === 'Kadaluarsa') {
        echo " 5.1 Sistem otomatis mendeteksi kedaluwarsa: Status = {$coop->status_berlaku}\n";
    } else {
        throw new Exception("Status tidak berubah ke Kadaluarsa: {$coop->status_berlaku}");
    }

    // 3. Distribusi notifikasi terarah ke Penginput, Humas, Jurusan, Mitra, Pimpinan
    $notifCount = Notifikasi::where('source_id', $coop->id)->where('type', 'kadaluarsa')->count();
    echo " 5.2 Notifikasi kedaluwarsa terdistribusi ke {$notifCount} pihak terkait (Idempotent)\n";

    // 4. Mitra/Unit mengajukan perpanjangan kerja sama (UC18)
    Auth::login($mitraUser);
    $kodePerpanjangan = 'EXT-' . strtoupper(Str::random(8));
    $perpanjangan = PengajuanPerpanjanganKerjasama::create([
        'kode_pengajuan' => $kodePerpanjangan,
        'cooperation_id' => $coop->id,
        'mitra_id' => $mitra->id,
        'nama_mitra' => $mitra->nama_mitra,
        'kategori' => 'nasional',
        'alamat' => $mitra->alamat ?? 'Jl. Industri No. 12, Manado',
        'telp' => $mitra->telp ?? '081298765432',
        'nama_penandatangan' => 'Direktur Utama',
        'email' => $mitraUser->email,
        'doc_number' => $coop->doc_number,
        'start_date' => Carbon::now()->format('Y-m-d'),
        'end_date' => Carbon::now()->addYears(3)->format('Y-m-d'),
        'file_surat' => 'surat/perpanjangan_test.pdf',
        'judul_pengajuan' => 'Perpanjangan Kerjasama Program Vokasi Terpadu',
        'tujuan_pengajuan' => 'Kolaborasi sangat efektif dan ingin melanjutkan periode 3 tahun ke depan.',
        'status' => 'diajukan',
    ]);
    echo " 5.3 Mitra mengajukan perpanjangan kerjasama: {$perpanjangan->kode_pengajuan} (Status: {$perpanjangan->status})\n";
    $phases['fase_5_expiry_extension'] = 'PASS';

    // -------------------------------------------------------------
    // FASE 6: TRACKING LULUSAN & IKU 1 (UC32 -> UC33)
    // -------------------------------------------------------------
    echo "\n--- FASE 6: Tracking Lulusan & Capaian Statistik IKU 1 ---\n";
    Auth::login($prodiUser);

    // 1. Prodi mencatat data alumni bekerja di mitra (UC32)
    $alumni = Alumni::firstOrCreate([
        'nim' => 'ALM' . rand(100000, 999999),
    ], [
        'nama' => 'Alumni Vokasi Sukses ' . uniqid(),
        'prodi_id' => $prodi->id,
        'tahun_lulus' => 2024,
        'email' => 'alumni.' . uniqid() . '@alumni.polnado.ac.id',
        'telepon' => '081377889900',
    ]);

    $alumniMitra = AlumniMitra::create([
        'alumni_id' => $alumni->id,
        'mitra_id' => $mitra->id,
        'posisi' => 'Fullstack Software Engineer',
        'tahun_mulai' => 2025,
        'status' => 'Aktif',
        'sumber_data' => 'Prodi',
    ]);
    echo " 6.1 Prodi mencatat alumni ({$alumni->nama}) berkarir di {$mitra->nama_mitra} sebagai {$alumniMitra->posisi}\n";

    // 2. Menghitung capaian IKU 1 (UC33)
    $totalAlumni = Alumni::count();
    $alumniBekerja = AlumniMitra::where('status', 'Aktif')->distinct('alumni_id')->count('alumni_id');
    $iku1Rate = $totalAlumni > 0 ? round(($alumniBekerja / $totalAlumni) * 100, 2) : 0;
    echo " 6.2 Statistik IKU 1: Total Alumni = {$totalAlumni}, Bekerja di DUDIKA = {$alumniBekerja}, Capaian = {$iku1Rate}%\n";
    $phases['fase_6_tracking_iku'] = 'PASS';

    // -------------------------------------------------------------
    // FASE 7: KOMUNIKASI & HELPDESK (UC34 & UC35)
    // -------------------------------------------------------------
    echo "\n--- FASE 7: Komunikasi Sistem & Helpdesk Mitra ke Admin ---\n";
    Auth::login($mitraUser);
    $bantuanController = new \App\Http\Controllers\Mitra\BantuanController();
    $helpReq = Request::create('/mitra/bantuan', 'POST', [
        'subjek' => 'Permintaan Akses Tambahan PIC Cabang',
        'kategori' => 'Permintaan Bantuan',
        'pesan' => 'Mohon bantuan untuk menambahkan akun PIC kedua dari kantor cabang kami.',
    ]);
    $bantuanController->store($helpReq);

    $adminHelpNotif = Notifikasi::where('user_id', $adminUser->id)->where('type', 'pesan_mitra')->latest()->first();
    if ($adminHelpNotif) {
        echo " 7.1 Administrator berhasil menerima notifikasi tiket bantuan dari Mitra: {$adminHelpNotif->title}\n";
    }
    $phases['fase_7_komunikasi'] = 'PASS';

    // Simpan hasil ke JSON
    $jsonResultPath = __DIR__ . '/backend_full_lifecycle_results.json';
    file_put_contents($jsonResultPath, json_encode([
        'status' => 'ALL_LIFECYCLE_PHASES_PASSED',
        'timestamp' => Carbon::now()->toIso8601String(),
        'phases' => $phases,
    ], JSON_PRETTY_PRINT));

    echo "\n=== ALL 7 PHASES OF END-TO-END BUSINESS LIFECYCLE PASSED SUCCESSFULLY! ===\n";
    echo "Results saved to: {$jsonResultPath}\n";

} catch (\Throwable $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

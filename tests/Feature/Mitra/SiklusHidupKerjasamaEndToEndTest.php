<?php

namespace Tests\Feature\Mitra;

use App\Models\Alumni;
use App\Models\AlumniMitra;
use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\Jurusan;
use App\Models\KegiatanKerjasama;
use App\Models\KegiatanMahasiswa;
use App\Models\Klasifikasi;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\PengajuanKerjasamaBaru;
use App\Models\PengajuanPerpanjanganKerjasama;
use App\Models\Prodi;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiklusHidupKerjasamaEndToEndTest extends TestCase
{
    use DatabaseTransactions;

    protected $rolePimpinan;
    protected $roleUnit;
    protected $roleJurusan;
    protected $roleProdi;
    protected $roleMitra;

    protected $pimpinanUser;
    protected $jurusanUser;
    protected $prodiUser;
    protected $jurusan;
    protected $prodi;
    protected $klasifikasi;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Roles
        $this->rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        $this->roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $this->roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $this->roleProdi = Role::firstOrCreate(['name' => 'prodi'], ['guard_name' => 'web']);
        $this->roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);

        $this->klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Teknologi & Manufaktur']);

        // 2. Setup Pimpinan
        $this->pimpinanUser = User::factory()->create([
            'role_id' => $this->rolePimpinan->id,
            'name' => 'Direktur Polimdo',
            'email' => 'direktur.e2e@polimdo.ac.id',
        ]);
        Profile::firstOrCreate(['user_id' => $this->pimpinanUser->id], ['nip' => '197001011995011001']);

        // 3. Setup Jurusan & Prodi
        $this->jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Jurusan Teknik Elektro']);
        $this->prodi = Prodi::firstOrCreate(['nama_prodi' => 'D4 Teknik Informatika'], [
            'jurusan_id' => $this->jurusan->id,
        ]);

        $this->jurusanUser = User::factory()->create([
            'role_id' => $this->roleJurusan->id,
            'name' => 'Kajur Elektro E2E',
            'email' => 'kajur.elektro.e2e@polimdo.ac.id',
        ]);
        Profile::firstOrCreate(['user_id' => $this->jurusanUser->id], [
            'jurusan_id' => $this->jurusan->id,
            'nip' => '197501012000031002',
        ]);

        $this->prodiUser = User::factory()->create([
            'role_id' => $this->roleProdi->id,
            'name' => 'Kaprodi TI E2E',
            'email' => 'kaprodi.ti.e2e@polimdo.ac.id',
        ]);
        Profile::firstOrCreate(['user_id' => $this->prodiUser->id], [
            'prodi_id' => $this->prodi->id,
            'jurusan_id' => $this->jurusan->id,
            'nip' => '198801012015041003',
        ]);
    }

    public function test_full_continuous_cooperation_lifecycle_end_to_end()
    {
        Storage::fake('public');

        // =========================================================================
        // FASE 1: INISIASI (UC15 -> UC16 -> UC17)
        // =========================================================================
        
        // 1.1 Mitra mengajukan permohonan kerja sama baru (UC15)
        $mitraEmail = 'pic.schneider.e2e@schneider.com';
        $submissionResponse = $this->post(route('pengajuan.kerjasama.store'), [
            'nama_mitra' => 'PT Schneider Electric Manufacturing',
            'id_klasifikasi' => $this->klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Kawasan Industri BIP, Batam',
            'telp' => '08117788990',
            'website' => 'https://www.se.com',
            'nama_penandatangan' => 'Ir. Michael Santoso',
            'jabatan_penandatangan' => 'HR & Industrial Relations Director',
            'nama_penanggung_jawab' => 'Dwi Prasetyo',
            'jabatan_penanggung_jawab' => 'Talent Acquisition Manager',
            'email' => $mitraEmail,
            'judul_pengajuan' => 'Kerja Sama Smart Factory Lab & Program Magang Bersertifikat',
            'tujuan_pengajuan' => 'Peningkatan kompetensi automasi industri dan penyerapan lulusan.',
            'ruang_lingkup' => 'Penyelenggaraan magang industri, kurikulum bersama, dan sertifikasi keahlian.',
            'pesan_tambahan' => 'Kami siap memulai penempatan batch pertama segera.',
        ]);

        $submissionResponse->assertRedirect(route('pengajuan.kerjasama.create'));
        $submissionResponse->assertSessionHas('success');

        $pengajuan = PengajuanKerjasamaBaru::where('email', $mitraEmail)->firstOrFail();
        $this->assertEquals(PengajuanKerjasamaBaru::STATUS_DIAJUKAN, $pengajuan->status);

        // 1.2 Pimpinan menerima pengajuan & melihat di daftar pengajuan masuk (UC16)
        $pimpinanInbox = $this->actingAs($this->pimpinanUser)->get(route('pimpinan.pengajuan_mitra'));
        $pimpinanInbox->assertStatus(200);
        $pimpinanInbox->assertSee('PT Schneider Electric Manufacturing');

        // 1.3 Pimpinan memvalidasi dan menyetujui pengajuan (UC17)
        $approvalResponse = $this->actingAs($this->pimpinanUser)->post(route('pimpinan.pengajuan_mitra.review', $pengajuan->id), [
            'keputusan' => 'disetujui',
            'catatan_pimpinan' => 'Pengajuan sangat strategis dan disetujui untuk diproses ke draf naskah.',
        ]);

        $approvalResponse->assertRedirect(route('pimpinan.pengajuan_mitra'));
        $pengajuan->refresh();
        $this->assertEquals('disetujui', $pengajuan->status);

        $mitra = Mitra::where('nama_mitra', 'PT Schneider Electric Manufacturing')->firstOrFail();
        $mitraUser = User::where('email', $mitraEmail)->firstOrFail();

        // =========================================================================
        // FASE 2: ADMINISTRASI DOKUMEN (UC08 -> UC13 -> UC10 -> UC11/12)
        // =========================================================================

        // 2.1 Jurusan menginput dokumen IA kerja sama magang (UC08)
        $docNumber = 'IA/EL/SCH/' . rand(1000, 9999);
        $createDocResponse = $this->actingAs($this->jurusanUser)->post(route('jurusan.kerjasama.store'), [
            'title' => 'IA Program Magang Smart Factory Schneider - Polimdo',
            'jenis' => 'IA (Implementation Agreement)',
            'doc_number' => $docNumber,
            'start_date' => now()->startOfYear()->format('Y-m-d'),
            'end_date' => now()->addYears(2)->format('Y-m-d'),
            'description' => 'Implementasi penempatan magang mahasiswa prodi D4 Teknik Informatika',
            'tipe_pelaksana' => 'jurusan',
            'pelaksana_jurusan_ids' => [$this->jurusan->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Ir. Michael Santoso',
                    'jabatan_penandatangan' => 'HR Director',
                    'nama_pj' => 'Dwi Prasetyo',
                    'jabatan_pj' => 'Talent Acquisition Manager',
                ]
            ],
            'nama_penandatangan' => 'Kajur Elektro E2E',
            'jabatan_penandatangan' => 'Ketua Jurusan',
            'document_link' => 'https://drive.google.com/ia-schneider-polimdo',
        ]);

        $createDocResponse->assertRedirect(route('jurusan.dkerjasama'));

        $coop = Cooperation::where('doc_number', $docNumber)->firstOrFail();
        $this->assertEquals('Draft', $coop->status_dokumen);

        // 2.2 Mitra mereview draf online dan menyetujuinya (UC13)
        $mitraReviewResponse = $this->actingAs($mitraUser)->post(route('mitra.dokumen.review', $coop->id), [
            'status_review' => 'Disetujui',
            'catatan_review' => 'Draf IA telah sesuai dengan kesepakatan ruang lingkup industri.',
        ]);

        $mitraReviewResponse->assertRedirect();

        // 2.3 Jurusan mensubmit dokumen ke Pimpinan (UC10)
        $submitResponse = $this->actingAs($this->jurusanUser)->post(route('jurusan.kerjasama.submit', $coop->id));
        $submitResponse->assertRedirect();

        $coop->refresh();
        $this->assertEquals('Menunggu Evaluasi', $coop->status_dokumen);

        // 2.4 Pimpinan memvalidasi dan mengesahkan dokumen kerja sama (UC11 & UC12)
        $pimpinanApproveDoc = $this->actingAs($this->pimpinanUser)->post(route('pimpinan.evaluate', $coop->id), [
            'status_validasi' => 'layak',
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 5,
            'efisiensi' => 5,
            'kepuasan' => 5,
            'ringkasan' => 'Naskah IA sangat baik dan memenuhi standar kerja sama industri.',
            'saran' => 'Segera laksanakan penempatan mahasiswa magang batch 1.',
            'tindak_lanjut' => 'Lanjutkan ke fase pelaksanaan kegiatan.',
        ]);

        $pimpinanApproveDoc->assertRedirect(route('pimpinan.evaluasi'));
        $coop->refresh();
        $this->assertEquals('Disahkan', $coop->status_dokumen);
        $this->assertEquals('Aktif', $coop->status_berlaku);

        // =========================================================================
        // FASE 3: PELAKSANAAN (UC19 -> UC20 -> UC21 -> UC22)
        // =========================================================================

        // 3.1 Unit/Prodi menginput kegiatan kerja sama (UC19)
        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Magang Smart Factory Schneider Batch 1',
            'periode_mulai' => now()->startOfYear()->format('Y-m-d'),
            'periode_selesai' => now()->addMonths(6)->format('Y-m-d'),
            'status' => 'Perencanaan',
        ]);

        // 3.2 Prodi menginput peserta mahasiswa magang (UC20)
        $mhs = Mahasiswa::firstOrCreate(['nim' => '23024099'], [
            'nama' => 'Budi Santoso',
            'prodi_id' => $this->prodi->id,
            'angkatan' => 2023,
            'status' => 'Aktif',
        ]);

        $penempatan = KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => now()->startOfYear()->format('Y-m-d'),
            'periode_selesai' => now()->addMonths(6)->format('Y-m-d'),
            'status' => 'Aktif',
        ]);

        // 3.3 Mitra memberi penilaian performa mahasiswa (UC21)
        $mitraGradeResponse = $this->actingAs($mitraUser)->put(route('mitra.penilaian.update', $penempatan->id), [
            'nilai_mitra' => 95.0,
            'catatan_mitra' => 'Mahasiswa menunjukkan etos kerja luar biasa dalam automasi SCADA.',
        ]);

        $mitraGradeResponse->assertRedirect(route('mitra.penilaian.index'));
        $penempatan->refresh();
        $this->assertEquals(95.0, (float) $penempatan->nilai_mitra);
        $this->assertEquals('Selesai', $penempatan->status);

        // 3.4 Monitoring Mahasiswa Aktif (UC22)
        $prodiMonitorResponse = $this->actingAs($this->prodiUser)->get(route('prodi.penempatan.index'));
        $prodiMonitorResponse->assertStatus(200);
        $prodiMonitorResponse->assertSee('Budi Santoso');

        // =========================================================================
        // FASE 4: EVALUASI & UMPAN BALIK (UC23 -> UC24 -> UC25 -> UC26)
        // =========================================================================

        // 4.1 Jurusan mengisi form evaluasi kerja sama (UC23)
        $evaluasiResponse = $this->actingAs($this->jurusanUser)->post(route('jurusan.evaluasi.store', $coop->id), [
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 5,
            'efisiensi' => 4,
            'kepuasan' => 5,
            'catatan' => 'Kerja sama magang sangat berdampak positif bagi IKU prodi dan penyerapan mahasiswa.',
        ]);

        $evaluasiResponse->assertRedirect(route('jurusan.evaluasi'));
        $evaluasi = Evaluasi::where('cooperation_id', $coop->id)->firstOrFail();

        // 4.2 Jurusan mensubmit evaluasi ke Pimpinan (UC24)
        $submitEvalResponse = $this->actingAs($this->jurusanUser)->post(route('jurusan.evaluasi.submit', $coop->id));
        $submitEvalResponse->assertRedirect(route('jurusan.evaluasi'));

        // 4.3 Pimpinan memvalidasi evaluasi (UC25)
        $pimpinanValidateEval = $this->actingAs($this->pimpinanUser)->post(route('pimpinan.evaluate', $coop->id), [
            'status_validasi' => 'layak',
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 5,
            'efisiensi' => 5,
            'kepuasan' => 5,
            'ringkasan' => 'Pelaksanaan program automasi Schneider sangat optimal.',
            'saran' => 'Pertahankan dan tingkatkan kuota peserta tahun depan.',
            'tindak_lanjut' => 'Lanjutkan implementasi kegiatan.',
        ]);

        $pimpinanValidateEval->assertRedirect(route('pimpinan.evaluasi'));
        $evaluasi->refresh();
        $this->assertEquals('Divalidasi', $evaluasi->status_validasi);

        // 4.4 Mitra memberi umpan balik kerja sama (UC26)
        $feedbackResponse = $this->actingAs($mitraUser)->post(route('mitra.umpan_balik.store'), [
            'cooperation_id' => $coop->id,
            'kepuasan' => 5,
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 5,
            'efisiensi' => 5,
            'ringkasan' => 'Pelaksanaan program magang industri berlangsung sangat tertib dan mahasiswa memiliki etos kerja tinggi.',
            'rekomendasi' => 'Pertahankan program magang bersertifikat industri ini.',
            'kesimpulan' => 'Sangat Baik',
            'tindak_lanjut' => 'Bersedia Melanjutkan Kerjasama',
        ]);

        $feedbackResponse->assertRedirect(route('mitra.umpan_balik.index'));
        $this->assertDatabaseHas('evaluasis', [
            'cooperation_id' => $coop->id,
            'tipe_evaluasi' => 'Umpan_Balik_Mitra',
            'kepuasan' => 5,
        ]);

        // =========================================================================
        // FASE 5: PERPANJANGAN KERJA SAMA (UC18)
        // =========================================================================

        // 5.1 Mitra mengajukan perpanjangan kerja sama (UC18)
        $fileSurat = UploadedFile::fake()->create('surat_perpanjangan_schneider.pdf', 300, 'application/pdf');

        $extensionResponse = $this->actingAs($mitraUser)->post(route('pengajuan.perpanjangan.store'), [
            'mitra_id' => $mitra->id,
            'jenis' => 'IA (Implementation Agreement)',
            'doc_number' => $docNumber,
            'nama_penandatangan' => 'Ir. Michael Santoso',
            'jabatan_penandatangan' => 'HR Director',
            'nama_penanggung_jawab' => 'Dwi Prasetyo',
            'jabatan_penanggung_jawab' => 'Manager HR',
            'email' => $mitraEmail,
            'telp' => '08117788990',
            'start_date' => now()->addYears(2)->format('Y-m-d'),
            'end_date' => now()->addYears(5)->format('Y-m-d'),
            'judul_pengajuan' => 'Perpanjangan IA Magang Smart Factory Schneider Batch 2',
            'tujuan_pengajuan' => 'Melanjutkan ekspansi program magang dan kurikulum bersama.',
            'ruang_lingkup' => 'Penyelenggaraan kelas industri dan beasiswa mahasiswa berprestasi.',
            'pesan_tambahan' => 'Dokumen draf baru sudah kami lampirkan dalam surat permohonan.',
            'file_surat' => $fileSurat,
        ]);

        $extensionResponse->assertRedirect(route('pengajuan.perpanjangan.create'));
        $this->assertDatabaseHas('pengajuan_perpanjangan_kerjasama', [
            'mitra_id' => $mitra->id,
            'doc_number' => $docNumber,
            'status' => PengajuanPerpanjanganKerjasama::STATUS_DIAJUKAN,
            'judul_pengajuan' => 'Perpanjangan IA Magang Smart Factory Schneider Batch 2',
        ]);

        // =========================================================================
        // FASE 6: TRACKING LULUSAN & ALUMNI (UC32 -> UC33)
        // =========================================================================

        // 6.1 Input data alumni/lulusan bekerja di mitra (UC32)
        $alumni = Alumni::firstOrCreate(['nim' => '20024001'], [
            'nama' => 'Randi Pangalila, S.Tr.Kom',
            'prodi_id' => $this->prodi->id,
            'tahun_lulus' => 2024,
            'email' => 'randi.alumni@gmail.com',
            'no_hp' => '081234567890',
        ]);

        AlumniMitra::create([
            'alumni_id' => $alumni->id,
            'mitra_id' => $mitra->id,
            'posisi' => 'Automation Engineer',
            'tahun_mulai' => 2025,
            'status' => 'Aktif',
        ]);

        // 6.2 Verifikasi statistik penyerapan di Dashboard Prodi & Dashboard Mitra (UC33)
        $prodiDash = $this->actingAs($this->prodiUser)->get(route('prodi.dashboard'));
        $prodiDash->assertStatus(200);
        $prodiDash->assertViewHas('totalAlumni');
        $prodiDash->assertViewHas('alumniBekerja');

        $mitraDash = $this->actingAs($mitraUser)->get(route('mitra.dashboard'));
        $mitraDash->assertStatus(200);
        $mitraDash->assertViewHas('alumniTerserap');
        $this->assertGreaterThanOrEqual(1, $mitraDash->viewData('alumniTerserap'));
    }
}

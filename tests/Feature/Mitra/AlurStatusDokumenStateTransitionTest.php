<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\Jurusan;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use App\Models\PengajuanPerpanjanganKerjasama;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlurStatusDokumenStateTransitionTest extends TestCase
{
    use DatabaseTransactions;

    protected $rolePimpinan;
    protected $roleJurusan;
    protected $roleMitra;

    protected $pimpinanUser;
    protected $jurusanUser;
    protected $mitraUser;
    protected $jurusan;
    protected $mitra;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        $this->roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $this->roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);

        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Otomasi & Robotika']);

        $this->pimpinanUser = User::factory()->create([
            'role_id' => $this->rolePimpinan->id,
            'name' => 'Direktur Polimdo State Test',
            'email' => 'direktur.state@polimdo.ac.id',
        ]);
        Profile::firstOrCreate(['user_id' => $this->pimpinanUser->id], ['nip' => '197101011996011001']);

        $this->jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Jurusan Teknik Mesin State']);

        $this->jurusanUser = User::factory()->create([
            'role_id' => $this->roleJurusan->id,
            'name' => 'Kajur Mesin State',
            'email' => 'kajur.mesin.state@polimdo.ac.id',
        ]);
        Profile::firstOrCreate(['user_id' => $this->jurusanUser->id], [
            'jurusan_id' => $this->jurusan->id,
            'nip' => '197601012001031002',
        ]);

        $this->mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT ABB Sakti Industri'], [
            'klasifikasi_id' => $klasifikasi->id,
            'status_akses' => 'Aktif',
            'telepon' => '021-888999',
        ]);

        $this->mitraUser = User::factory()->create([
            'role_id' => $this->roleMitra->id,
            'mitra_id' => $this->mitra->id,
            'name' => 'PIC ABB Sakti',
            'email' => 'pic.abb@partner.com',
        ]);
    }

    /**
     * State Transition 1: [*] -> Draft (Input Dokumen UC08)
     */
    public function test_document_creation_initializes_with_draft_state()
    {
        $docNumber = 'MOA/TM/ABB/' . rand(1000, 9999);

        $response = $this->actingAs($this->jurusanUser)->post(route('jurusan.kerjasama.store'), [
            'title' => 'MoA Pengembangan Laboratorium Robotika ABB',
            'jenis' => 'MoA (Memorandum of Agreement)',
            'doc_number' => $docNumber,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYears(3)->format('Y-m-d'),
            'description' => 'Kerja sama riset terapan dan pelatihan robotika industri.',
            'tipe_pelaksana' => 'jurusan',
            'pelaksana_jurusan_ids' => [$this->jurusan->id],
            'penggiat_mitra_ids' => [$this->mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Ir. Gunawan',
                    'jabatan_penandatangan' => 'Director of Robotics',
                    'nama_pj' => 'Dewi',
                    'jabatan_pj' => 'Project Lead',
                ]
            ],
            'nama_penandatangan' => 'Kajur Mesin State',
            'jabatan_penandatangan' => 'Ketua Jurusan',
            'document_link' => 'https://drive.google.com/abb-polimdo-draft',
        ]);

        $response->assertRedirect(route('jurusan.dkerjasama'));

        $coop = Cooperation::where('doc_number', $docNumber)->firstOrFail();
        $this->assertEquals('Draft', $coop->status_dokumen);
        $this->assertEquals('Aktif', $coop->status_berlaku);
    }

    /**
     * State Transition 2: Draft -> Draft (Edit Dokumen UC09)
     */
    public function test_editing_document_in_draft_remains_in_draft_state()
    {
        $coop = Cooperation::create([
            'judul' => 'Draf Awal Kerja Sama Robotika',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/TM/ABB/DRAFT-01',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $this->mitra->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $this->jurusan->id,
        ]);
        $coop->jurusans()->sync([$this->jurusan->id]);

        $response = $this->actingAs($this->jurusanUser)->put(route('jurusan.kerjasama.update', $coop->id), [
            'title' => 'Draf Diperbarui Kerja Sama Robotika Terapan ABB',
            'jenis' => 'MoA (Memorandum of Agreement)',
            'doc_number' => 'MOA/TM/ABB/DRAFT-01',
            'tipe_pelaksana' => 'jurusan',
            'pelaksana_jurusan_ids' => [$this->jurusan->id],
            'penggiat_mitra_ids' => [$this->mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Ir. Gunawan',
                    'jabatan_penandatangan' => 'Director',
                    'nama_pj' => 'Dewi',
                    'jabatan_pj' => 'Project Lead',
                ]
            ],
            'document_link' => 'https://drive.google.com/abb-updated',
        ]);

        $response->assertRedirect(route('jurusan.dkerjasama'));
        $coop->refresh();

        $this->assertEquals('Draft', $coop->status_dokumen);
        $this->assertEquals('Draf Diperbarui Kerja Sama Robotika Terapan ABB', $coop->judul);
    }

    /**
     * State Transition 3: Draft -> Menunggu_Evaluasi (Submit ke Pimpinan UC10)
     */
    public function test_submitting_draft_transitions_to_menunggu_evaluasi()
    {
        $coop = Cooperation::create([
            'judul' => 'Draf Siap Submit Pimpinan',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/TM/ABB/SUBMIT-01',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $this->mitra->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $this->jurusan->id,
        ]);
        $coop->jurusans()->sync([$this->jurusan->id]);

        $response = $this->actingAs($this->jurusanUser)->post(route('jurusan.kerjasama.submit', $coop->id));
        $response->assertRedirect();

        $coop->refresh();
        $this->assertEquals('Menunggu Evaluasi', $coop->status_dokumen);
    }

    /**
     * State Transition 4: Menunggu_Evaluasi -> Revisi (Pimpinan Meminta Revisi UC11)
     */
    public function test_pimpinan_requesting_revision_transitions_to_revisi()
    {
        $coop = Cooperation::create([
            'judul' => 'Naskah Evaluasi Perlu Perbaikan',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/TM/ABB/REV-01',
            'status_dokumen' => 'Menunggu Evaluasi',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $this->mitra->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $this->jurusan->id,
        ]);
        $coop->jurusans()->sync([$this->jurusan->id]);

        $response = $this->actingAs($this->pimpinanUser)->post(route('pimpinan.evaluate', $coop->id), [
            'status_validasi' => 'revisi',
            'ringkasan' => 'Rincian jadwal pendanaan dan kontribusi alat lab belum lengkap.',
            'saran' => 'Harap lengkapi lampiran spesifikasi teknis robot.',
            'tindak_lanjut' => 'Kembalikan ke Jurusan untuk revisi.',
        ]);

        $response->assertRedirect(route('pimpinan.evaluasi'));
        $coop->refresh();

        $this->assertEquals('Revisi', $coop->status_dokumen);
    }

    /**
     * State Transition 5: Revisi -> Menunggu_Evaluasi (Unit Memperbaiki & Submit Ulang UC09 & UC10)
     */
    public function test_unit_editing_revisi_document_and_resubmitting()
    {
        $coop = Cooperation::create([
            'judul' => 'Naskah Dalam Masa Revisi',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/TM/ABB/REV-02',
            'status_dokumen' => 'Revisi',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $this->mitra->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $this->jurusan->id,
        ]);
        $coop->jurusans()->sync([$this->jurusan->id]);

        // 1. Unit memperbaiki data (UC09)
        $updateResponse = $this->actingAs($this->jurusanUser)->put(route('jurusan.kerjasama.update', $coop->id), [
            'title' => 'Naskah Pasca Revisi Dilengkapi Lampiran Teknis',
            'jenis' => 'MoA (Memorandum of Agreement)',
            'doc_number' => 'MOA/TM/ABB/REV-02',
            'tipe_pelaksana' => 'jurusan',
            'pelaksana_jurusan_ids' => [$this->jurusan->id],
            'penggiat_mitra_ids' => [$this->mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Ir. Gunawan',
                    'jabatan_penandatangan' => 'Director',
                    'nama_pj' => 'Dewi',
                    'jabatan_pj' => 'Project Lead',
                ]
            ],
            'document_link' => 'https://drive.google.com/abb-revised-ok',
        ]);
        $updateResponse->assertRedirect(route('jurusan.dkerjasama'));

        // 2. Unit submit ulang ke pimpinan (UC10)
        $resubmitResponse = $this->actingAs($this->jurusanUser)->post(route('jurusan.kerjasama.submit', $coop->id));
        $resubmitResponse->assertRedirect();

        $coop->refresh();
        $this->assertEquals('Menunggu Evaluasi', $coop->status_dokumen);
    }

    /**
     * State Transition 6: Menunggu_Evaluasi -> Disahkan (Pimpinan Evaluasi & Mengesahkan UC11 & UC12)
     */
    public function test_pimpinan_approving_transitions_to_disahkan_and_aktif()
    {
        $coop = Cooperation::create([
            'judul' => 'Naskah Siap Pengesahan Akhir',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/TM/ABB/DISAHKAN-01',
            'status_dokumen' => 'Menunggu Evaluasi',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $this->mitra->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $this->jurusan->id,
        ]);
        $coop->jurusans()->sync([$this->jurusan->id]);

        $response = $this->actingAs($this->pimpinanUser)->post(route('pimpinan.evaluate', $coop->id), [
            'status_validasi' => 'layak',
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 5,
            'efisiensi' => 5,
            'kepuasan' => 5,
            'ringkasan' => 'Dokumen kerjasama memenuhi seluruh persyaratan dan disahkan.',
            'saran' => 'Segera koordinasikan peresmian laboratorium.',
            'tindak_lanjut' => 'Mulai implementasi kerja sama.',
        ]);

        $response->assertRedirect(route('pimpinan.evaluasi'));
        $coop->refresh();

        $this->assertEquals('Disahkan', $coop->status_dokumen);
        $this->assertEquals('Aktif', $coop->status_berlaku);
    }

    /**
     * State Transition 7: Lifecycle State: Disahkan (Aktif -> Akan Berakhir -> Kadaluarsa -> Diperpanjang UC18)
     */
    public function test_lifecycle_state_transitions_for_disahkan_document()
    {
        Storage::fake('public');

        // 7.1 Aktif -> Akan Berakhir (Tersisa < 90 hari / status Akan Berakhir)
        $coopExpiring = Cooperation::create([
            'judul' => 'Kerjasama Menjelang Berakhir 30 Hari',
            'jenis' => 'MoU',
            'doc_number' => 'MOU/ABB/2023/001',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Akan Berakhir',
            'start_date' => now()->subYears(3),
            'end_date' => now()->addDays(20),
            'mitra_id' => $this->mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $this->assertEquals('Akan Berakhir', $coopExpiring->status_berlaku);
        $this->assertEquals('Disahkan', $coopExpiring->status_dokumen);

        // 7.2 Mitra mengajukan perpanjangan pada dokumen Akan Berakhir (UC18)
        $fileSurat = UploadedFile::fake()->create('surat_perpanjangan_abb.pdf', 300, 'application/pdf');
        $extResponse = $this->actingAs($this->mitraUser)->post(route('pengajuan.perpanjangan.store'), [
            'mitra_id' => $this->mitra->id,
            'jenis' => 'MoU (Memorandum of Understanding)',
            'doc_number' => 'MOU/ABB/2023/001',
            'nama_penandatangan' => 'Ir. Gunawan',
            'jabatan_penandatangan' => 'Director',
            'email' => 'pic.abb@partner.com',
            'telp' => '021-888999',
            'start_date' => now()->addDays(21)->format('Y-m-d'),
            'end_date' => now()->addYears(3)->format('Y-m-d'),
            'judul_pengajuan' => 'Perpanjangan MoU Riset Otomasi Robotika ABB',
            'tujuan_pengajuan' => 'Melanjutkan program sertifikasi robotika industri.',
            'ruang_lingkup' => 'Penyelenggaraan sertifikasi dan riset bersama.',
            'file_surat' => $fileSurat,
        ]);

        $extResponse->assertRedirect(route('pengajuan.perpanjangan.create'));

        $this->assertDatabaseHas('pengajuan_perpanjangan_kerjasama', [
            'mitra_id' => $this->mitra->id,
            'doc_number' => 'MOU/ABB/2023/001',
            'status' => PengajuanPerpanjanganKerjasama::STATUS_DIAJUKAN,
        ]);

        // 7.3 Kadaluarsa -> Diperpanjang (Dokumen Melewati end_date dan diajukan perpanjangan)
        $coopExpired = Cooperation::create([
            'judul' => 'Kerjasama Kadaluarsa Telah Diajukan Perpanjangan',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/ABB/2021/009',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Kadaluarsa',
            'start_date' => now()->subYears(5),
            'end_date' => now()->subMonths(2),
            'mitra_id' => $this->mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $this->assertEquals('Kadaluarsa', $coopExpired->status_berlaku);

        // Simulasi update status berlaku menjadi Diperpanjang
        $coopExpired->update(['status_berlaku' => 'Diperpanjang']);
        $this->assertEquals('Diperpanjang', $coopExpired->fresh()->status_berlaku);
    }
}

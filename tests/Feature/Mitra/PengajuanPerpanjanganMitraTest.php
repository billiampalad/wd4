<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\JenisKerjasama;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use App\Models\PengajuanPerpanjanganKerjasama;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengajuanPerpanjanganMitraTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupMitraAndUser()
    {
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Teknologi Informasi']);
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);

        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Telekomunikasi Selular Manado'],
            [
                'id_klasifikasi' => $klasifikasi->id,
                'telepon' => '0431-888222',
                'alamat' => 'Jl. Boulevard No. 99, Manado',
                'status_akses' => 'Aktif',
            ]
        );

        $mitraUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'PIC Mitra Telkomsel',
            'email' => 'pic.telkomsel@mitra.com',
        ]);

        return [$mitra, $mitraUser];
    }

    protected function setupPimpinanUser()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        return User::factory()->create([
            'role_id' => $rolePimpinan->id,
            'name' => 'Pimpinan Polimdo Penguji',
        ]);
    }

    public function test_mitra_can_access_perpanjangan_form_with_auto_filled_data()
    {
        [$mitra, $mitraUser] = $this->setupMitraAndUser();

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Akselerasi Talenta 5G',
            'jenis' => 'MoU',
            'doc_number' => 'MOU/TSEL/2024/009',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Kadaluarsa',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => '2024-01-01',
            'end_date' => '2026-01-01',
        ]);

        $response = $this->actingAs($mitraUser)->get(route('mitra.perpanjangan.create', ['cooperation_id' => $coop->id]));

        $response->assertStatus(200);
        $response->assertSee('PT Telekomunikasi Selular Manado');
        $response->assertSee('Perpanjangan Kerja Sama');
        $response->assertSee('name="doc_number"', false);
        $response->assertSee('name="file_surat"', false);
    }

    public function test_mitra_can_submit_perpanjangan_kerjasama_with_document_upload()
    {
        Storage::fake('public');
        [$mitra, $mitraUser] = $this->setupMitraAndUser();
        $pimpinan = $this->setupPimpinanUser();

        $fileSurat = UploadedFile::fake()->create('surat_permohonan_perpanjangan.pdf', 300, 'application/pdf');

        $payload = [
            'mitra_id' => $mitra->id,
            'jenis' => 'MoU (Memorandum of Understanding)',
            'doc_number' => 'MOU/TSEL/2024/009',
            'nama_penandatangan' => 'Bpk. Hendrawan Santoso',
            'jabatan_penandatangan' => 'Vice President Sales',
            'nama_penanggung_jawab' => 'Ibu Claudia',
            'jabatan_penanggung_jawab' => 'Manager HR',
            'email' => 'pic.telkomsel@mitra.com',
            'telp' => '081199887766',
            'start_date' => '2026-09-01',
            'end_date' => '2029-09-01',
            'judul_pengajuan' => 'Perpanjangan Kerjasama Akselerasi Talenta 5G',
            'tujuan_pengajuan' => 'Melanjutkan program magang industri dan laboratorium bersama 5G.',
            'ruang_lingkup' => 'Penyelenggaraan kelas industri dan beasiswa mahasiswa berprestasi.',
            'pesan_tambahan' => 'Dokumen draf baru sudah kami lampirkan dalam surat permohonan.',
            'file_surat' => $fileSurat,
        ];

        $response = $this->actingAs($mitraUser)->post(route('pengajuan.perpanjangan.store'), $payload);

        $response->assertRedirect(route('pengajuan.perpanjangan.create'));
        $response->assertSessionHas('success');

        // Assert record exists in database
        $this->assertDatabaseHas('pengajuan_perpanjangan_kerjasama', [
            'mitra_id' => $mitra->id,
            'doc_number' => 'MOU/TSEL/2024/009',
            'status' => PengajuanPerpanjanganKerjasama::STATUS_DIAJUKAN,
            'judul_pengajuan' => 'Perpanjangan Kerjasama Akselerasi Talenta 5G',
        ]);

        // Assert notification sent to pimpinan
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pimpinan->id,
            'type' => 'pengajuan_mitra',
            'source_type' => 'pengajuan_perpanjangan_kerjasama',
        ]);
    }

    public function test_perpanjangan_validation_errors_when_required_fields_are_missing()
    {
        [$mitra, $mitraUser] = $this->setupMitraAndUser();

        $response = $this->actingAs($mitraUser)->post(route('pengajuan.perpanjangan.store'), []);

        $response->assertSessionHasErrors([
            'mitra_id',
            'jenis',
            'doc_number',
            'nama_penandatangan',
            'jabatan_penandatangan',
            'email',
            'telp',
            'start_date',
            'end_date',
            'judul_pengajuan',
            'tujuan_pengajuan',
            'ruang_lingkup',
            'file_surat',
        ]);
    }

    public function test_end_date_must_be_after_or_equal_to_start_date()
    {
        [$mitra, $mitraUser] = $this->setupMitraAndUser();

        $fileSurat = UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf');

        $payload = [
            'mitra_id' => $mitra->id,
            'jenis' => 'MoU',
            'doc_number' => 'DOC/001',
            'nama_penandatangan' => 'Nama',
            'jabatan_penandatangan' => 'Jabatan',
            'email' => 'test@test.com',
            'telp' => '08123456789',
            'start_date' => '2026-10-01',
            'end_date' => '2026-01-01', // Invalid: end_date before start_date
            'judul_pengajuan' => 'Judul',
            'tujuan_pengajuan' => 'Tujuan',
            'ruang_lingkup' => 'Scope',
            'file_surat' => $fileSurat,
        ];

        $response = $this->actingAs($mitraUser)->post(route('pengajuan.perpanjangan.store'), $payload);

        $response->assertSessionHasErrors(['end_date']);
    }
}

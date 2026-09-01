<?php

namespace Tests\Feature\Pimpinan;

use App\Models\Klasifikasi;
use App\Models\Notifikasi;
use App\Models\PengajuanKerjasamaBaru;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MenerimaPengajuanKerjasamaBaruTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupPimpinanUser()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        return User::factory()->create([
            'role_id' => $rolePimpinan->id,
            'name' => 'Dr. Pimpinan Penguji, M.T.',
        ]);
    }

    public function test_guest_cannot_access_pimpinan_pengajuan_mitra()
    {
        $response = $this->get(route('pimpinan.pengajuan_mitra'));
        $response->assertRedirect('/login');
    }

    public function test_non_pimpinan_cannot_access_pengajuan_mitra()
    {
        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $userJurusan = User::factory()->create(['role_id' => $roleJurusan->id]);

        $response = $this->actingAs($userJurusan)->get(route('pimpinan.pengajuan_mitra'));
        $response->assertRedirect(route('jurusan.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_pimpinan_receives_notification_on_new_submission()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Manufaktur']);

        $submission = PengajuanKerjasamaBaru::create([
            'kode_pengajuan' => 'PGM-20260901-0001',
            'nama_mitra' => 'PT Manufaktur Sukses Abadi',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Kawasan Industri Bitung',
            'telp' => '0438-123456',
            'website' => 'https://manufaktursukses.co.id',
            'nama_penandatangan' => 'Bpk. Gunawan',
            'jabatan_penandatangan' => 'Direktur',
            'email' => 'contact@manufaktursukses.co.id',
            'judul_pengajuan' => 'Pengembangan Praktik Kerja Industri Pengelasan',
            'tujuan_pengajuan' => 'Kerjasama penyelarasan kurikulum teknik mesin.',
            'ruang_lingkup' => 'Pelatihan dan sertifikasi vokasi.',
            'status' => PengajuanKerjasamaBaru::STATUS_DIAJUKAN,
            'submitted_at' => now(),
        ]);

        Notifikasi::send(
            $pimpinan->id,
            null,
            $submission->id,
            'pengajuan_mitra',
            'Pengajuan Mitra Baru',
            "Pengajuan {$submission->kode_pengajuan} dari {$submission->nama_mitra} menunggu validasi Anda.",
            route('pimpinan.pengajuan_mitra'),
            'pengajuan_kerjasama_baru'
        );

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pimpinan->id,
            'type' => 'pengajuan_mitra',
            'source_id' => $submission->id,
            'source_type' => 'pengajuan_kerjasama_baru',
        ]);
    }

    public function test_pimpinan_can_view_daftar_pengajuan_masuk_page()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Perusahaan BUMN']);

        $submission = PengajuanKerjasamaBaru::create([
            'kode_pengajuan' => 'PGM-20260901-0002',
            'nama_mitra' => 'PT Telkom Indonesia (Persero) Tbk',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Jl. Japati No. 1 Bandung',
            'telp' => '022-4521510',
            'website' => 'https://telkom.co.id',
            'nama_penandatangan' => 'Ririek Adriansyah',
            'jabatan_penandatangan' => 'Direktur Utama',
            'email' => 'partnership@telkom.co.id',
            'judul_pengajuan' => 'Kerja Sama Digital Talent & Inkubasi Startup',
            'tujuan_pengajuan' => 'Pengembangan talenta digital muda.',
            'ruang_lingkup' => 'Penyediaan laboratorium IoT dan kurikulum cloud.',
            'status' => PengajuanKerjasamaBaru::STATUS_DIAJUKAN,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.pengajuan_mitra'));

        $response->assertStatus(200);
        $response->assertSee('Validasi Pengajuan Kerja Sama Mitra');
        $response->assertSee('PGM-20260901-0002');
        $response->assertSee('PT Telkom Indonesia (Persero) Tbk');
        $response->assertSee('Kerja Sama Digital Talent & Inkubasi Startup');
        $response->assertSee('Menunggu Review');
    }

    public function test_pimpinan_can_view_detail_pengajuan_attributes_in_table_and_modal()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Pemerintahan']);

        $submission = PengajuanKerjasamaBaru::create([
            'kode_pengajuan' => 'PGM-20260901-0003',
            'nama_mitra' => 'Dinas Komunikasi dan Informatika Sulut',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Jl. 17 Agustus No. 69 Manado',
            'telp' => '0431-865555',
            'website' => 'https://diskominfo.sulutprov.go.id',
            'nama_penandatangan' => 'Evans Steven Liow, S.Sos, M.M.',
            'jabatan_penandatangan' => 'Kepala Dinas',
            'nama_penanggung_jawab' => 'Maria S.',
            'jabatan_penanggung_jawab' => 'Kabid e-Gov',
            'email' => 'diskominfo@sulutprov.go.id',
            'judul_pengajuan' => 'Implementasi SPBE dan Magang Mahasiswa TI',
            'tujuan_pengajuan' => 'Mendukung digitalisasi pelayanan publik daerah.',
            'ruang_lingkup' => 'Pengembangan aplikasi dan tata kelola keamanan siber.',
            'pesan_tambahan' => 'Mohon dapat segera dijadwalkan penandatanganan MoU.',
            'status' => PengajuanKerjasamaBaru::STATUS_DIAJUKAN,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.pengajuan_mitra'));

        $response->assertStatus(200);
        $response->assertSee('PGM-20260901-0003');
        $response->assertSee('Dinas Komunikasi dan Informatika Sulut');
        $response->assertSee('Evans Steven Liow, S.Sos, M.M.');
        $response->assertSee('diskominfo@sulutprov.go.id');
        $response->assertSee('Implementasi SPBE dan Magang Mahasiswa TI');
    }
}

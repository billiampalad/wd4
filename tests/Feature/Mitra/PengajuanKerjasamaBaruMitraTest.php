<?php

namespace Tests\Feature\Mitra;

use App\Models\Klasifikasi;
use App\Models\Mitra;
use App\Models\PengajuanKerjasamaBaru;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PengajuanKerjasamaBaruMitraTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupKlasifikasi()
    {
        return Klasifikasi::firstOrCreate(['nama' => 'Industri Teknologi']);
    }

    protected function setupPimpinanUser()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        return User::factory()->create([
            'role_id' => $rolePimpinan->id,
        ]);
    }

    public function test_guest_can_access_pengajuan_kerjasama_form()
    {
        $klasifikasi = $this->setupKlasifikasi();

        $response = $this->get(route('pengajuan.kerjasama.create'));

        $response->assertStatus(200);
        $response->assertSee('Pengajuan Mitra Baru');
        $response->assertSee($klasifikasi->nama);
    }

    public function test_authenticated_mitra_can_access_pengajuan_form_with_autofill()
    {
        $klasifikasi = $this->setupKlasifikasi();
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);

        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Digital Inovasi Nusantara'],
            [
                'telepon' => '081234567890',
                'alamat' => 'Jl. Boulevard No. 100, Manado',
                'status_akses' => 'Aktif',
            ]
        );

        $mitraUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
        ]);

        $response = $this->actingAs($mitraUser)->get(route('mitra.pengajuan.create'));

        $response->assertStatus(200);
        $response->assertSee('PT Digital Inovasi Nusantara');
    }

    public function test_guest_can_submit_pengajuan_kerjasama_baru_successfully()
    {
        $klasifikasi = $this->setupKlasifikasi();
        $pimpinan = $this->setupPimpinanUser();

        $payload = [
            'nama_mitra' => 'PT Semesta Cipta Solusi',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Kawasan Megamas Blok 2, Manado',
            'telp' => '0431-888999',
            'website' => 'https://semestasolusi.com',
            'nama_penandatangan' => 'Bpk. Hendra Gunawan',
            'jabatan_penandatangan' => 'Direktur Utama',
            'nama_penanggung_jawab' => 'Ibu Siti Rahma',
            'jabatan_penanggung_jawab' => 'HR Manager',
            'email' => 'admin@semestasolusi.com',
            'judul_pengajuan' => 'Kerja Sama Program Magang & Rekrutmen Lulusan',
            'tujuan_pengajuan' => 'Meningkatkan penyerapan lulusan Polimdo di industri IT regional.',
            'ruang_lingkup' => 'Penyelenggaraan magang bersertifikat dan kuliah tamu praktisi.',
            'pesan_tambahan' => 'Kami siap memulai program pada semester genap tahun 2026.',
        ];

        $response = $this->post(route('pengajuan.kerjasama.store'), $payload);

        $response->assertRedirect(route('pengajuan.kerjasama.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan_kerjasama_baru', [
            'nama_mitra' => 'PT Semesta Cipta Solusi',
            'email' => 'admin@semestasolusi.com',
            'status' => PengajuanKerjasamaBaru::STATUS_DIAJUKAN,
            'judul_pengajuan' => 'Kerja Sama Program Magang & Rekrutmen Lulusan',
        ]);

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pimpinan->id,
            'type' => 'pengajuan_mitra',
        ]);
    }

    public function test_authenticated_mitra_can_submit_pengajuan_with_mitra_id()
    {
        $klasifikasi = $this->setupKlasifikasi();
        $pimpinan = $this->setupPimpinanUser();
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);

        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Surya Mega Perkasa'],
            [
                'status_akses' => 'Aktif',
            ]
        );

        $mitraUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
        ]);

        $payload = [
            'nama_mitra' => 'PT Surya Mega Perkasa',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Jl. Sam Ratulangi No. 45, Manado',
            'telp' => '081122334455',
            'website' => 'https://suryamega.co.id',
            'nama_penandatangan' => 'Ir. Surya Wijaya',
            'jabatan_penandatangan' => 'Direktur',
            'email' => 'info@suryamega.co.id',
            'judul_pengajuan' => 'Kemitraan Fasilitas Lab Robotika',
            'tujuan_pengajuan' => 'Pengembangan teaching factory dan riset terapan.',
            'ruang_lingkup' => 'Hibah peralatan dan sertifikasi kompetensi.',
        ];

        $response = $this->actingAs($mitraUser)->post(route('pengajuan.kerjasama.store'), $payload);

        $response->assertRedirect(route('pengajuan.kerjasama.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan_kerjasama_baru', [
            'nama_mitra' => 'PT Surya Mega Perkasa',
            'mitra_id' => $mitra->id,
            'status' => PengajuanKerjasamaBaru::STATUS_DIAJUKAN,
        ]);
    }

    public function test_guest_cannot_submit_if_email_already_registered_as_user()
    {
        $klasifikasi = $this->setupKlasifikasi();
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);

        $existingUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'email' => 'user.terdaftar@mitra.com',
        ]);

        $payload = [
            'nama_mitra' => 'PT Cipta Kreasi Mandiri',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Jl. Piere Tendean, Manado',
            'telp' => '081234567800',
            'nama_penandatangan' => 'Bpk. Mario',
            'jabatan_penandatangan' => 'Manajer',
            'email' => 'user.terdaftar@mitra.com',
            'judul_pengajuan' => 'Pengajuan Kemitraan Baru',
            'tujuan_pengajuan' => 'Tujuan kerjasama',
            'ruang_lingkup' => 'Ruang lingkup kerjasama',
        ];

        $response = $this->post(route('pengajuan.kerjasama.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('pengajuan_kerjasama_baru', [
            'nama_mitra' => 'PT Cipta Kreasi Mandiri',
        ]);
    }

    public function test_validation_errors_when_required_fields_are_missing()
    {
        $response = $this->post(route('pengajuan.kerjasama.store'), []);

        $response->assertSessionHasErrors([
            'nama_mitra',
            'id_klasifikasi',
            'kategori',
            'negara',
            'alamat',
            'telp',
            'nama_penandatangan',
            'jabatan_penandatangan',
            'email',
            'judul_pengajuan',
            'tujuan_pengajuan',
            'ruang_lingkup',
        ]);
    }
}

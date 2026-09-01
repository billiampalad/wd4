<?php

namespace Tests\Feature\Pimpinan;

use App\Models\Cooperation;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use App\Models\Notifikasi;
use App\Models\PengajuanKerjasamaBaru;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ValidasiPengajuanKerjasamaBaruTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupPimpinanUser()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        return User::factory()->create([
            'role_id' => $rolePimpinan->id,
            'name' => 'Prof. Dr. Ir. Pimpinan, M.T.',
        ]);
    }

    protected function setupHumasUser()
    {
        $roleHumas = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        return User::factory()->create([
            'role_id' => $roleHumas->id,
            'name' => 'Humas Polimdo',
        ]);
    }

    public function test_pimpinan_can_approve_pengajuan_and_auto_create_mitra_account()
    {
        $pimpinan = $this->setupPimpinanUser();
        $humas = $this->setupHumasUser();
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Manufaktur Otomotif']);

        $submission = PengajuanKerjasamaBaru::create([
            'kode_pengajuan' => 'PGM-20260901-7777',
            'nama_mitra' => 'PT Astra Honda Motor Manado',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Jl. Wolter Monginsidi No. 88, Manado',
            'telp' => '0431-855123',
            'website' => 'https://astrahonda.com',
            'nama_penandatangan' => 'Ir. Toshiyuki',
            'jabatan_penandatangan' => 'Presiden Direktur',
            'nama_penanggung_jawab' => 'Bpk. Ahmad Rian',
            'jabatan_penanggung_jawab' => 'Senior Manager CSR',
            'email' => 'csr.partnership@astrahonda.com',
            'judul_pengajuan' => 'Kerja Sama Kelas Khusus Otomotif & Donasi Mesin',
            'tujuan_pengajuan' => 'Peningkatan sarana praktik mahasiswa D3 Teknik Mesin.',
            'ruang_lingkup' => 'Penyediaan unit mesin dan sertifikasi teknisi.',
            'status' => PengajuanKerjasamaBaru::STATUS_DIAJUKAN,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($pimpinan)->post(route('pimpinan.pengajuan_mitra.review', $submission->id), [
            'keputusan' => 'disetujui',
            'catatan_pimpinan' => 'Pengajuan sangat relevan dengan kurikulum vokasi otomotif.',
        ]);

        $response->assertRedirect(route('pimpinan.pengajuan_mitra'));
        $response->assertSessionHas('success');

        // 1. Assert status submission is updated
        $this->assertDatabaseHas('pengajuan_kerjasama_baru', [
            'id' => $submission->id,
            'status' => 'disetujui',
            'catatan_pimpinan' => 'Pengajuan sangat relevan dengan kurikulum vokasi otomotif.',
            'reviewed_by' => $pimpinan->id,
        ]);

        // 2. Assert Master Mitra is created
        $this->assertDatabaseHas('mitras', [
            'nama_mitra' => 'PT Astra Honda Motor Manado',
        ]);

        // 3. Assert User Mitra account is automatically created
        $this->assertDatabaseHas('users', [
            'email' => 'csr.partnership@astrahonda.com',
            'role_id' => $roleMitra->id,
        ]);

        // 4. Assert Cooperation draft record is created
        $this->assertDatabaseHas('cooperations', [
            'pengajuan_kerjasama_baru_id' => $submission->id,
            'judul' => 'Kerja Sama Kelas Khusus Otomotif & Donasi Mesin',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
        ]);

        // 5. Assert notification sent to Humas/Unit Kerja
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $humas->id,
            'type' => 'data_baru',
        ]);
    }

    public function test_pimpinan_can_reject_pengajuan_with_reason()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Pemerintahan']);

        $submission = PengajuanKerjasamaBaru::create([
            'kode_pengajuan' => 'PGM-20260901-8888',
            'nama_mitra' => 'CV Mitra Tidak Lengkap',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Jl. Lingkar Luar Manado',
            'telp' => '081234567899',
            'nama_penandatangan' => 'Bpk. X',
            'email' => 'info@mitratidaklengkap.com',
            'judul_pengajuan' => 'Kerja Sama Tanpa Rincian',
            'tujuan_pengajuan' => 'Kerjasama umum',
            'ruang_lingkup' => 'Umum',
            'status' => PengajuanKerjasamaBaru::STATUS_DIAJUKAN,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($pimpinan)->post(route('pimpinan.pengajuan_mitra.review', $submission->id), [
            'keputusan' => 'ditolak',
            'catatan_pimpinan' => 'Dokumen profil perusahaan dan rincian teknis program tidak terlampir.',
        ]);

        $response->assertRedirect(route('pimpinan.pengajuan_mitra'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan_kerjasama_baru', [
            'id' => $submission->id,
            'status' => 'ditolak',
            'catatan_pimpinan' => 'Dokumen profil perusahaan dan rincian teknis program tidak terlampir.',
        ]);

        $this->assertDatabaseMissing('cooperations', [
            'pengajuan_kerjasama_baru_id' => $submission->id,
        ]);
    }

    public function test_rejection_requires_catatan_pimpinan()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Pemerintahan']);

        $submission = PengajuanKerjasamaBaru::create([
            'kode_pengajuan' => 'PGM-20260901-9990',
            'nama_mitra' => 'CV Coba Coba',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Alamat',
            'telp' => '08111111111',
            'nama_penandatangan' => 'Penandatangan',
            'email' => 'coba@cvcoba.com',
            'judul_pengajuan' => 'Judul',
            'tujuan_pengajuan' => 'Tujuan',
            'ruang_lingkup' => 'Scope',
            'status' => PengajuanKerjasamaBaru::STATUS_DIAJUKAN,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($pimpinan)->post(route('pimpinan.pengajuan_mitra.review', $submission->id), [
            'keputusan' => 'ditolak',
            'catatan_pimpinan' => '',
        ]);

        $response->assertSessionHasErrors(['catatan_pimpinan']);
    }
}

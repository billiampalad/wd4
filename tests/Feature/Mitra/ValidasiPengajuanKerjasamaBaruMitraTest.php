<?php

namespace Tests\Feature\Mitra;

use App\Models\Klasifikasi;
use App\Models\PengajuanKerjasamaBaru;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ValidasiPengajuanKerjasamaBaruMitraTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupPimpinanUser()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        return User::factory()->create([
            'role_id' => $rolePimpinan->id,
            'name' => 'Direktur Polimdo',
        ]);
    }

    public function test_pimpinan_can_validate_pengajuan_kerjasama_baru()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Teknologi Informasi']);

        $submission = PengajuanKerjasamaBaru::create([
            'kode_pengajuan' => 'PGM-20260901-5555',
            'nama_mitra' => 'PT Mitra Sukses Mandiri',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Jl. Bethesda No. 1 Manado',
            'telp' => '0431-888111',
            'nama_penandatangan' => 'Bpk. Johny',
            'email' => 'partner@mitrasukses.com',
            'judul_pengajuan' => 'Kerjasama Training IT',
            'tujuan_pengajuan' => 'Peningkatan kompetensi',
            'ruang_lingkup' => 'Training & Sertifikasi',
            'status' => PengajuanKerjasamaBaru::STATUS_DIAJUKAN,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($pimpinan)->post(route('pimpinan.pengajuan_mitra.review', $submission->id), [
            'keputusan' => 'disetujui',
            'catatan_pimpinan' => 'Disetujui untuk ditindaklanjuti Humas.',
        ]);

        $response->assertRedirect(route('pimpinan.pengajuan_mitra'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan_kerjasama_baru', [
            'id' => $submission->id,
            'status' => 'disetujui',
        ]);
    }
}

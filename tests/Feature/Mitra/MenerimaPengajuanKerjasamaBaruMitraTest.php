<?php

namespace Tests\Feature\Mitra;

use App\Models\Klasifikasi;
use App\Models\Notifikasi;
use App\Models\PengajuanKerjasamaBaru;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MenerimaPengajuanKerjasamaBaruMitraTest extends TestCase
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

    public function test_pimpinan_can_view_incoming_submission_and_details()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Teknologi']);

        $submission = PengajuanKerjasamaBaru::create([
            'kode_pengajuan' => 'PGM-20260901-9999',
            'nama_mitra' => 'PT Cloud Computing Nusantara',
            'id_klasifikasi' => $klasifikasi->id,
            'kategori' => 'nasional',
            'negara' => 'Indonesia',
            'alamat' => 'Jl. Sam Ratulangi No. 12',
            'telp' => '08123456789',
            'email' => 'contact@cloudnusantara.id',
            'nama_penandatangan' => 'Direktur Utama',
            'jabatan_penandatangan' => 'Direktur',
            'judul_pengajuan' => 'Kerjasama Cloud Academy',
            'tujuan_pengajuan' => 'Peningkatan skill cloud computing',
            'ruang_lingkup' => 'Sertifikasi AWS dan GCP',
            'status' => PengajuanKerjasamaBaru::STATUS_DIAJUKAN,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.pengajuan_mitra'));

        $response->assertStatus(200);
        $response->assertSee('PGM-20260901-9999');
        $response->assertSee('PT Cloud Computing Nusantara');
        $response->assertSee('Kerjasama Cloud Academy');
    }
}

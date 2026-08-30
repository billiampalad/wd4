<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\Jurusan;
use App\Models\KegiatanKerjasama;
use App\Models\KegiatanMahasiswa;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MitraPenilaianPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_mitra_can_access_penilaian_page()
    {
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);
        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Mitra Industri Test Penilaian'],
            ['status_akses' => 'Aktif']
        );

        $userMitra = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'PIC Mitra Penilaian',
        ]);

        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);
        $prodi = Prodi::firstOrCreate([
            'nama_prodi' => 'Teknik Informatika',
            'jurusan_id' => $jurusan->id,
        ]);

        $mahasiswa = Mahasiswa::create([
            'nim' => '22024099',
            'nama' => 'Budi Santoso Magang',
            'prodi_id' => $prodi->id,
            'angkatan' => 2022,
        ]);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Magang Industri POLIMDO',
            'doc_number' => 'DOC/MTR/MAGANG/01',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'nama_kegiatan' => 'Program Magang MBKM 2026',
            'cooperation_id' => $coop->id,
            'status' => 'Berjalan',
        ]);

        KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mahasiswa->id,
            'mitra_id' => $mitra->id,
            'status' => 'Aktif',
            'nilai_mitra' => 88.5,
            'periode_mulai' => now()->subMonths(2),
            'periode_selesai' => now()->addMonths(4),
        ]);

        $response = $this->actingAs($userMitra)->get(route('mitra.penilaian.index'));

        $response->assertStatus(200);
        $response->assertSee('Kegiatan &amp; Penilaian Magang', false);
        $response->assertSee('Budi Santoso Magang');
        $response->assertSee('22024099');
        $response->assertSee('js/auth/mitra/kegiatan-magang.js');
    }
}

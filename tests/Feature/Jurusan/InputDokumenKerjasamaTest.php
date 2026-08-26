<?php

namespace Tests\Feature\Jurusan;

use App\Models\User;
use App\Models\Role;
use App\Models\Mitra;
use App\Models\Jurusan;
use App\Models\Profile;
use App\Models\JenisKerjasama;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InputDokumenKerjasamaTest extends TestCase
{
    use DatabaseTransactions;

    protected $jurusanUser;
    protected $jurusan;

    protected function setUp(): void
    {
        parent::setUp();
        
        $role = Role::firstOrCreate(['role_name' => 'jurusan'], ['name' => 'jurusan', 'guard_name' => 'web']);
        $this->jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Jurusan Teknologi Informasi']);
        
        $this->jurusanUser = User::factory()->create([
            'role_id' => $role->id,
        ]);

        Profile::create([
            'user_id' => $this->jurusanUser->id,
            'jurusan_id' => $this->jurusan->id,
            'nama_lengkap' => 'Ketua Jurusan TI',
        ]);
    }

    public function test_jurusan_can_input_dokumen_mou()
    {
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Test Jurusan', 'status_akses' => 'Aktif']);
        
        $response = $this->actingAs($this->jurusanUser)->post(route('jurusan.kerjasama.store'), [
            'title' => 'MoU Kerjasama Jurusan TI dengan PT Mitra Test',
            'jenis' => 'MoU (Memorandum of Understanding)',
            'doc_number' => 'MOU/JTI/' . rand(1000, 9999),
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYears(5)->format('Y-m-d'),
            'description' => 'Ruang lingkup kerjasama...',
            'tipe_pelaksana' => 'jurusan',
            'pelaksana_jurusan_ids' => [$this->jurusan->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Budi',
                    'jabatan_penandatangan' => 'Direktur',
                    'nama_pj' => 'Andi',
                    'jabatan_pj' => 'Manajer',
                ]
            ],
            'nama_penandatangan' => 'Kajur TI',
            'jabatan_penandatangan' => 'Ketua Jurusan',
            'document_link' => 'https://drive.google.com/mou',
        ]);

        $response->assertRedirect(route('jurusan.dkerjasama'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cooperations', [
            'title' => 'MoU Kerjasama Jurusan TI dengan PT Mitra Test',
            'mitra_id' => $mitra->id,
            'status_dokumen' => 'Draft',
        ]);
    }
}

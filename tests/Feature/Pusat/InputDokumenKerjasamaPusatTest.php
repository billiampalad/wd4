<?php

namespace Tests\Feature\Pusat;

use App\Models\User;
use App\Models\Role;
use App\Models\Mitra;
use App\Models\Profile;
use App\Models\Pusat;
use App\Models\Cooperation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InputDokumenKerjasamaPusatTest extends TestCase
{
    use DatabaseTransactions;

    public function test_pusat_can_input_dokumen_mou()
    {
        $role = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Penelitian']);
        
        $pusatUser = User::factory()->create([
            'role_id' => $role->id,
        ]);

        Profile::create([
            'user_id' => $pusatUser->id,
            'pusat_id' => $pusat->id,
            'nama_lengkap' => 'Kepala Pusat',
        ]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Test Pusat', 'status_akses' => 'Aktif']);
        
        $response = $this->actingAs($pusatUser)->post(route('pusat.kerjasama.store'), [
            'title' => 'MoU Kerjasama Pusat dengan PT Mitra Test',
            'jenis' => 'MoU (Memorandum of Understanding)',
            'doc_number' => 'MOU/PST/' . rand(1000, 9999),
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYears(5)->format('Y-m-d'),
            'description' => 'Ruang lingkup kerjasama pusat',
            'tipe_pelaksana' => 'pusat',
            'pelaksana_pusat_ids' => [$pusat->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Budi',
                    'jabatan_penandatangan' => 'Direktur',
                    'nama_pj' => 'Andi',
                    'jabatan_pj' => 'Manajer',
                ]
            ],
            'nama_penandatangan' => 'Kepala Pusat Penelitian',
            'document_link' => 'https://drive.google.com/mou-pusat',
        ]);

        $response->assertRedirect(route('pusat.dkerjasama'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cooperations', [
            'judul' => 'MoU Kerjasama Pusat dengan PT Mitra Test',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Pusat/UPA',
        ]);
    }
}

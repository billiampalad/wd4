<?php

namespace Tests\Feature\Humas;

use App\Models\User;
use App\Models\Role;
use App\Models\Mitra;
use App\Models\Profile;
use App\Models\UnitKerja;
use App\Models\Cooperation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InputDokumenKerjasamaHumasTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Since we are running in sqlite memory, we don't have tables unless we migrate.
        // If testing on real DB, DatabaseTransactions will wrap this.
    }

    public function test_humas_can_input_dokumen_mou()
    {
        $role = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $unit = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas Polimdo']);
        
        $humasUser = User::factory()->create([
            'role_id' => $role->id,
        ]);

        Profile::create([
            'user_id' => $humasUser->id,
            'unit_kerja_id' => $unit->id,
            'nama_lengkap' => 'Kepala Humas',
        ]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Test Humas', 'status_akses' => 'Aktif']);
        
        $response = $this->actingAs($humasUser)->post(route('unit.kerjasama.store'), [
            'title' => 'MoU Kerjasama Humas dengan PT Mitra Test',
            'jenis' => 'MoU (Memorandum of Understanding)',
            'doc_number' => 'MOU/HMS/' . rand(1000, 9999),
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYears(5)->format('Y-m-d'),
            'description' => 'Ruang lingkup kerjasama tingkat institusi',
            'tipe_pelaksana' => 'unit',
            'pelaksana_unit_ids' => [$unit->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Budi',
                    'jabatan_penandatangan' => 'Direktur',
                    'nama_pj' => 'Andi',
                    'jabatan_pj' => 'Manajer',
                ]
            ],
            'nama_penandatangan' => 'Direktur Polimdo',
            'document_link' => 'https://drive.google.com/mou-humas',
        ]);

        $response->assertRedirect(route('unit.dkerjasama'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cooperations', [
            'judul' => 'MoU Kerjasama Humas dengan PT Mitra Test',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);
    }
}

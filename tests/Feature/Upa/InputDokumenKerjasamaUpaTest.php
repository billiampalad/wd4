<?php

namespace Tests\Feature\Upa;

use App\Models\User;
use App\Models\Role;
use App\Models\Mitra;
use App\Models\Profile;
use App\Models\Upa;
use App\Models\Cooperation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InputDokumenKerjasamaUpaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_upa_can_input_dokumen_ia()
    {
        $role = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA TIK']);
        
        $upaUser = User::factory()->create([
            'role_id' => $role->id,
        ]);

        Profile::create([
            'user_id' => $upaUser->id,
            'upa_id' => $upa->id,
            'nama_lengkap' => 'Kepala UPA',
        ]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Test UPA', 'status_akses' => 'Aktif']);
        
        $response = $this->actingAs($upaUser)->post(route('upa.kerjasama.store'), [
            'title' => 'IA Kerjasama UPA dengan PT Mitra Test',
            'jenis' => 'IA (Implementation Agreement)',
            'doc_number' => 'IA/UPA/' . rand(1000, 9999),
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYears(5)->format('Y-m-d'),
            'description' => 'Ruang lingkup kerjasama UPA',
            'tipe_pelaksana' => 'upa',
            'pelaksana_upa_ids' => [$upa->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Budi',
                    'jabatan_penandatangan' => 'Direktur',
                    'nama_pj' => 'Andi',
                    'jabatan_pj' => 'Manajer',
                ]
            ],
            'nama_penandatangan' => 'Kepala UPA TIK',
            'document_link' => 'https://drive.google.com/ia-upa',
        ]);

        $response->assertRedirect(route('upa.dkerjasama'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cooperations', [
            'judul' => 'IA Kerjasama UPA dengan PT Mitra Test',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Pusat/UPA',
        ]);
    }

    public function test_upa_can_edit_dokumen_ia()
    {
        $role = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA TIK']);
        
        $upaUser = User::factory()->create([
            'role_id' => $role->id,
        ]);

        Profile::create([
            'user_id' => $upaUser->id,
            'upa_id' => $upa->id,
        ]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Test UPA Edit', 'status_akses' => 'Aktif']);
        
        $coop = Cooperation::create([
            'judul' => 'Draft UPA Original',
            'jenis' => 'IA',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Pusat/UPA',
            'upa_id' => $upa->id
        ]);
        $coop->upas()->sync([$upa->id]);

        $response = $this->actingAs($upaUser)->put(route('upa.kerjasama.update', $coop->id), [
            'title' => 'Draft UPA Diupdate',
            'jenis' => 'IA (Implementation Agreement)',
            'doc_number' => 'IA/UPA/EDIT',
            'tipe_pelaksana' => 'upa',
            'pelaksana_upa_ids' => [$upa->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Budi',
                    'jabatan_penandatangan' => 'Direktur',
                    'nama_pj' => 'Andi',
                    'jabatan_pj' => 'Manajer',
                ]
            ],
            'document_link' => 'https://drive.google.com/test-upa-edit',
            'status' => 'Aktif',
        ]);

        $response->assertRedirect(route('upa.dkerjasama'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cooperations', [
            'id' => $coop->id,
            'judul' => 'Draft UPA Diupdate',
        ]);
    }
}

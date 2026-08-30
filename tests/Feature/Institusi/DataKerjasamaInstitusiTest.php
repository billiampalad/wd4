<?php

namespace Tests\Feature\Institusi;

use App\Models\Cooperation;
use App\Models\Jurusan;
use App\Models\Mitra;
use App\Models\Profile;
use App\Models\Pusat;
use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\Upa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataKerjasamaInstitusiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unit_can_access_institusi_page()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $userUnit = User::factory()->create(['role_id' => $roleUnit->id]);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas']);
        Profile::create(['user_id' => $userUnit->id, 'unit_kerja_id' => $unitKerja->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Institusi Test', 'status_akses' => 'Aktif']);

        // Instansi document (MoU tanpa pelaksana spesifik)
        Cooperation::create([
            'judul' => 'MoU Instansi Test',
            'doc_number' => 'MOU/INST/2026',
            'jenis' => 'MoU',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $response = $this->actingAs($userUnit)->get(route('unit.institusi'));
        $response->assertStatus(200);
        $response->assertViewHas('instansi');
        $response->assertViewHas('jurusans');
        $response->assertViewHas('upas');
        $response->assertViewHas('pusats');
        $response->assertViewHas('mouCount');
        $response->assertViewHas('moaCount');
        $response->assertViewHas('iaCount');
    }

    public function test_jurusan_can_access_institusi_page()
    {
        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $userJurusan = User::factory()->create(['role_id' => $roleJurusan->id]);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);
        Profile::create(['user_id' => $userJurusan->id, 'jurusan_id' => $jurusan->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Jurusan Test', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'MoA Jurusan Test',
            'doc_number' => 'MOA/JUR/2026',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'jurusan_id' => $jurusan->id,
        ]);
        $coop->jurusans()->sync([$jurusan->id]);

        $response = $this->actingAs($userJurusan)->get(route('jurusan.institusi'));
        $response->assertStatus(200);
        $response->assertViewHas('instansi');
        $response->assertViewHas('jurusans');
    }

    public function test_upa_can_access_institusi_page()
    {
        $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $userUpa = User::factory()->create(['role_id' => $roleUpa->id]);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Perpustakaan']);
        Profile::create(['user_id' => $userUpa->id, 'upa_id' => $upa->id]);

        $response = $this->actingAs($userUpa)->get(route('upa.institusi'));
        $response->assertStatus(200);
        $response->assertViewHas('instansi');
        $response->assertViewHas('upas');
    }

    public function test_pusat_can_access_institusi_page()
    {
        $rolePusat = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $userPusat = User::factory()->create(['role_id' => $rolePusat->id]);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Karir']);
        Profile::create(['user_id' => $userPusat->id, 'pusat_id' => $pusat->id]);

        $response = $this->actingAs($userPusat)->get(route('pusat.institusi'));
        $response->assertStatus(200);
        $response->assertViewHas('instansi');
        $response->assertViewHas('pusats');
    }
}

<?php

namespace Tests\Feature\Kerjasama;

use App\Models\Cooperation;
use App\Models\Jurusan;
use App\Models\Mitra;
use App\Models\PksNumber;
use App\Models\Profile;
use App\Models\Pusat;
use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\Upa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InputKerjasamaPksNumberTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_jurusan_can_store_kerjasama_with_pks_numbers()
    {
        $role = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);
        Profile::create(['user_id' => $user->id, 'jurusan_id' => $jurusan->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra PKS Jurusan Test', 'status_akses' => 'Aktif']);

        $payload = [
            'title' => 'Kerjasama Riset Jurusan Elektro 2026',
            'jenis' => 'MoA (Memorandum of Agreement)',
            'doc_number' => 'DOC/MOA/JUR/' . uniqid(),
            'pks_numbers' => ['0321', '0322'],
            'tipe_pelaksana' => 'jurusan',
            'pelaksana_jurusan_ids' => [$jurusan->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Direktur PT Mitra',
                    'jabatan_penandatangan' => 'Direktur Utama',
                ],
            ],
            'start_date' => '2026-09-01',
            'end_date' => '2027-09-01',
        ];

        $response = $this->actingAs($user)->post(route('jurusan.kerjasama.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('jurusan.dkerjasama'));

        $coop = Cooperation::where('judul', 'Kerjasama Riset Jurusan Elektro 2026')->first();
        $this->assertNotNull($coop);
        $this->assertEquals('Jurusan', $coop->tingkat);
        $this->assertEquals($jurusan->id, $coop->jurusan_id);

        $pks = $coop->pksNumbers;
        $this->assertCount(2, $pks);
        $this->assertEquals(['0321', '0322'], $pks->pluck('number')->all());
    }

    public function test_pusat_can_store_kerjasama_with_pks_numbers()
    {
        $role = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Penelitian']);
        Profile::create(['user_id' => $user->id, 'pusat_id' => $pusat->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra PKS Pusat Test', 'status_akses' => 'Aktif']);

        $payload = [
            'title' => 'Kerjasama Penelitian Terapan Pusat 2026',
            'jenis' => 'MoA (Memorandum of Agreement)',
            'doc_number' => 'DOC/MOA/PUSAT/' . uniqid(),
            'pks_numbers' => ['0401'],
            'tipe_pelaksana' => 'pusat',
            'pelaksana_pusat_ids' => [$pusat->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Pimpinan Mitra Pusat',
                    'jabatan_penandatangan' => 'Direktur',
                ],
            ],
            'start_date' => '2026-09-01',
            'end_date' => '2027-09-01',
        ];

        $response = $this->actingAs($user)->post(route('pusat.kerjasama.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('pusat.dkerjasama'));

        $coop = Cooperation::where('judul', 'Kerjasama Penelitian Terapan Pusat 2026')->first();
        $this->assertNotNull($coop);
        $this->assertEquals($pusat->id, $coop->pusat_id);

        $pks = $coop->pksNumbers;
        $this->assertCount(1, $pks);
        $this->assertEquals('0401', $pks->first()->number);
    }

    public function test_upa_can_store_kerjasama_with_pks_numbers()
    {
        $role = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Perpustakaan']);
        Profile::create(['user_id' => $user->id, 'upa_id' => $upa->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra PKS UPA Test', 'status_akses' => 'Aktif']);

        $payload = [
            'title' => 'Kerjasama Digital Library UPA 2026',
            'jenis' => 'MoA (Memorandum of Agreement)',
            'doc_number' => 'DOC/MOA/UPA/' . uniqid(),
            'pks_numbers' => ['0501'],
            'tipe_pelaksana' => 'upa',
            'pelaksana_upa_ids' => [$upa->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Direktur Vendor Perpustakaan',
                    'jabatan_penandatangan' => 'Direktur',
                ],
            ],
            'start_date' => '2026-09-01',
            'end_date' => '2027-09-01',
        ];

        $response = $this->actingAs($user)->post(route('upa.kerjasama.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('upa.dkerjasama'));

        $coop = Cooperation::where('judul', 'Kerjasama Digital Library UPA 2026')->first();
        $this->assertNotNull($coop);
        $this->assertEquals($upa->id, $coop->upa_id);

        $pks = $coop->pksNumbers;
        $this->assertCount(1, $pks);
        $this->assertEquals('0501', $pks->first()->number);
    }

    public function test_unit_humas_can_store_kerjasama_with_pks_numbers()
    {
        $role = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $unit = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas']);
        Profile::create(['user_id' => $user->id, 'unit_kerja_id' => $unit->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra PKS Humas Test', 'status_akses' => 'Aktif']);

        $payload = [
            'title' => 'Kerjasama Publikasi Humas 2026',
            'jenis' => 'MoU (Memorandum of Understanding)',
            'doc_number' => 'DOC/MOU/HUMAS/' . uniqid(),
            'pks_numbers' => ['0601'],
            'tipe_pelaksana' => ['jurusan'],
            'pelaksana_jurusan_ids' => [1],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Pimpinan Humas Mitra',
                    'jabatan_penandatangan' => 'Direktur Komunikasi',
                ],
            ],
            'start_date' => '2026-09-01',
            'end_date' => '2027-09-01',
        ];

        $response = $this->actingAs($user)->post(route('unit.kerjasama.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('unit.dkerjasama'));

        $coop = Cooperation::where('judul', 'Kerjasama Publikasi Humas 2026')->first();
        $this->assertNotNull($coop);

        $pks = $coop->pksNumbers;
        $this->assertCount(1, $pks);
        $this->assertEquals('0601', $pks->first()->number);
    }

    public function test_pks_number_validation_rejects_duplicate_number()
    {
        $role = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Mesin']);
        Profile::create(['user_id' => $user->id, 'jurusan_id' => $jurusan->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra PKS Dup Test', 'status_akses' => 'Aktif']);

        $existingCoop = Cooperation::create([
            'judul' => 'Kerjasama Mesin Lama',
            'doc_number' => 'DOC/OLD/' . uniqid(),
            'jenis' => 'MoA',
            'status_dokumen' => 'Draft',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $jurusan->id,
        ]);
        $existingCoop->pksNumbers()->create(['number' => 'DUPLICATE_PKS_123', 'sort_order' => 0]);

        $payload = [
            'title' => 'Kerjasama Mesin Baru',
            'jenis' => 'MoA (Memorandum of Agreement)',
            'doc_number' => 'DOC/NEW/' . uniqid(),
            'pks_numbers' => ['DUPLICATE_PKS_123'],
            'tipe_pelaksana' => 'jurusan',
            'pelaksana_jurusan_ids' => [$jurusan->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Direktur Mitra',
                    'jabatan_penandatangan' => 'Direktur',
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('jurusan.kerjasama.store'), $payload);
        $response->assertSessionHasErrors('pks_numbers.0');
    }

    public function test_jurusan_can_update_kerjasama_with_same_pks_number()
    {
        $role = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Komputer']);
        Profile::create(['user_id' => $user->id, 'jurusan_id' => $jurusan->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Update Test', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Update Test',
            'doc_number' => 'DOC/UPD/' . uniqid(),
            'jenis' => 'MoA',
            'status_dokumen' => 'Draft',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $jurusan->id,
        ]);
        $coop->pksNumbers()->create(['number' => 'PKS/OWN/123', 'sort_order' => 0]);

        $payload = [
            'title' => 'Kerjasama Update Test (Updated)',
            'jenis' => 'MoA (Memorandum of Agreement)',
            'doc_number' => $coop->doc_number,
            'pks_numbers' => ['PKS/OWN/123', 'PKS/OWN/456'],
            'tipe_pelaksana' => 'jurusan',
            'pelaksana_jurusan_ids' => [$jurusan->id],
            'penggiat_mitra_ids' => [$mitra->id],
            'penggiat' => [
                [
                    'nama_penandatangan' => 'Direktur Mitra',
                    'jabatan_penandatangan' => 'Direktur',
                ],
            ],
        ];

        $response = $this->actingAs($user)->put(route('jurusan.kerjasama.update', $coop->id), $payload);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('jurusan.dkerjasama'));

        $this->assertEquals(2, $coop->fresh()->pksNumbers()->count());
        $this->assertEquals(['PKS/OWN/123', 'PKS/OWN/456'], $coop->fresh()->pksNumbers->pluck('number')->all());
    }
}

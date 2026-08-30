<?php

namespace Tests\Feature\Referensi;

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

class StatusKerjasamaReferensiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unit_can_access_status_kerjasama_referensi()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $userUnit = User::factory()->create(['role_id' => $roleUnit->id]);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas']);
        Profile::create(['user_id' => $userUnit->id, 'unit_kerja_id' => $unitKerja->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Referensi Unit Test', 'status_akses' => 'Aktif']);

        Cooperation::create([
            'judul' => 'Kerjasama Aktif Unit 2026',
            'doc_number' => 'DOC/UNIT/REF/01',
            'jenis' => 'MoU',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $response = $this->actingAs($userUnit)->get(route('unit.referensi.status-kerjasama'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Status Kerjasama');
        $response->assertSee('Aktif');
        $response->assertSee('Segera Berakhir');
        $response->assertSee('Kadaluarsa');
        $response->assertSee('Draft / Proses');
        $response->assertSee('Tidak Aktif');
    }

    public function test_jurusan_can_access_status_kerjasama_referensi()
    {
        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $userJurusan = User::factory()->create(['role_id' => $roleJurusan->id]);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Sipil']);
        Profile::create(['user_id' => $userJurusan->id, 'jurusan_id' => $jurusan->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Referensi Jurusan Test', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Aktif Jurusan 2026',
            'doc_number' => 'DOC/JUR/REF/01',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'jurusan_id' => $jurusan->id,
        ]);
        $coop->jurusans()->sync([$jurusan->id]);

        $response = $this->actingAs($userJurusan)->get(route('jurusan.referensi.status-kerjasama'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Status Kerjasama');
        $response->assertSee('Aktif');
    }

    public function test_upa_can_access_status_kerjasama_referensi()
    {
        $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $userUpa = User::factory()->create(['role_id' => $roleUpa->id]);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Bahasa']);
        Profile::create(['user_id' => $userUpa->id, 'upa_id' => $upa->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Referensi UPA Test', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Aktif UPA 2026',
            'doc_number' => 'DOC/UPA/REF/01',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'upa_id' => $upa->id,
        ]);
        $coop->upas()->sync([$upa->id]);

        $response = $this->actingAs($userUpa)->get(route('upa.referensi.status-kerjasama'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Status Kerjasama');
        $response->assertSee('Aktif');
    }

    public function test_pusat_can_access_status_kerjasama_referensi()
    {
        $rolePusat = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $userPusat = User::factory()->create(['role_id' => $rolePusat->id]);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Pengabdian']);
        Profile::create(['user_id' => $userPusat->id, 'pusat_id' => $pusat->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Referensi Pusat Test', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Aktif Pusat 2026',
            'doc_number' => 'DOC/PUSAT/REF/01',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'pusat_id' => $pusat->id,
        ]);
        $coop->pusats()->sync([$pusat->id]);

        $response = $this->actingAs($userPusat)->get(route('pusat.referensi.status-kerjasama'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Status Kerjasama');
        $response->assertSee('Aktif');
    }
}

<?php

namespace Tests\Feature\Dkerjasama;

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

class DkerjasamaPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unit_can_access_dkerjasama_page_and_preview_ajax()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $userUnit = User::factory()->create(['role_id' => $roleUnit->id]);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas']);
        Profile::create(['user_id' => $userUnit->id, 'unit_kerja_id' => $unitKerja->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Unit Test Dkerjasama', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Strategis Unit Humas 2026',
            'doc_number' => 'DOC/UNIT/DK/01',
            'jenis' => 'MoU',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);

        $response = $this->actingAs($userUnit)->get(route('unit.dkerjasama'));
        $response->assertStatus(200);
        $response->assertSee('Kerjasama Strategis Unit Humas 2026');

        $previewResponse = $this->actingAs($userUnit)->getJson(route('unit.dkerjasama.preview'));
        $previewResponse->assertStatus(200);
        $json = $previewResponse->json();
        $this->assertIsArray($json);
        $this->assertNotEmpty($json);
        $this->assertContains('Kerjasama Strategis Unit Humas 2026', collect($json)->pluck('title')->all());
    }

    public function test_jurusan_can_access_dkerjasama_page_and_preview_ajax()
    {
        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $userJurusan = User::factory()->create(['role_id' => $roleJurusan->id]);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);
        Profile::create(['user_id' => $userJurusan->id, 'jurusan_id' => $jurusan->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Jurusan Test Dkerjasama', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Riset Jurusan Elektro 2026',
            'doc_number' => 'DOC/JUR/DK/01',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'jurusan_id' => $jurusan->id,
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);
        $coop->jurusans()->sync([$jurusan->id]);

        $response = $this->actingAs($userJurusan)->get(route('jurusan.dkerjasama'));
        $response->assertStatus(200);
        $response->assertSee('Kerjasama Riset Jurusan Elektro 2026');

        $previewResponse = $this->actingAs($userJurusan)->getJson(route('jurusan.dkerjasama.preview'));
        $previewResponse->assertStatus(200);
        $json = $previewResponse->json();
        $this->assertIsArray($json);
        $this->assertNotEmpty($json);
        $this->assertContains('Kerjasama Riset Jurusan Elektro 2026', collect($json)->pluck('title')->all());
    }

    public function test_upa_can_access_dkerjasama_page_and_preview_ajax()
    {
        $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $userUpa = User::factory()->create(['role_id' => $roleUpa->id]);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Perpustakaan']);
        Profile::create(['user_id' => $userUpa->id, 'upa_id' => $upa->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra UPA Test Dkerjasama', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Digital Library UPA 2026',
            'doc_number' => 'DOC/UPA/DK/01',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'upa_id' => $upa->id,
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);
        $coop->upas()->sync([$upa->id]);

        $response = $this->actingAs($userUpa)->get(route('upa.dkerjasama'));
        $response->assertStatus(200);
        $response->assertSee('Kerjasama Digital Library UPA 2026');

        $previewResponse = $this->actingAs($userUpa)->getJson(route('upa.dkerjasama.preview'));
        $previewResponse->assertStatus(200);
        $json = $previewResponse->json();
        $this->assertIsArray($json);
        $this->assertNotEmpty($json);
        $this->assertContains('Kerjasama Digital Library UPA 2026', collect($json)->pluck('title')->all());
    }

    public function test_pusat_can_access_dkerjasama_page_and_preview_ajax()
    {
        $rolePusat = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $userPusat = User::factory()->create(['role_id' => $rolePusat->id]);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Karir']);
        Profile::create(['user_id' => $userPusat->id, 'pusat_id' => $pusat->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Pusat Test Dkerjasama', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Rekrutmen Pusat Karir 2026',
            'doc_number' => 'DOC/PUSAT/DK/01',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'pusat_id' => $pusat->id,
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);
        $coop->pusats()->sync([$pusat->id]);

        $response = $this->actingAs($userPusat)->get(route('pusat.dkerjasama'));
        $response->assertStatus(200);
        $response->assertSee('Kerjasama Rekrutmen Pusat Karir 2026');

        $previewResponse = $this->actingAs($userPusat)->getJson(route('pusat.dkerjasama.preview'));
        $previewResponse->assertStatus(200);
        $json = $previewResponse->json();
        $this->assertIsArray($json);
        $this->assertNotEmpty($json);
        $this->assertContains('Kerjasama Rekrutmen Pusat Karir 2026', collect($json)->pluck('title')->all());
    }
}

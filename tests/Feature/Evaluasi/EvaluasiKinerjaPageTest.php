<?php

namespace Tests\Feature\Evaluasi;

use App\Models\Cooperation;
use App\Models\Evaluasi;
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

class EvaluasiKinerjaPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unit_can_access_evaluasi_page_and_render_all_status_sections()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $userUnit = User::factory()->create(['role_id' => $roleUnit->id]);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas']);
        Profile::create(['user_id' => $userUnit->id, 'unit_kerja_id' => $unitKerja->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Unit Evaluasi Test', 'status_akses' => 'Aktif']);

        // 1. Draft
        $draft = Cooperation::create([
            'judul' => 'Kerjasama Unit Humas Draft 2026',
            'doc_number' => 'DOC/UNIT/EV/DRAFT',
            'jenis' => 'MoU',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        // 2. Revisi
        $revisi = Cooperation::create([
            'judul' => 'Kerjasama Unit Humas Revisi 2026',
            'doc_number' => 'DOC/UNIT/EV/REV',
            'jenis' => 'MoA',
            'status_dokumen' => 'Revisi',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);
        Evaluasi::create([
            'cooperation_id' => $revisi->id,
            'evaluator_id' => $userUnit->id,
            'ringkasan' => 'Perbaiki berkas lampiran pendukung',
        ]);

        // 3. Menunggu Evaluasi
        $pending = Cooperation::create([
            'judul' => 'Kerjasama Unit Humas Menunggu 2026',
            'doc_number' => 'DOC/UNIT/EV/WAIT',
            'jenis' => 'IA',
            'status_dokumen' => 'Menunggu Evaluasi',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        // 4. Disahkan
        $disahkan = Cooperation::create([
            'judul' => 'Kerjasama Unit Humas Disahkan 2026',
            'doc_number' => 'DOC/UNIT/EV/DONE',
            'jenis' => 'MoU',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);
        Evaluasi::create([
            'cooperation_id' => $disahkan->id,
            'evaluator_id' => $userUnit->id,
            'kualitas' => 5,
            'keterlibatan' => 4,
            'efisiensi' => 5,
            'kepuasan' => 4,
        ]);

        $response = $this->actingAs($userUnit)->get(route('unit.evaluasi'));
        $response->assertStatus(200);
        $response->assertSee('Kerjasama Unit Humas Draft 2026');
        $response->assertSee('Kerjasama Unit Humas Revisi 2026');
        $response->assertSee('Perbaiki berkas lampiran pendukung');
        $response->assertSee('Kerjasama Unit Humas Menunggu 2026');
        $response->assertSee('Kerjasama Unit Humas Disahkan 2026');
    }

    public function test_jurusan_can_access_evaluasi_page_and_render_sections()
    {
        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $userJurusan = User::factory()->create(['role_id' => $roleJurusan->id]);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Mesin']);
        Profile::create(['user_id' => $userJurusan->id, 'jurusan_id' => $jurusan->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Jurusan Evaluasi Test', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Mesin Industri 2026',
            'doc_number' => 'DOC/MESIN/EV/01',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'jurusan_id' => $jurusan->id,
        ]);
        $coop->jurusans()->sync([$jurusan->id]);

        Evaluasi::create([
            'cooperation_id' => $coop->id,
            'evaluator_id' => $userJurusan->id,
            'kualitas' => 4,
            'keterlibatan' => 4,
            'efisiensi' => 4,
            'kepuasan' => 4,
        ]);

        $response = $this->actingAs($userJurusan)->get(route('jurusan.evaluasi'));
        $response->assertStatus(200);
        $response->assertSee('Kerjasama Mesin Industri 2026');
    }

    public function test_upa_can_access_evaluasi_page_and_render_sections()
    {
        $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $userUpa = User::factory()->create(['role_id' => $roleUpa->id]);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Komputer']);
        Profile::create(['user_id' => $userUpa->id, 'upa_id' => $upa->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra UPA Evaluasi Test', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Jaringan Server UPA 2026',
            'doc_number' => 'DOC/UPA/EV/01',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'upa_id' => $upa->id,
        ]);
        $coop->upas()->sync([$upa->id]);

        $response = $this->actingAs($userUpa)->get(route('upa.evaluasi'));
        $response->assertStatus(200);
        $response->assertSee('Kerjasama Jaringan Server UPA 2026');
    }

    public function test_pusat_can_access_evaluasi_page_and_render_sections()
    {
        $rolePusat = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $userPusat = User::factory()->create(['role_id' => $rolePusat->id]);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Penelitian']);
        Profile::create(['user_id' => $userPusat->id, 'pusat_id' => $pusat->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Pusat Evaluasi Test', 'status_akses' => 'Aktif']);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Inovasi Terapan Pusat 2026',
            'doc_number' => 'DOC/PUSAT/EV/01',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'pusat_id' => $pusat->id,
        ]);
        $coop->pusats()->sync([$pusat->id]);

        $response = $this->actingAs($userPusat)->get(route('pusat.evaluasi'));
        $response->assertStatus(200);
        $response->assertSee('Kerjasama Inovasi Terapan Pusat 2026');
    }
}

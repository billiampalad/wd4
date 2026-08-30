<?php

namespace Tests\Feature\Analitik;

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

class StatusKerjasamaAnalitikTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unit_can_access_status_kerjasama_page_and_ajax()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $userUnit = User::factory()->create(['role_id' => $roleUnit->id]);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas']);
        Profile::create(['user_id' => $userUnit->id, 'unit_kerja_id' => $unitKerja->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Unit Test', 'status_akses' => 'Aktif']);
        Cooperation::create([
            'judul' => 'Kerjasama Analitik Unit Test',
            'doc_number' => 'DOC/UNIT/2026',
            'jenis' => 'MoU',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);

        $response = $this->actingAs($userUnit)->get(route('unit.analitik.status-kerjasama'));
        $response->assertStatus(200);
        $response->assertViewHas('statusKerjasamaData');
        $response->assertViewHas('growthData');
        $response->assertViewHas('calendarData');
        $response->assertViewHas('dueDateData');

        $responsePartial = $this->actingAs($userUnit)->getJson(route('unit.analitik.status-kerjasama', ['partial' => 'due_date']));
        $responsePartial->assertStatus(200);
        $responsePartial->assertJsonStructure(['dueDateData' => ['year', 'rows', 'weeks']]);
    }

    public function test_jurusan_can_access_status_kerjasama_page_and_ajax()
    {
        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $userJurusan = User::factory()->create(['role_id' => $roleJurusan->id]);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);
        Profile::create(['user_id' => $userJurusan->id, 'jurusan_id' => $jurusan->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Jurusan Test', 'status_akses' => 'Aktif']);
        $coop = Cooperation::create([
            'judul' => 'Kerjasama Analitik Jurusan Test',
            'doc_number' => 'DOC/JURUSAN/2026',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $jurusan->id,
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);
        $coop->jurusans()->sync([$jurusan->id]);

        $response = $this->actingAs($userJurusan)->get(route('jurusan.analitik.status-kerjasama'));
        $response->assertStatus(200);
        $response->assertViewHas('statusKerjasamaData');
        $response->assertViewHas('dueDateData');

        $responsePartial = $this->actingAs($userJurusan)->getJson(route('jurusan.analitik.status-kerjasama', ['partial' => 'due_date']));
        $responsePartial->assertStatus(200);
        $responsePartial->assertJsonStructure(['dueDateData' => ['year', 'rows', 'weeks']]);
    }

    public function test_upa_can_access_status_kerjasama_page_and_ajax()
    {
        $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $userUpa = User::factory()->create(['role_id' => $roleUpa->id]);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Komputer']);
        Profile::create(['user_id' => $userUpa->id, 'upa_id' => $upa->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra UPA Test', 'status_akses' => 'Aktif']);
        $coop = Cooperation::create([
            'judul' => 'Kerjasama Analitik UPA Test',
            'doc_number' => 'DOC/UPA/2026',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'upa_id' => $upa->id,
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);
        $coop->upas()->sync([$upa->id]);

        $response = $this->actingAs($userUpa)->get(route('upa.analitik.status-kerjasama'));
        $response->assertStatus(200);
        $response->assertViewHas('statusKerjasamaData');

        $responsePartial = $this->actingAs($userUpa)->getJson(route('upa.analitik.status-kerjasama', ['partial' => 'due_date']));
        $responsePartial->assertStatus(200);
        $responsePartial->assertJsonStructure(['dueDateData' => ['year', 'rows', 'weeks']]);
    }

    public function test_pusat_can_access_status_kerjasama_page_and_ajax()
    {
        $rolePusat = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $userPusat = User::factory()->create(['role_id' => $rolePusat->id]);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Karir']);
        Profile::create(['user_id' => $userPusat->id, 'pusat_id' => $pusat->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Pusat Test', 'status_akses' => 'Aktif']);
        $coop = Cooperation::create([
            'judul' => 'Kerjasama Analitik Pusat Test',
            'doc_number' => 'DOC/PUSAT/2026',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'pusat_id' => $pusat->id,
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);
        $coop->pusats()->sync([$pusat->id]);

        $response = $this->actingAs($userPusat)->get(route('pusat.analitik.status-kerjasama'));
        $response->assertStatus(200);
        $response->assertViewHas('statusKerjasamaData');

        $responsePartial = $this->actingAs($userPusat)->getJson(route('pusat.analitik.status-kerjasama', ['partial' => 'due_date']));
        $responsePartial->assertStatus(200);
        $responsePartial->assertJsonStructure(['dueDateData' => ['year', 'rows', 'weeks']]);
    }

    public function test_all_roles_can_access_klasifikasi_mitra()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $userUnit = User::factory()->create(['role_id' => $roleUnit->id]);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas']);
        Profile::create(['user_id' => $userUnit->id, 'unit_kerja_id' => $unitKerja->id]);

        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $userJurusan = User::factory()->create(['role_id' => $roleJurusan->id]);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Mesin']);
        Profile::create(['user_id' => $userJurusan->id, 'jurusan_id' => $jurusan->id]);

        $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $userUpa = User::factory()->create(['role_id' => $roleUpa->id]);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Bahasa']);
        Profile::create(['user_id' => $userUpa->id, 'upa_id' => $upa->id]);

        $rolePusat = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $userPusat = User::factory()->create(['role_id' => $rolePusat->id]);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Penelitian']);
        Profile::create(['user_id' => $userPusat->id, 'pusat_id' => $pusat->id]);

        $responseUnit = $this->actingAs($userUnit)->get(route('unit.analitik.klasifikasi-mitra'));
        $responseUnit->assertStatus(200);

        $responseJurusan = $this->actingAs($userJurusan)->get(route('jurusan.analitik.klasifikasi-mitra'));
        $responseJurusan->assertStatus(200);

        $responseUpa = $this->actingAs($userUpa)->get(route('upa.analitik.klasifikasi-mitra'));
        $responseUpa->assertStatus(200);

        $responsePusat = $this->actingAs($userPusat)->get(route('pusat.analitik.klasifikasi-mitra'));
        $responsePusat->assertStatus(200);
    }

    public function test_all_roles_can_access_geo_mitra()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $userUnit = User::factory()->create(['role_id' => $roleUnit->id]);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas']);
        Profile::create(['user_id' => $userUnit->id, 'unit_kerja_id' => $unitKerja->id]);

        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $userJurusan = User::factory()->create(['role_id' => $roleJurusan->id]);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Sipil']);
        Profile::create(['user_id' => $userJurusan->id, 'jurusan_id' => $jurusan->id]);

        $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $userUpa = User::factory()->create(['role_id' => $roleUpa->id]);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Percetakan']);
        Profile::create(['user_id' => $userUpa->id, 'upa_id' => $upa->id]);

        $rolePusat = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $userPusat = User::factory()->create(['role_id' => $rolePusat->id]);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Inovasi']);
        Profile::create(['user_id' => $userPusat->id, 'pusat_id' => $pusat->id]);

        $mitra1 = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Indo Geo', 'country_code' => 'ID', 'negara' => 'Indonesia', 'status_akses' => 'Aktif']);
        $mitra2 = Mitra::firstOrCreate(['nama_mitra' => 'Mitra Japan Geo', 'country_code' => 'JP', 'negara' => 'Jepang', 'status_akses' => 'Aktif']);

        $responseUnit = $this->actingAs($userUnit)->get(route('unit.analitik.geo-mitra'));
        $responseUnit->assertStatus(200);
        $responseUnit->assertViewHas('categoryChartData');
        $responseUnit->assertViewHas('countryChartData');

        $responseJurusan = $this->actingAs($userJurusan)->get(route('jurusan.analitik.geo-mitra'));
        $responseJurusan->assertStatus(200);
        $responseJurusan->assertViewHas('categoryChartData');

        $responseUpa = $this->actingAs($userUpa)->get(route('upa.analitik.geo-mitra'));
        $responseUpa->assertStatus(200);
        $responseUpa->assertViewHas('categoryChartData');

        $responsePusat = $this->actingAs($userPusat)->get(route('pusat.analitik.geo-mitra'));
        $responsePusat->assertStatus(200);
        $responsePusat->assertViewHas('categoryChartData');
    }
}

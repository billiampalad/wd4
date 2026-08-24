<?php

namespace Tests\Feature\Admin\up;

use App\Models\Role;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\Upa;
use App\Models\Pusat;
use App\Models\UnitKerja;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class UnitPelaksanaTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'email' => 'admin_up_' . rand(1,999) . '@wd4.com',
        ]);
    }

    // ==============================================
    // PENGUJIAN JURUSAN
    // ==============================================
    public function test_admin_can_view_jurusan_list()
    {
        $response = $this->actingAs($this->adminUser)->get(route('jurusan.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.layout.jurusan');
    }

    public function test_admin_can_create_jurusan()
    {
        $response = $this->actingAs($this->adminUser)->post(route('jurusan.store'), [
            'kode_jurusan' => 'JUR-' . rand(100, 999),
            'nama_jurusan' => 'Jurusan Testing ' . rand(100, 999),
        ]);

        $response->assertRedirect(route('jurusan.index'));
        $response->assertSessionHas('success');
    }

    // ==============================================
    // PENGUJIAN PRODI
    // ==============================================
    public function test_admin_can_view_prodi_list()
    {
        $response = $this->actingAs($this->adminUser)->get(route('prodi.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.layout.prodi');
    }

    public function test_admin_can_create_prodi()
    {
        $jurusan = Jurusan::firstOrCreate([
            'kode_jurusan' => 'JUR-PRD',
            'nama_jurusan' => 'Jurusan For Prodi'
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('prodi.store'), [
            'jurusan_id' => $jurusan->id,
            'kode_prodi' => 'PRD-' . rand(100, 999),
            'nama_prodi' => 'Prodi Testing ' . rand(100, 999),
            'jenjang' => 'D4',
        ]);

        $response->assertRedirect(route('prodi.index'));
        $response->assertSessionHas('success');
    }

    // ==============================================
    // PENGUJIAN UPA
    // ==============================================
    public function test_admin_can_view_upa_list()
    {
        $response = $this->actingAs($this->adminUser)->get(route('upa.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.layout.upa');
    }

    public function test_admin_can_create_upa()
    {
        $response = $this->actingAs($this->adminUser)->post(route('upa.store'), [
            'nama_upa' => 'UPA Testing ' . rand(100, 999),
        ]);

        $response->assertRedirect(route('upa.index'));
        $response->assertSessionHas('success');
    }

    // ==============================================
    // PENGUJIAN PUSAT
    // ==============================================
    public function test_admin_can_view_pusat_list()
    {
        $response = $this->actingAs($this->adminUser)->get(route('pusat.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.layout.pusat');
    }

    public function test_admin_can_create_pusat()
    {
        $response = $this->actingAs($this->adminUser)->post(route('pusat.store'), [
            'nama_pusat' => 'Pusat Testing ' . rand(100, 999),
        ]);

        $response->assertRedirect(route('pusat.index'));
        $response->assertSessionHas('success');
    }

    // ==============================================
    // PENGUJIAN UNIT KERJA (HUMAS)
    // ==============================================
    public function test_admin_can_view_upelaksana_list()
    {
        $response = $this->actingAs($this->adminUser)->get(route('upelaksana.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.layout.unit');
    }

    public function test_admin_can_create_upelaksana()
    {
        $response = $this->actingAs($this->adminUser)->post(route('upelaksana.store'), [
            'nama_unit_pelaksana' => 'Unit Testing ' . rand(100, 999),
        ]);

        $response->assertRedirect(route('upelaksana.index'));
        $response->assertSessionHas('success');
    }
}

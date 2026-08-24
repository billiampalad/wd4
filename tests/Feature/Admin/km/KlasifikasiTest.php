<?php

namespace Tests\Feature\Admin\up;

use App\Models\Role;
use App\Models\User;
use App\Models\Klasifikasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class KlasifikasiTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'email' => 'admin_klas_' . rand(1,999) . '@wd4.com',
        ]);
    }

    public function test_admin_can_view_klasifikasi_list()
    {
        $response = $this->actingAs($this->adminUser)->get(route('klasifikasi.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.klasifikasi.index');
    }

    public function test_admin_can_create_klasifikasi()
    {
        $namaKlas = 'Klasifikasi Tes ' . rand(100, 999);
        $response = $this->actingAs($this->adminUser)->post(route('klasifikasi.store'), [
            'nama' => $namaKlas,
        ]);

        $response->assertRedirect(route('klasifikasi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('klasifikasis', [
            'nama' => $namaKlas,
        ]);
    }

    public function test_admin_cannot_create_duplicate_klasifikasi()
    {
        $klas = Klasifikasi::create(['nama' => 'Duplikat Klasifikasi']);

        $response = $this->actingAs($this->adminUser)->post(route('klasifikasi.store'), [
            'nama' => $klas->nama,
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_admin_can_update_klasifikasi()
    {
        $klas = Klasifikasi::create(['nama' => 'Lama Klasifikasi']);
        $namaBaru = 'Baru Klasifikasi';

        $response = $this->actingAs($this->adminUser)->put(route('klasifikasi.update', $klas->id), [
            'nama' => $namaBaru,
        ]);

        $response->assertRedirect(route('klasifikasi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('klasifikasis', [
            'id' => $klas->id,
            'nama' => $namaBaru,
        ]);
    }

    public function test_admin_can_delete_klasifikasi()
    {
        $klas = Klasifikasi::create(['nama' => 'Hapus Klasifikasi']);

        $response = $this->actingAs($this->adminUser)->delete(route('klasifikasi.destroy', $klas->id));

        $response->assertRedirect(route('klasifikasi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('klasifikasis', [
            'id' => $klas->id,
        ]);
    }
}

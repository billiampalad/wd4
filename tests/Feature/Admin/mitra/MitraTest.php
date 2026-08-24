<?php

namespace Tests\Feature\Admin\mitra;

use App\Models\Role;
use App\Models\User;
use App\Models\Mitra;
use App\Models\Klasifikasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class MitraTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $adminUser;
    protected $klasifikasi;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'email' => 'admin_mitra@wd4.com',
        ]);

        $this->klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
    }

    public function test_admin_can_view_mitra_list()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('mitra.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.mitra.index');
    }

    public function test_admin_can_create_mitra()
    {
        $namaMitra = 'PT Testing ' . $this->faker->unique()->word;

        $response = $this->actingAs($this->adminUser)
            ->post(route('mitra.store'), [
                'nama_mitra' => $namaMitra,
                'id_klasifikasi' => $this->klasifikasi->id,
                'kategori' => 'nasional',
                'negara' => 'Indonesia',
                'alamat' => 'Jl. Test No. 1',
                'telp' => '081234567890',
                'website' => 'https://test.com'
            ]);

        $response->assertRedirect(route('mitra.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('mitras', [
            'nama_mitra' => $namaMitra,
            'klasifikasi_id' => $this->klasifikasi->id,
            'telepon' => '081234567890',
        ]);
    }

    public function test_admin_cannot_create_duplicate_mitra()
    {
        $existing = Mitra::create([
            'nama_mitra' => 'Mitra Duplikat',
            'negara' => 'Indonesia'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('mitra.store'), [
                'nama_mitra' => $existing->nama_mitra,
                'id_klasifikasi' => $this->klasifikasi->id,
                'kategori' => 'nasional',
                'negara' => 'Indonesia',
            ]);

        $response->assertSessionHasErrors('nama_mitra');
    }

    public function test_admin_can_update_mitra()
    {
        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra Lama',
            'negara' => 'Indonesia'
        ]);

        $updatedName = 'Mitra Baru Updated';

        $response = $this->actingAs($this->adminUser)
            ->put(route('mitra.update', $mitra->id), [
                'nama_mitra' => $updatedName,
                'id_klasifikasi' => $this->klasifikasi->id,
                'kategori' => 'internasional',
                'negara' => 'Singapura',
                'telp' => '99999',
            ]);

        $response->assertRedirect(route('mitra.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('mitras', [
            'id' => $mitra->id,
            'nama_mitra' => $updatedName,
            'negara' => 'Singapura',
            'telepon' => '99999',
        ]);
    }

    public function test_admin_can_delete_mitra_if_not_used()
    {
        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra Akan Dihapus',
            'negara' => 'Indonesia'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('mitra.destroy', $mitra->id));

        $response->assertRedirect(route('mitra.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('mitras', [
            'id' => $mitra->id,
        ]);
    }

    public function test_admin_cannot_delete_mitra_if_used()
    {
        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra Digunakan',
            'negara' => 'Indonesia'
        ]);
        
        DB::table('cooperations')->insert([
            'mitra_id' => $mitra->id,
            'doc_number' => 'TEST/123',
            'judul' => 'Kerjasama Test',
            'jenis' => 'MOU',
            'status_berlaku' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('mitra.destroy', $mitra->id));

        $response->assertRedirect(route('mitra.index'));
        $response->assertSessionHas('error'); // Should have error session

        // Ensure it is NOT deleted
        $this->assertDatabaseHas('mitras', [
            'id' => $mitra->id,
        ]);
    }
}

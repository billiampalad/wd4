<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\JenisKerjasama;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\KegiatanKerjasama;
use Illuminate\Support\Facades\DB;

class JenisKerjasamaTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'email' => 'admin_jk@wd4.com',
        ]);
    }

    public function test_admin_can_view_jenis_kerjasama_list()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('jkerjasama.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.layout.jkerjasama');
    }

    public function test_admin_can_create_jenis_kerjasama()
    {
        $namaKerjasama = 'Penelitian Bersama ' . $this->faker->unique()->word;

        $response = $this->actingAs($this->adminUser)
            ->post(route('jkerjasama.store'), [
                'nama_kerjasama' => $namaKerjasama,
            ]);

        $response->assertRedirect(route('jkerjasama.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('jenis_kerjasamas', [
            'nama_kerjasama' => $namaKerjasama,
        ]);
    }

    public function test_admin_cannot_create_duplicate_jenis_kerjasama()
    {
        $existing = JenisKerjasama::firstOrCreate(['nama_kerjasama' => 'Pertukaran Pelajar']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('jkerjasama.store'), [
                'nama_kerjasama' => $existing->nama_kerjasama,
            ]);

        $response->assertSessionHasErrors('nama_kerjasama');
    }

    public function test_admin_can_update_jenis_kerjasama()
    {
        $jk = JenisKerjasama::create(['nama_kerjasama' => 'Magang Mandiri']);
        $updatedName = 'Magang Mandiri Terstruktur';

        $response = $this->actingAs($this->adminUser)
            ->put(route('jkerjasama.update', $jk->id), [
                'nama_kerjasama' => $updatedName,
            ]);

        $response->assertRedirect(route('jkerjasama.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('jenis_kerjasamas', [
            'id' => $jk->id,
            'nama_kerjasama' => $updatedName,
        ]);
    }

    public function test_admin_can_delete_jenis_kerjasama_if_not_used()
    {
        $jk = JenisKerjasama::create(['nama_kerjasama' => 'Akan Dihapus']);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('jkerjasama.destroy', $jk->id));

        $response->assertRedirect(route('jkerjasama.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('jenis_kerjasamas', [
            'id' => $jk->id,
        ]);
    }

    public function test_admin_cannot_delete_jenis_kerjasama_if_used()
    {
        $jk = JenisKerjasama::create(['nama_kerjasama' => 'Tidak Boleh Dihapus']);
        
        // Simulate usage by attaching to a KegiatanKerjasama (if factory exists, else manual DB insert)
        $kegiatanId = DB::table('kegiatan_kerjasamas')->insertGetId([
            'judul_kegiatan' => 'Kegiatan Test',
            'deskripsi' => 'test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('kegiatan_jenis_kerjasamas')->insert([
            'id_kegiatan' => $kegiatanId,
            'id_jenis' => $jk->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('jkerjasama.destroy', $jk->id));

        // According to flowchart, should fail with error message
        $response->assertRedirect();
        $response->assertSessionHas('error'); // Should have error session

        // Ensure it is NOT deleted
        $this->assertDatabaseHas('jenis_kerjasamas', [
            'id' => $jk->id,
        ]);
    }
}

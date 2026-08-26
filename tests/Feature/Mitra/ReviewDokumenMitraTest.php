<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\Mitra;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReviewDokumenMitraTest extends TestCase
{
    use DatabaseTransactions;

    public function test_mitra_can_view_dokumen_list()
    {
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Penguji', 'status_akses' => 'Aktif']);

        $mitraUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
        ]);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Mitra Test List',
            'jenis' => 'MoU',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $response = $this->actingAs($mitraUser)->get(route('mitra.dokumen.index'));
        $response->assertStatus(200);
        $response->assertSee('Kerjasama Mitra Test List');
    }

    public function test_mitra_can_send_review_dokumen()
    {
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);
        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);

        $pengusulUser = User::factory()->create([
            'role_id' => $roleJurusan->id,
        ]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Pemberi Review', 'status_akses' => 'Aktif']);

        $mitraUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
        ]);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Mitra Test Review',
            'jenis' => 'MoA',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Jurusan',
            'created_by' => $pengusulUser->id,
        ]);

        $response = $this->actingAs($mitraUser)->post(route('mitra.dokumen.review', $coop->id), [
            'catatan_review' => 'Mohon tambahkan pasal mengenai kerahasiaan data.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pengusulUser->id,
            'sender_id' => $mitraUser->id,
            'source_id' => $coop->id,
            'type' => 'review_draf',
        ]);
    }
}

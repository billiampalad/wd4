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
            'action' => 'revisi',
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

    public function test_mitra_can_approve_draft_without_notes()
    {
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);
        $roleUpa = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);

        $pengusulUser = User::factory()->create([
            'role_id' => $roleUpa->id,
        ]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Setuju Draf', 'status_akses' => 'Aktif']);

        $mitraUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
        ]);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Mitra Test Setuju',
            'jenis' => 'IA',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Jurusan',
            'created_by' => $pengusulUser->id,
        ]);

        $response = $this->actingAs($mitraUser)->post(route('mitra.dokumen.review', $coop->id), [
            'action' => 'setuju',
            'catatan_review' => null,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pengusulUser->id,
            'sender_id' => $mitraUser->id,
            'source_id' => $coop->id,
            'type' => 'review_draf_setuju',
        ]);
    }

    public function test_mitra_cannot_review_other_mitra_document()
    {
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);

        $mitra1 = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Asal', 'status_akses' => 'Aktif']);
        $mitra2 = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Lain', 'status_akses' => 'Aktif']);

        $mitraUser1 = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra1->id,
        ]);

        $coopMitra2 = Cooperation::create([
            'judul' => 'Kerjasama Milik Mitra Lain',
            'jenis' => 'MoU',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra2->id,
            'tingkat' => 'Institusi',
        ]);

        $response = $this->actingAs($mitraUser1)->post(route('mitra.dokumen.review', $coopMitra2->id), [
            'action' => 'revisi',
            'catatan_review' => 'Catatan tidak sah.',
        ]);

        $response->assertStatus(404);
    }

    public function test_mitra_can_filter_dokumen_list()
    {
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Penguji Filter', 'status_akses' => 'Aktif']);

        $mitraUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
        ]);

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Mitra Test Filter',
            'jenis' => 'MoA',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Jurusan',
            'start_date' => '2024-01-01',
        ]);

        $response = $this->actingAs($mitraUser)->get(route('mitra.dokumen.index'));
        $response->assertStatus(200);
        $response->assertSee('Kerjasama Mitra Test Filter');
        
        // Assert view contains the Vue/Alpine models
        $response->assertSee('jenisFilter');
        $response->assertSee('periodeFilter');
        $response->assertSee('MoA');
        $response->assertSee('2024');
    }
}

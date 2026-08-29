<?php

namespace Tests\Feature\Pimpinan;

use App\Models\Cooperation;
use App\Models\Jurusan;
use App\Models\Mitra;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EvaluasiPimpinanTest extends TestCase
{
    use DatabaseTransactions;

    public function test_pimpinan_can_evaluate_layak()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        $pimpinanUser = User::factory()->create(['role_id' => $rolePimpinan->id]);
        Profile::create(['user_id' => $pimpinanUser->id]);

        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Jurusan Teknik']);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Test', 'status_akses' => 'Aktif']);
        
        $coop = Cooperation::create([
            'judul' => 'Draft Evaluasi Test',
            'jenis' => 'MoA',
            'status_dokumen' => 'Menunggu Evaluasi',
            'status_berlaku' => 'aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $jurusan->id
        ]);
        $coop->jurusans()->sync([$jurusan->id]);

        $response = $this->actingAs($pimpinanUser)->post(route('pimpinan.evaluate', $coop->id), [
            'status_validasi' => 'layak',
            'ringkasan' => 'Bagus sekali',
            'saran' => 'Lanjutkan',
            'tindak_lanjut' => 'Tanda tangan',
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 5,
            'efisiensi' => 5,
            'kepuasan' => 5,
        ]);

        $response->assertRedirect(route('pimpinan.evaluasi'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cooperations', [
            'id' => $coop->id,
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
        ]);

        $this->assertDatabaseHas('evaluasis', [
            'cooperation_id' => $coop->id,
            'evaluator_id' => $pimpinanUser->id,
            'status_validasi' => 'Divalidasi',
            'sesuai_rencana' => 5,
        ]);
    }

    public function test_pimpinan_can_evaluate_revisi()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        $pimpinanUser = User::factory()->create(['role_id' => $rolePimpinan->id]);
        Profile::create(['user_id' => $pimpinanUser->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Test', 'status_akses' => 'Aktif']);
        
        $coop = Cooperation::create([
            'judul' => 'Draft Revisi Test',
            'jenis' => 'MoU',
            'status_dokumen' => 'Menunggu Evaluasi',
            'status_berlaku' => 'aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $response = $this->actingAs($pimpinanUser)->post(route('pimpinan.evaluate', $coop->id), [
            'status_validasi' => 'revisi',
            'ringkasan' => 'Ada yang kurang',
            'saran' => 'Lengkapi dokumen lampiran',
            'tindak_lanjut' => 'Revisi kembali',
        ]);

        $response->assertRedirect(route('pimpinan.evaluasi'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cooperations', [
            'id' => $coop->id,
            'status_dokumen' => 'Revisi',
        ]);

        $this->assertDatabaseHas('evaluasis', [
            'cooperation_id' => $coop->id,
            'evaluator_id' => $pimpinanUser->id,
            'status_validasi' => 'Perlu Revisi',
            'rekomendasi' => 'Lengkapi dokumen lampiran',
        ]);
    }

    public function test_pimpinan_can_view_monitoring_page()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        $pimpinanUser = User::factory()->create(['role_id' => $rolePimpinan->id]);
        Profile::create(['user_id' => $pimpinanUser->id]);

        $response = $this->actingAs($pimpinanUser)->get(route('pimpinan.monitoring'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.pimpinan');
        $response->assertViewHas('view', 'monitoring');
        $response->assertViewHas('totalNilaiKontrakAktif');
    }

    public function test_pimpinan_can_view_monitoring_detail_page()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        $pimpinanUser = User::factory()->create(['role_id' => $rolePimpinan->id]);
        Profile::create(['user_id' => $pimpinanUser->id]);

        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Detail Test', 'status_akses' => 'Aktif']);
        $coop = Cooperation::create([
            'judul' => 'Kerjasama Detail Monitoring Test',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'ruang_lingkup' => 'Penyelenggaraan magang dan riset bersama',
        ]);

        $response = $this->actingAs($pimpinanUser)->get(route('pimpinan.monitoring.detail', $coop->id));

        $response->assertStatus(200);
        $response->assertViewIs('auth.pimpinan');
        $response->assertViewHas('view', 'detail_monitoring');
        $response->assertSee('Kerjasama Detail Monitoring Test');
        $response->assertSee('PT Mitra Detail Test');
        $response->assertSee('Penyelenggaraan magang dan riset bersama');
    }
}

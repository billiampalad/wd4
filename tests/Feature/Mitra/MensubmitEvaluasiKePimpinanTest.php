<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\Jurusan;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use App\Models\Notifikasi;
use App\Models\Profile;
use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MensubmitEvaluasiKePimpinanTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupPimpinanUser()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);

        return User::factory()->create([
            'role_id' => $rolePimpinan->id,
            'name' => 'Direktur Polimdo',
            'email' => 'direktur.eval@polimdo.ac.id',
        ]);
    }

    protected function setupUnitUser()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Bagian Kerja Sama & Humas']);

        $user = User::factory()->create([
            'role_id' => $roleUnit->id,
            'name' => 'Staf Humas Kerja Sama',
            'email' => 'humas.submit@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'unit_kerja_id' => $unitKerja->id,
            'nip' => '198501012010121005',
        ]);

        return [$user, $unitKerja];
    }

    protected function setupJurusanUser()
    {
        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);

        $user = User::factory()->create([
            'role_id' => $roleJurusan->id,
            'name' => 'Kajur Elektro Polimdo',
            'email' => 'kajur.submit@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'jurusan_id' => $jurusan->id,
            'nip' => '197901012005011005',
        ]);

        return [$user, $jurusan];
    }

    public function test_unit_user_can_submit_evaluasi_draft_to_pimpinan()
    {
        $pimpinan = $this->setupPimpinanUser();
        [$user, $unitKerja] = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Teknologi']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Cloud Hosting Indonesia'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'MoA Cloud Data Center Polimdo',
            'jenis' => 'MoA',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        // Create Evaluasi in Draft
        $eval = Evaluasi::create([
            'cooperation_id' => $coop->id,
            'evaluator_id' => $user->id,
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 5,
            'efisiensi' => 4,
            'kepuasan' => 5,
            'status_validasi' => 'Draft',
        ]);

        $response = $this->actingAs($user)->post(route('unit.evaluasi.submit', $coop->id));

        $response->assertRedirect(route('unit.evaluasi'));
        $response->assertSessionHas('success');

        // 1. Assert Cooperation status_dokumen updated to 'Menunggu Evaluasi'
        $this->assertDatabaseHas('cooperations', [
            'id' => $coop->id,
            'status_dokumen' => 'Menunggu Evaluasi',
        ]);

        // 2. Assert Evaluasi status_validasi updated to 'Menunggu Validasi'
        $this->assertDatabaseHas('evaluasis', [
            'id' => $eval->id,
            'status_validasi' => 'Menunggu Validasi',
        ]);

        // 3. Assert Notification sent to Pimpinan
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pimpinan->id,
            'sender_id' => $user->id,
            'source_id' => $coop->id,
            'type' => 'validasi',
        ]);
    }

    public function test_cannot_submit_evaluasi_when_evaluasi_has_not_been_filled()
    {
        $this->setupPimpinanUser();
        [$user, $unitKerja] = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Kosong'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'Kerja Sama Belum Dievaluasi',
            'jenis' => 'IA',
            'status_dokumen' => 'Draft',
            'mitra_id' => $mitra->id,
        ]);

        // No Evaluasi record created!

        $response = $this->actingAs($user)->post(route('unit.evaluasi.submit', $coop->id));

        $response->assertSessionHas('error');

        // Status remains Draft
        $this->assertDatabaseHas('cooperations', [
            'id' => $coop->id,
            'status_dokumen' => 'Draft',
        ]);
    }

    public function test_jurusan_user_can_submit_evaluasi_to_pimpinan()
    {
        $pimpinan = $this->setupPimpinanUser();
        [$kajurUser, $jurusan] = $this->setupJurusanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT PLN Manado'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'MoA Smart Energy Lab',
            'jenis' => 'MoA',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
        ]);

        $coop->jurusans()->attach($jurusan->id);

        $eval = Evaluasi::create([
            'cooperation_id' => $coop->id,
            'evaluator_id' => $kajurUser->id,
            'sesuai_rencana' => 4,
            'kualitas' => 5,
            'keterlibatan' => 4,
            'efisiensi' => 4,
            'kepuasan' => 5,
            'status_validasi' => 'Draft',
        ]);

        $response = $this->actingAs($kajurUser)->post(route('jurusan.evaluasi.submit', $coop->id));

        $response->assertRedirect(route('jurusan.evaluasi'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cooperations', [
            'id' => $coop->id,
            'status_dokumen' => 'Menunggu Evaluasi',
        ]);

        $this->assertDatabaseHas('evaluasis', [
            'id' => $eval->id,
            'status_validasi' => 'Menunggu Validasi',
        ]);

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pimpinan->id,
            'sender_id' => $kajurUser->id,
            'source_id' => $coop->id,
            'type' => 'validasi',
        ]);
    }
}

<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\Jurusan;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use App\Models\Profile;
use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MengisiFormEvaluasiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupUnitUser()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Bagian Kerja Sama & Humas']);

        $user = User::factory()->create([
            'role_id' => $roleUnit->id,
            'name' => 'Staf Humas Kerjasama',
            'email' => 'humas.evaluasi@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'unit_kerja_id' => $unitKerja->id,
            'nip' => '198501012010121001',
        ]);

        return [$user, $unitKerja];
    }

    protected function setupJurusanUser()
    {
        $roleJurusan = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);

        $user = User::factory()->create([
            'role_id' => $roleJurusan->id,
            'name' => 'Ketua Jurusan Elektro',
            'email' => 'kajur.elektro@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'jurusan_id' => $jurusan->id,
            'nip' => '197901012005011002',
        ]);

        return [$user, $jurusan];
    }

    public function test_unit_user_can_access_evaluasi_index()
    {
        [$user, $unitKerja] = $this->setupUnitUser();

        $response = $this->actingAs($user)->get(route('unit.evaluasi'));

        $response->assertStatus(200);
        $response->assertSee('Evaluasi Kinerja Kerjasama');
        $response->assertSee('Bagian Kerja Sama &amp; Humas', false);
    }

    public function test_unit_user_can_access_form_evaluasi_for_cooperation()
    {
        [$user, $unitKerja] = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Manufaktur']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Komatsu Indonesia'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'Kerja Sama Pelatihan Alat Berat Komatsu',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/KOM/2026/01',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => '2026-01-01',
            'end_date' => '2028-01-01',
        ]);

        $response = $this->actingAs($user)->get(route('unit.evaluasi.form', $coop->id));

        $response->assertStatus(200);
        $response->assertSee('Beri Evaluasi Kinerja');
        $response->assertSee('Kesesuaian dengan Rencana');
        $response->assertSee('Kualitas Output');
        $response->assertSee('Kepuasan Keseluruhan');
    }

    public function test_unit_user_can_store_evaluasi_successfully()
    {
        [$user, $unitKerja] = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Teknologi']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Cisco Systems Indonesia'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'Kerja Sama Cisco Networking Academy',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/CSCO/2026/08',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => '2026-02-01',
            'end_date' => '2027-02-01',
        ]);

        $payload = [
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 4,
            'efisiensi' => 5,
            'kepuasan' => 5,
            'catatan' => 'Pelaksanaan program sertifikasi Cisco berjalan sangat memuaskan dan mencapai target kelulusan.',
        ];

        $response = $this->actingAs($user)->post(route('unit.evaluasi.store', $coop->id), $payload);

        $response->assertRedirect(route('unit.evaluasi'));
        $response->assertSessionHas('success');

        // 1. Assert record created in evaluasis
        $this->assertDatabaseHas('evaluasis', [
            'cooperation_id' => $coop->id,
            'evaluator_id' => $user->id,
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 4,
            'efisiensi' => 5,
            'kepuasan' => 5,
        ]);

        // 2. Assert cooperation status updated
        $this->assertDatabaseHas('cooperations', [
            'id' => $coop->id,
            'status_dokumen' => 'Menunggu Evaluasi',
        ]);
    }

    public function test_evaluasi_validation_errors_when_required_scores_missing()
    {
        [$user, $unitKerja] = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Evaluasi'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'Kerja Sama Uji Validasi Evaluasi',
            'jenis' => 'IA',
            'status_dokumen' => 'Draft',
            'mitra_id' => $mitra->id,
        ]);

        $response = $this->actingAs($user)->post(route('unit.evaluasi.store', $coop->id), []);

        $response->assertSessionHasErrors([
            'sesuai_rencana',
            'kualitas',
            'keterlibatan',
            'efisiensi',
            'kepuasan',
        ]);
    }

    public function test_jurusan_user_can_fill_and_store_evaluasi()
    {
        [$kajurUser, $jurusan] = $this->setupJurusanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT PLN Icon Plus'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'MoA Fiber Optic Smart Grid',
            'jenis' => 'MoA',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
        ]);

        // Attach jurusan to cooperation
        $coop->jurusans()->attach($jurusan->id);

        $payload = [
            'sesuai_rencana' => 4,
            'kualitas' => 4,
            'keterlibatan' => 5,
            'efisiensi' => 4,
            'kepuasan' => 4,
            'catatan' => 'Sangat bermanfaat untuk peningkatan skill mahasiswa elektro.',
        ];

        $response = $this->actingAs($kajurUser)->post(route('jurusan.evaluasi.store', $coop->id), $payload);

        $response->assertRedirect(route('jurusan.evaluasi'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('evaluasis', [
            'cooperation_id' => $coop->id,
            'evaluator_id' => $kajurUser->id,
            'sesuai_rencana' => 4,
            'kualitas' => 4,
        ]);
    }
}

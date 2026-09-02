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

class MemvalidasiEvaluasiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupPimpinanUser()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);

        return User::factory()->create([
            'role_id' => $rolePimpinan->id,
            'name' => 'Direktur Polimdo',
            'email' => 'direktur.val@polimdo.ac.id',
        ]);
    }

    protected function setupUnitUser()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Bagian Kerja Sama & Humas']);

        $user = User::factory()->create([
            'role_id' => $roleUnit->id,
            'name' => 'Staf Humas Kerja Sama',
            'email' => 'humas.val@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'unit_kerja_id' => $unitKerja->id,
            'nip' => '198501012010121008',
        ]);

        return [$user, $unitKerja];
    }

    public function test_pimpinan_can_access_evaluasi_list()
    {
        $pimpinan = $this->setupPimpinanUser();

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.evaluasi'));

        $response->assertStatus(200);
        $response->assertSee('Evaluasi');
    }

    public function test_pimpinan_can_view_evaluasi_detail()
    {
        $pimpinan = $this->setupPimpinanUser();
        [$unitUser] = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Telekomunikasi']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Indosat Ooredoo Hutchison'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'Kerja Sama 5G Experience Center',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/ISAT/2026/04',
            'status_dokumen' => 'Menunggu Evaluasi',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => '2026-03-01',
            'end_date' => '2028-03-01',
        ]);

        Evaluasi::create([
            'cooperation_id' => $coop->id,
            'evaluator_id' => $unitUser->id,
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 4,
            'efisiensi' => 5,
            'kepuasan' => 5,
            'status_validasi' => 'Menunggu Validasi',
        ]);

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.evaluasi.show', $coop->id));

        $response->assertStatus(200);
        $response->assertSee('Kerja Sama 5G Experience Center');
        $response->assertSee('PT Indosat Ooredoo Hutchison');
    }

    public function test_pimpinan_can_approve_evaluasi_and_disahkan_cooperation()
    {
        $pimpinan = $this->setupPimpinanUser();
        [$unitUser, $unitKerja] = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Huawei Tech Investment'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'MoA Huawei ICT Academy',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/HWI/2026/01',
            'status_dokumen' => 'Menunggu Evaluasi',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => '2026-01-01',
            'end_date' => '2028-01-01',
        ]);

        $eval = Evaluasi::create([
            'cooperation_id' => $coop->id,
            'evaluator_id' => $unitUser->id,
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 5,
            'efisiensi' => 5,
            'kepuasan' => 5,
            'status_validasi' => 'Menunggu Validasi',
        ]);

        $payload = [
            'status_validasi' => 'layak',
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 5,
            'efisiensi' => 5,
            'kepuasan' => 5,
            'ringkasan' => 'Pelaksanaan program sertifikasi Huawei sangat optimal.',
            'saran' => 'Pertahankan dan tingkatkan kuota peserta tahun depan.',
            'tindak_lanjut' => 'Lanjutkan implementasi kegiatan.',
        ];

        $response = $this->actingAs($pimpinan)->post(route('pimpinan.evaluate', $coop->id), $payload);

        $response->assertRedirect(route('pimpinan.evaluasi'));
        $response->assertSessionHas('success');

        // 1. Assert cooperation updated to Disahkan
        $this->assertDatabaseHas('cooperations', [
            'id' => $coop->id,
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
        ]);

        // 2. Assert evaluasi updated to Divalidasi
        $this->assertDatabaseHas('evaluasis', [
            'id' => $eval->id,
            'status_validasi' => 'Divalidasi',
        ]);

        // 3. Assert notification created for unit user
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $unitUser->id,
            'sender_id' => $pimpinan->id,
            'source_id' => $coop->id,
            'type' => 'disahkan',
        ]);
    }

    public function test_pimpinan_can_request_revision_on_evaluasi()
    {
        $pimpinan = $this->setupPimpinanUser();
        [$unitUser, $unitKerja] = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Revisi Evaluasi'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'MoA Uji Coba Revisi',
            'jenis' => 'MoA',
            'status_dokumen' => 'Menunggu Evaluasi',
            'mitra_id' => $mitra->id,
            'start_date' => '2026-01-01',
            'end_date' => '2027-01-01',
        ]);

        $eval = Evaluasi::create([
            'cooperation_id' => $coop->id,
            'evaluator_id' => $unitUser->id,
            'sesuai_rencana' => 3,
            'kualitas' => 3,
            'keterlibatan' => 3,
            'efisiensi' => 3,
            'kepuasan' => 3,
            'status_validasi' => 'Menunggu Validasi',
        ]);

        $payload = [
            'status_validasi' => 'revisi',
            'saran' => 'Mohon lengkapi data realisasi luaran mahasiswa dan data serapan industri.',
        ];

        $response = $this->actingAs($pimpinan)->post(route('pimpinan.evaluate', $coop->id), $payload);

        $response->assertRedirect(route('pimpinan.evaluasi'));
        $response->assertSessionHas('success');

        // 1. Assert cooperation updated to Revisi
        $this->assertDatabaseHas('cooperations', [
            'id' => $coop->id,
            'status_dokumen' => 'Revisi',
        ]);

        // 2. Assert evaluasi updated to Perlu Revisi
        $this->assertDatabaseHas('evaluasis', [
            'id' => $eval->id,
            'status_validasi' => 'Perlu Revisi',
        ]);

        // 3. Assert notification created for unit user
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $unitUser->id,
            'sender_id' => $pimpinan->id,
            'source_id' => $coop->id,
            'type' => 'revisi',
        ]);
    }
}

<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\Evaluasi;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use App\Models\Notifikasi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberiUmpanBalikKerjasamaTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupMitraUser()
    {
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Manufaktur']);

        $mitra = Mitra::create([
            'nama_mitra' => 'PT Astra Honda Motor',
            'id_klasifikasi' => $klasifikasi->id,
            'email' => 'contact@ahm.co.id',
        ]);

        $user = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'PIC Astra Honda Motor',
            'email' => 'pic.ahm@astra-honda.com',
        ]);

        return [$user, $mitra];
    }

    protected function setupPimpinanUser()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);

        return User::factory()->create([
            'role_id' => $rolePimpinan->id,
            'name' => 'Direktur Polimdo',
            'email' => 'direktur.csat@polimdo.ac.id',
        ]);
    }

    public function test_guest_cannot_access_umpan_balik()
    {
        $response = $this->get(route('mitra.umpan_balik.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_mitra_can_access_umpan_balik_index_and_view_cooperations()
    {
        [$user, $mitra] = $this->setupMitraUser();

        $coop = Cooperation::create([
            'judul' => 'Kerja Sama Vokasi Otomotif AHM',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/AHM/2026/01',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => '2026-01-01',
            'end_date' => '2028-01-01',
        ]);

        $response = $this->actingAs($user)->get(route('mitra.umpan_balik.index'));

        $response->assertStatus(200);
        $response->assertSee('Umpan Balik');
        $response->assertSee('Kerja Sama Vokasi Otomotif AHM');
        $response->assertSee('PT Astra Honda Motor');
    }

    public function test_mitra_can_submit_umpan_balik_feedback()
    {
        $pimpinan = $this->setupPimpinanUser();
        [$user, $mitra] = $this->setupMitraUser();

        $coop = Cooperation::create([
            'judul' => 'Kerja Sama Rekrutmen & Magang Industri',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/AHM/2026/02',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $payload = [
            'cooperation_id' => $coop->id,
            'kepuasan' => 5,
            'sesuai_rencana' => 5,
            'kualitas' => 4,
            'keterlibatan' => 5,
            'efisiensi' => 5,
            'ringkasan' => 'Pelaksanaan program magang industri berlangsung sangat tertib dan mahasiswa memiliki etos kerja tinggi.',
            'kendala' => 'Jadwal pembekalan awal perlu disesuaikan dengan shift pabrik.',
            'rekomendasi' => 'Penambahan materi K3 sebelum mahasiswa terjun ke lini produksi.',
            'kesimpulan' => 'Sangat Baik',
            'tindak_lanjut' => 'Bersedia Melanjutkan Kerjasama',
        ];

        $response = $this->actingAs($user)->post(route('mitra.umpan_balik.store'), $payload);

        $response->assertRedirect(route('mitra.umpan_balik.index'));
        $response->assertSessionHas('success');

        // 1. Assert Evaluasi record created with Umpan_Balik_Mitra
        $this->assertDatabaseHas('evaluasis', [
            'cooperation_id' => $coop->id,
            'evaluator_id' => $user->id,
            'tipe_evaluasi' => 'Umpan_Balik_Mitra',
            'kepuasan' => 5,
            'kesimpulan' => 'Sangat Baik',
            'tindak_lanjut' => 'Bersedia Melanjutkan Kerjasama',
            'status_validasi' => 'Divalidasi',
        ]);

        // 2. Assert Notification sent to Pimpinan
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pimpinan->id,
            'sender_id' => $user->id,
            'source_id' => $coop->id,
            'type' => 'umpan_balik',
        ]);
    }

    public function test_umpan_balik_validation_errors_when_required_fields_missing()
    {
        [$user, $mitra] = $this->setupMitraUser();

        $response = $this->actingAs($user)->post(route('mitra.umpan_balik.store'), []);

        $response->assertSessionHasErrors([
            'cooperation_id',
            'kepuasan',
        ]);
    }

    public function test_mitra_can_update_existing_umpan_balik()
    {
        [$user, $mitra] = $this->setupMitraUser();

        $coop = Cooperation::create([
            'judul' => 'Kerja Sama Donasi Unit Sepeda Motor Praktik',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'mitra_id' => $mitra->id,
        ]);

        $eval = Evaluasi::create([
            'cooperation_id' => $coop->id,
            'evaluator_id' => $user->id,
            'tipe_evaluasi' => 'Umpan_Balik_Mitra',
            'score' => 4.0,
            'kepuasan' => 4,
            'sesuai_rencana' => 4,
            'kualitas' => 4,
            'keterlibatan' => 4,
            'efisiensi' => 4,
            'status_validasi' => 'Divalidasi',
        ]);

        $updatePayload = [
            'kepuasan' => 5,
            'sesuai_rencana' => 5,
            'kualitas' => 5,
            'keterlibatan' => 5,
            'efisiensi' => 5,
            'ringkasan' => 'Revisi penilaian kepuasan menjadi maksimal setelah penerimaan unit berjalan sempurna.',
            'kesimpulan' => 'Sangat Baik',
            'tindak_lanjut' => 'Bersedia Melanjutkan Kerjasama',
        ];

        $response = $this->actingAs($user)->put(route('mitra.umpan_balik.update', $eval->id), $updatePayload);

        $response->assertRedirect(route('mitra.umpan_balik.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('evaluasis', [
            'id' => $eval->id,
            'kepuasan' => 5,
            'score' => 5.0,
            'kesimpulan' => 'Sangat Baik',
        ]);
    }
}

<?php

namespace Tests\Feature\Kerjasama;

use App\Models\Cooperation;
use App\Models\Jurusan;
use App\Models\Mitra;
use App\Models\Notifikasi;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Support\CooperationExpiryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotifikasiKerjasamaKadaluarsaTest extends TestCase
{
    use DatabaseTransactions;

    protected function getOrCreateRole(string $roleName): Role
    {
        return Role::firstOrCreate(['role_name' => $roleName]);
    }

    public function test_automatic_expiry_notifies_only_relevant_units_and_partner()
    {
        // 1. Setup Roles
        $roleUnit = $this->getOrCreateRole('unit_kerja');
        $roleJurusan = $this->getOrCreateRole('jurusan');
        $roleMitra = $this->getOrCreateRole('mitra');

        // 2. Setup Jurusan & Mitra Terkait vs Tidak Terkait
        $jurusanElektro = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);
        $jurusanMesin = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Mesin']);

        $mitraA = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Terkait A']);
        $mitraB = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Lain B']);

        // 3. Setup Users
        // User Humas / Unit Kerja
        $userHumas = User::factory()->create([
            'role_id' => $roleUnit->id,
            'name' => 'Staf Humas Polimdo',
        ]);

        // User Jurusan Terkait (Elektro)
        $userJurusanElektro = User::factory()->create([
            'role_id' => $roleJurusan->id,
            'name' => 'Kajur Elektro',
        ]);
        Profile::create([
            'user_id' => $userJurusanElektro->id,
            'jurusan_id' => $jurusanElektro->id,
        ]);

        // User Jurusan Tidak Terkait (Mesin)
        $userJurusanMesin = User::factory()->create([
            'role_id' => $roleJurusan->id,
            'name' => 'Kajur Mesin',
        ]);
        Profile::create([
            'user_id' => $userJurusanMesin->id,
            'jurusan_id' => $jurusanMesin->id,
        ]);

        // User Mitra Terkait
        $userMitraA = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitraA->id,
            'name' => 'PIC PT Mitra A',
        ]);

        // User Mitra Lain (Tidak Terkait)
        $userMitraB = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitraB->id,
            'name' => 'PIC PT Mitra B',
        ]);

        // 4. Buat dokumen kerjasama yang masa berlakunya kemarin (kadaluarsa)
        $coop = Cooperation::create([
            'judul' => 'Kerjasama Riset IoT dengan PT Mitra Terkait A',
            'jenis' => 'MoA',
            'doc_number' => 'DOC/TEST/' . uniqid(),
            'start_date' => Carbon::now()->subYears(3)->format('Y-m-d'),
            'end_date' => Carbon::yesterday()->format('Y-m-d'), // Telah berakhir kemarin
            'status_berlaku' => 'Aktif',
            'status_dokumen' => 'Disahkan',
            'mitra_id' => $mitraA->id,
            'jurusan_id' => $jurusanElektro->id,
            'created_by' => $userJurusanElektro->id,
        ]);
        $coop->jurusans()->attach($jurusanElektro->id);

        // 5. Jalankan Service Pengecekan Otomatis
        $processed = CooperationExpiryService::checkAndNotifyExpiredCooperations();
        $this->assertGreaterThanOrEqual(1, $processed);

        // 6. Verifikasi status dokumen berubah menjadi 'Kadaluarsa'
        $coop->refresh();
        $this->assertEquals('Kadaluarsa', $coop->status_berlaku);

        // 7. Verifikasi notifikasi terkirim ke pihak-pihak RELEVAN
        // A. Penginput / Kajur Elektro harus menerima notifikasi
        $notifElektro = Notifikasi::where('user_id', $userJurusanElektro->id)
            ->where('source_id', $coop->id)
            ->where('type', 'kadaluarsa')
            ->first();
        $this->assertNotNull($notifElektro, 'User Jurusan Elektro harus menerima notifikasi');
        $this->assertStringContainsString('Telah Berakhir', $notifElektro->title);

        // B. Humas / Unit Kerja harus menerima notifikasi
        $notifHumas = Notifikasi::where('user_id', $userHumas->id)
            ->where('source_id', $coop->id)
            ->where('type', 'kadaluarsa')
            ->first();
        $this->assertNotNull($notifHumas, 'User Humas harus menerima notifikasi');

        // C. Mitra Privat Terkait (PT Mitra A) harus menerima notifikasi khusus mitra
        $notifMitraA = Notifikasi::where('user_id', $userMitraA->id)
            ->where('source_id', $coop->id)
            ->where('type', 'kadaluarsa')
            ->first();
        $this->assertNotNull($notifMitraA, 'User Mitra A harus menerima notifikasi');
        $this->assertStringContainsString('Politeknik Negeri Manado', $notifMitraA->message);
        $this->assertEquals(route('mitra.dokumen.show', $coop->id), $notifMitraA->url);

        // 8. Verifikasi pihak TIDAK RELEVAN TIDAK menerima notifikasi
        // A. Jurusan Mesin TIDAK boleh menerima
        $notifMesin = Notifikasi::where('user_id', $userJurusanMesin->id)
            ->where('source_id', $coop->id)
            ->where('type', 'kadaluarsa')
            ->first();
        $this->assertNull($notifMesin, 'User Jurusan Mesin tidak boleh menerima notifikasi dokumen ini');

        // B. Mitra B TIDAK boleh menerima
        $notifMitraB = Notifikasi::where('user_id', $userMitraB->id)
            ->where('source_id', $coop->id)
            ->where('type', 'kadaluarsa')
            ->first();
        $this->assertNull($notifMitraB, 'User Mitra B tidak boleh menerima notifikasi dokumen ini');

        // 9. Uji Idempotensi: Jalankan sekali lagi, jumlah notifikasi tidak boleh bertambah (tidak duplikat)
        CooperationExpiryService::checkAndNotifyExpiredCooperations();
        $totalNotifElektro = Notifikasi::where('user_id', $userJurusanElektro->id)
            ->where('source_id', $coop->id)
            ->where('type', 'kadaluarsa')
            ->count();
        $this->assertEquals(1, $totalNotifElektro, 'Notifikasi tidak boleh duplikat');

        // 10. Uji API Notifikasi untuk Mitra A
        $response = $this->actingAs($userMitraA)->getJson('/api/notifikasi');
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertGreaterThanOrEqual(1, $response->json('unread_count'));
    }

    public function test_artisan_command_check_expired_runs_successfully()
    {
        $this->artisan('notifikasi:check-expired')
            ->assertExitCode(0);
    }
}

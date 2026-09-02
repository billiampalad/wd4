<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\KegiatanKerjasama;
use App\Models\KegiatanMahasiswa;
use App\Models\Klasifikasi;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MelihatDashboardMitraTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupMitraUser($name = 'PT Schneider Electric Indonesia', $email = 'pic.schneider@schneider.com')
    {
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Manufaktur']);

        $mitra = Mitra::create([
            'nama_mitra' => $name,
            'id_klasifikasi' => $klasifikasi->id,
            'email' => $email,
        ]);

        $user = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'PIC ' . $name,
            'email' => $email,
        ]);

        return [$user, $mitra];
    }

    public function test_guest_cannot_access_mitra_dashboard()
    {
        $response = $this->get(route('mitra.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_mitra_can_access_mitra_dashboard()
    {
        [$user, $mitra] = $this->setupMitraUser();

        $response = $this->actingAs($user)->get(route('mitra.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Beranda Mitra');
        $response->assertSee('Kerja Sama Aktif');
        $response->assertSee('Menunggu Review');
        $response->assertSee('Mahasiswa Magang');
        $response->assertSee('Alumni Terserap');
        $response->assertSee('Aksi Cepat');
    }

    public function test_mitra_dashboard_displays_correct_scoped_kpi_metrics()
    {
        [$userA, $mitraA] = $this->setupMitraUser('PT Telkomsel', 'telkomsel@mitra.com');
        [$userB, $mitraB] = $this->setupMitraUser('PT Indosat', 'indosat@mitra.com');

        // Mitra A docs
        $docA1 = Cooperation::create([
            'judul' => 'MoA 5G Telkomsel',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/TSEL/01',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitraA->id,
        ]);

        $docA2 = Cooperation::create([
            'judul' => 'Draf IA Magang Telkomsel',
            'jenis' => 'IA',
            'status_dokumen' => 'Draft',
            'mitra_id' => $mitraA->id,
        ]);

        // Mitra B doc
        $docB = Cooperation::create([
            'judul' => 'MoA Fiber Indosat',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitraB->id,
        ]);

        $response = $this->actingAs($userA)->get(route('mitra.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('aktifCount', 1);
        $response->assertViewHas('pendingReviewCount', 1);

        $recentDocs = $response->viewData('recentDocuments');
        $this->assertTrue($recentDocs->contains('id', $docA1->id));
        $this->assertTrue($recentDocs->contains('id', $docA2->id));
        $this->assertFalse($recentDocs->contains('id', $docB->id));
    }

    public function test_mitra_dashboard_monitors_active_placed_students()
    {
        [$user, $mitra] = $this->setupMitraUser();
        $prodi = Prodi::first() ?? Prodi::factory()->create();

        $mhs = Mahasiswa::firstOrCreate(['nim' => '23024001'], [
            'nama' => 'Mahasiswa Magang Mitra',
            'prodi_id' => $prodi->id,
            'jurusan_id' => $prodi->jurusan_id,
            'angkatan' => 2023,
        ]);

        $coop = Cooperation::create([
            'judul' => 'Program Magang Industri',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'mitra_id' => $mitra->id,
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Magang Industri PLC',
            'status' => 'Berjalan',
        ]);

        KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => now()->startOfYear(),
            'periode_selesai' => now()->addMonths(6),
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($user)->get(route('mitra.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalMahasiswaAktif');
        $this->assertGreaterThanOrEqual(1, $response->viewData('totalMahasiswaAktif'));
    }

    public function test_mitra_dashboard_quick_actions_links()
    {
        [$user, $mitra] = $this->setupMitraUser();

        $response = $this->actingAs($user)->get(route('mitra.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('mitra.pengajuan.create'));
        $response->assertSee(route('mitra.dokumen.index'));
        $response->assertSee(route('mitra.penilaian.index'));
        $response->assertSee(route('mitra.umpan_balik.index'));
        $response->assertSee(route('mitra.perpanjangan.create'));
    }
}

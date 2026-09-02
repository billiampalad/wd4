<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\DetailKegiatan;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MelihatDashboardEksekutifTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupPimpinanUser()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);

        return User::factory()->create([
            'role_id' => $rolePimpinan->id,
            'name' => 'Direktur Utama Polimdo',
            'email' => 'direktur.exec@polimdo.ac.id',
        ]);
    }

    protected function setupNonPimpinanUser()
    {
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);

        return User::factory()->create([
            'role_id' => $roleMitra->id,
            'name' => 'User Non Pimpinan',
            'email' => 'nonpimpinan@mitra.com',
        ]);
    }

    public function test_guest_cannot_access_executive_dashboard()
    {
        $response = $this->get(route('pimpinan.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_pimpinan_redirected_away_from_pimpinan_dashboard()
    {
        $nonPimpinan = $this->setupNonPimpinanUser();

        $response = $this->actingAs($nonPimpinan)->get(route('pimpinan.dashboard'));
        // Non-pimpinan will be redirected based on role middleware
        $response->assertStatus(302);
    }

    public function test_pimpinan_can_access_executive_dashboard()
    {
        $pimpinan = $this->setupPimpinanUser();

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Sistem Informasi Kerjasama (Executive)');
        $response->assertSee('Total Kerjasama Aktif');
        $response->assertSee('Total Mitra Terdaftar');
        $response->assertSee('Tren Kerjasama Tahunan');
        $response->assertSee('Distribusi Dokumen');
        $response->assertSee('Sebaran Geografis');
    }

    public function test_executive_dashboard_calculates_correct_kpis_and_metrics()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'BUMN']);

        $mitraNasional = Mitra::create([
            'nama_mitra' => 'PT Telkom Indonesia (Persero) Tbk',
            'id_klasifikasi' => $klasifikasi->id,
            'is_luar_negeri' => 0,
        ]);

        $mitraInternasional = Mitra::create([
            'nama_mitra' => 'Tokyo Institute of Technology',
            'id_klasifikasi' => $klasifikasi->id,
            'is_luar_negeri' => 1,
        ]);

        $coop = Cooperation::create([
            'judul' => 'Kerja Sama Riset Fiber Optic',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitraNasional->id,
            'start_date' => now()->startOfYear(),
            'end_date' => now()->addYears(2),
        ]);

        DetailKegiatan::create([
            'cooperation_id' => $coop->id,
            'income' => 150000000,
            'volume_luaran' => 10,
        ]);

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalKerjasamaAktif');
        $response->assertViewHas('totalMitra');
        $response->assertViewHas('totalNilaiKontrak');
        $response->assertViewHas('nasional');
        $response->assertViewHas('internasional');

        $totalAktif = $response->viewData('totalKerjasamaAktif');
        $this->assertGreaterThanOrEqual(1, $totalAktif);

        $nilaiKontrak = $response->viewData('totalNilaiKontrak');
        $this->assertGreaterThanOrEqual(150000000, $nilaiKontrak);
    }

    public function test_executive_dashboard_detects_expiring_soon_documents()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Schneider Electric'], ['id_klasifikasi' => $klasifikasi->id]);

        $coopExpiring = Cooperation::create([
            'judul' => 'Kerja Sama Smart PLC Schneider',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'start_date' => now()->subYear(),
            'end_date' => now()->addDays(25), // Expiring in 25 days (< 60 days)
        ]);

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('expiringSoon');
        
        $expiringList = $response->viewData('expiringSoon');
        $this->assertTrue($expiringList->contains('id', $coopExpiring->id));
    }

    public function test_executive_dashboard_monitors_pending_evaluation_documents()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Epson Indonesia'], ['id_klasifikasi' => $klasifikasi->id]);

        $coopPending = Cooperation::create([
            'judul' => 'MoA Laboratorium Robotika Epson',
            'jenis' => 'MoA',
            'status_dokumen' => 'Menunggu Evaluasi',
            'mitra_id' => $mitra->id,
        ]);

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('dokumenMenunggu');

        $pendingList = $response->viewData('dokumenMenunggu');
        $this->assertTrue($pendingList->contains('id', $coopPending->id));
    }
}

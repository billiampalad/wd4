<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\DetailKegiatan;
use App\Models\Jurusan;
use App\Models\KegiatanMahasiswa;
use App\Models\Klasifikasi;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\Prodi;
use App\Models\Profile;
use App\Models\Pusat;
use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\Upa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MelihatDashboardPerUnitTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupUnitUser()
    {
        $role = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Bagian Humas dan Protokol']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Staf Unit Humas',
            'email' => 'humas.dash@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'unit_kerja_id' => $unitKerja->id,
            'nip' => '198501012010121020',
        ]);

        return [$user, $unitKerja];
    }

    protected function setupJurusanUser()
    {
        $role = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Mesin']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Ketua Jurusan Mesin',
            'email' => 'kajur.mesin@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'jurusan_id' => $jurusan->id,
            'nip' => '197501012000031001',
        ]);

        return [$user, $jurusan];
    }

    protected function setupProdiUser()
    {
        $role = Role::firstOrCreate(['name' => 'prodi'], ['guard_name' => 'web']);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);
        $prodi = Prodi::firstOrCreate(['nama_prodi' => 'D4 Teknik Informatika'], ['jurusan_id' => $jurusan->id]);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Kaprodi TI',
            'email' => 'kaprodi.ti@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'prodi_id' => $prodi->id,
            'jurusan_id' => $jurusan->id,
            'nip' => '198801012015041002',
        ]);

        return [$user, $prodi];
    }

    protected function setupUpaUser()
    {
        $role = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Perpustakaan']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Kepala UPA Perpus',
            'email' => 'kepala.perpus@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'upa_id' => $upa->id,
            'nip' => '198201012008011003',
        ]);

        return [$user, $upa];
    }

    protected function setupPusatUser()
    {
        $role = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Penelitian dan Pengabdian Masyarakat']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Kepala P3M',
            'email' => 'kepala.p3m@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'pusat_id' => $pusat->id,
            'nip' => '198001012006041001',
        ]);

        return [$user, $pusat];
    }

    public function test_guest_cannot_access_unit_dashboards()
    {
        $this->get(route('unit.dashboard'))->assertRedirect(route('login'));
        $this->get(route('jurusan.dashboard'))->assertRedirect(route('login'));
        $this->get(route('prodi.dashboard'))->assertRedirect(route('login'));
        $this->get(route('upa.dashboard'))->assertRedirect(route('login'));
        $this->get(route('pusat.dashboard'))->assertRedirect(route('login'));
    }

    public function test_unit_kerja_humas_can_access_unit_dashboard_with_metrics()
    {
        [$user, $unitKerja] = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Astra Otoparts'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'MoA Pelatihan Humas Industri',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'start_date' => now()->startOfYear(),
            'end_date' => now()->addYears(1),
        ]);

        $response = $this->actingAs($user)->get(route('unit.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Bagian Humas dan Protokol');
        $response->assertViewHas('totalKerjasama');
        $response->assertViewHas('kerjasamaTable');
    }

    public function test_jurusan_can_access_jurusan_dashboard_scoped_to_jurusan()
    {
        [$user, $jurusan] = $this->setupJurusanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT United Tractors'], ['id_klasifikasi' => $klasifikasi->id]);

        $coopMesin = Cooperation::create([
            'judul' => 'MoA Maintenance Alat Berat Mesin',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'jurusan_id' => $jurusan->id,
            'mitra_id' => $mitra->id,
        ]);

        $response = $this->actingAs($user)->get(route('jurusan.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalKerjasama');
        $this->assertGreaterThanOrEqual(1, $response->viewData('totalKerjasama'));
    }

    public function test_prodi_can_access_prodi_dashboard_with_student_metrics()
    {
        [$user, $prodi] = $this->setupProdiUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Teknologi']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Google Indonesia'], ['id_klasifikasi' => $klasifikasi->id]);

        $mhs = Mahasiswa::firstOrCreate(['nim' => '22024099'], [
            'nama' => 'Mahasiswa TI Polimdo',
            'prodi_id' => $prodi->id,
            'jurusan_id' => $prodi->jurusan_id,
            'angkatan' => 2024,
        ]);

        $coop = Cooperation::create([
            'judul' => 'Program Magang Bangkit',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'mitra_id' => $mitra->id,
        ]);

        $kegiatan = \App\Models\KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Bangkit Academy 2026',
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

        $response = $this->actingAs($user)->get(route('prodi.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalMahasiswaAktif');
        $this->assertGreaterThanOrEqual(1, $response->viewData('totalMahasiswaAktif'));
    }

    public function test_upa_can_access_upa_dashboard_scoped_to_upa()
    {
        [$user, $upa] = $this->setupUpaUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Penerbit']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Gramedia Asri Media'], ['id_klasifikasi' => $klasifikasi->id]);

        Cooperation::create([
            'judul' => 'Kerja Sama E-Library Perpustakaan',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'upa_id' => $upa->id,
            'mitra_id' => $mitra->id,
        ]);

        $response = $this->actingAs($user)->get(route('upa.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('UPA Perpustakaan');
        $response->assertViewHas('totalKerjasama');
    }

    public function test_pusat_can_access_pusat_dashboard_scoped_to_pusat()
    {
        [$user, $pusat] = $this->setupPusatUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Pemerintah']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'BRIN RI'], ['id_klasifikasi' => $klasifikasi->id]);

        Cooperation::create([
            'judul' => 'MoU Riset Terapan BRIN-P3M',
            'jenis' => 'MoU',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'pusat_id' => $pusat->id,
            'mitra_id' => $mitra->id,
        ]);

        $response = $this->actingAs($user)->get(route('pusat.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Penelitian dan Pengabdian Masyarakat');
        $response->assertViewHas('totalKerjasama');
    }
}

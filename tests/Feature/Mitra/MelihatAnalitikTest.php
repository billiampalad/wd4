<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\Jurusan;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use App\Models\Profile;
use App\Models\Pusat;
use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\Upa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MelihatAnalitikTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupJurusanUser()
    {
        $role = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Kajur Elektro Analitik',
            'email' => 'kajur.elektro.analitik@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'jurusan_id' => $jurusan->id,
            'nip' => '197701012002031001',
        ]);

        return [$user, $jurusan];
    }

    protected function setupUnitUser()
    {
        $role = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $unit = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Bagian Humas dan Protokol']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Staf Humas Analitik',
            'email' => 'humas.analitik@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'unit_kerja_id' => $unit->id,
            'nip' => '198601012011021002',
        ]);

        return [$user, $unit];
    }

    protected function setupUpaUser()
    {
        $role = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Perpustakaan']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Kepala Perpus Analitik',
            'email' => 'perpus.analitik@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'upa_id' => $upa->id,
            'nip' => '198401012009031002',
        ]);

        return [$user, $upa];
    }

    protected function setupPusatUser()
    {
        $role = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Penelitian dan Pengabdian Masyarakat']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Kepala P3M Analitik',
            'email' => 'p3m.analitik@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'pusat_id' => $pusat->id,
            'nip' => '198001012006041002',
        ]);

        return [$user, $pusat];
    }

    protected function setupPimpinanUser()
    {
        $role = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Pimpinan Analitik',
            'email' => 'pimpinan.analitik@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'nip' => '197001011995011002',
        ]);

        return $user;
    }

    public function test_guest_cannot_access_analitik_pages()
    {
        $this->get(route('jurusan.analitik.status-kerjasama'))->assertRedirect(route('login'));
        $this->get(route('jurusan.analitik.klasifikasi-mitra'))->assertRedirect(route('login'));
        $this->get(route('jurusan.analitik.geo-mitra'))->assertRedirect(route('login'));
        $this->get(route('unit.analitik.status-kerjasama'))->assertRedirect(route('login'));
        $this->get(route('pimpinan.monitoring'))->assertRedirect(route('login'));
    }

    public function test_jurusan_can_access_analitik_status_kerjasama()
    {
        [$user, $jurusan] = $this->setupJurusanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Schneider Electric'], ['id_klasifikasi' => $klasifikasi->id]);

        Cooperation::create([
            'judul' => 'MoA Lab Otomasi Industri',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'jurusan_id' => $jurusan->id,
            'mitra_id' => $mitra->id,
            'start_date' => now()->startOfYear(),
            'end_date' => now()->addYears(2),
        ]);

        $response = $this->actingAs($user)->get(route('jurusan.analitik.status-kerjasama'));

        $response->assertStatus(200);
        $response->assertViewHas('statusKerjasamaData');
        $response->assertViewHas('growthData');
        $response->assertViewHas('calendarData');
        $response->assertViewHas('dueDateData');
        $response->assertViewHas('mouVsMoaIaData');
    }

    public function test_jurusan_can_access_analitik_klasifikasi_mitra()
    {
        [$user, $jurusan] = $this->setupJurusanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'BUMN Telekomunikasi']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Telkom Indonesia'], ['id_klasifikasi' => $klasifikasi->id]);

        Cooperation::create([
            'judul' => 'IA Jaringan Fiber Optik',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'jurusan_id' => $jurusan->id,
            'mitra_id' => $mitra->id,
        ]);

        $response = $this->actingAs($user)->get(route('jurusan.analitik.klasifikasi-mitra'));

        $response->assertStatus(200);
        $response->assertViewHas('classifications');
        $response->assertViewHas('chartDataPayload');
        $response->assertViewHas('topMitras');
    }

    public function test_jurusan_can_access_analitik_geo_mitra()
    {
        [$user, $jurusan] = $this->setupJurusanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Multinasional']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Microsoft Asia Pacific'], [
            'id_klasifikasi' => $klasifikasi->id,
            'tingkat' => 'Internasional',
            'negara' => 'Singapura',
        ]);

        Cooperation::create([
            'judul' => 'MoU Cloud Computing Academy',
            'jenis' => 'MoU',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'jurusan_id' => $jurusan->id,
            'mitra_id' => $mitra->id,
        ]);

        $response = $this->actingAs($user)->get(route('jurusan.analitik.geo-mitra'));

        $response->assertStatus(200);
        $response->assertViewHas('totalMitras');
        $response->assertViewHas('nasionalCount');
        $response->assertViewHas('internasionalCount');
    }

    public function test_unit_kerja_humas_can_access_all_analitik_pages()
    {
        [$user, $unit] = $this->setupUnitUser();

        $this->actingAs($user)->get(route('unit.analitik.status-kerjasama'))->assertStatus(200);
        $this->actingAs($user)->get(route('unit.analitik.klasifikasi-mitra'))->assertStatus(200);
        $this->actingAs($user)->get(route('unit.analitik.geo-mitra'))->assertStatus(200);
    }

    public function test_upa_and_pusat_can_access_analitik_pages()
    {
        [$upaUser, $upa] = $this->setupUpaUser();
        [$pusatUser, $pusat] = $this->setupPusatUser();

        // UPA Analytics
        $this->actingAs($upaUser)->get(route('upa.analitik.status-kerjasama'))->assertStatus(200);
        $this->actingAs($upaUser)->get(route('upa.analitik.klasifikasi-mitra'))->assertStatus(200);
        $this->actingAs($upaUser)->get(route('upa.analitik.geo-mitra'))->assertStatus(200);

        // Pusat Analytics
        $this->actingAs($pusatUser)->get(route('pusat.analitik.status-kerjasama'))->assertStatus(200);
        $this->actingAs($pusatUser)->get(route('pusat.analitik.klasifikasi-mitra'))->assertStatus(200);
        $this->actingAs($pusatUser)->get(route('pusat.analitik.geo-mitra'))->assertStatus(200);
    }

    public function test_pimpinan_can_access_executive_analitik_monitoring()
    {
        $pimpinan = $this->setupPimpinanUser();

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.monitoring'));

        $response->assertStatus(200);
        $response->assertViewHas('funnelData');
        $response->assertViewHas('sasaranData');
        $response->assertViewHas('financialTrend');
        $response->assertViewHas('unitRanking');
    }
}

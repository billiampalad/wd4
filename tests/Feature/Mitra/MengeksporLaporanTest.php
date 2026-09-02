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

class MengeksporLaporanTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupPimpinanUser()
    {
        $role = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Direktur Polimdo',
            'email' => 'direktur.laporan@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'nip' => '197001011995011001',
        ]);

        return $user;
    }

    protected function setupUnitUser()
    {
        $role = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        $unit = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Bagian Kerja Sama & Humas']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Staf Humas Laporan',
            'email' => 'humas.laporan@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'unit_kerja_id' => $unit->id,
            'nip' => '198501012010121020',
        ]);

        return [$user, $unit];
    }

    protected function setupJurusanUser()
    {
        $role = Role::firstOrCreate(['name' => 'jurusan'], ['guard_name' => 'web']);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Kajur Elektro Laporan',
            'email' => 'kajur.elektro.laporan@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'jurusan_id' => $jurusan->id,
            'nip' => '197601012001031002',
        ]);

        return [$user, $jurusan];
    }

    protected function setupUpaUser()
    {
        $role = Role::firstOrCreate(['name' => 'upa'], ['guard_name' => 'web']);
        $upa = Upa::firstOrCreate(['nama_upa' => 'UPA Komputer']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Kepala UPA Komputer',
            'email' => 'kepala.upa.komputer@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'upa_id' => $upa->id,
            'nip' => '198301012009021004',
        ]);

        return [$user, $upa];
    }

    protected function setupPusatUser()
    {
        $role = Role::firstOrCreate(['name' => 'pusat'], ['guard_name' => 'web']);
        $pusat = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Penelitian dan Pengabdian Masyarakat']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Kepala P3M Laporan',
            'email' => 'kepala.p3m.laporan@polimdo.ac.id',
        ]);

        Profile::firstOrCreate(['user_id' => $user->id], [
            'pusat_id' => $pusat->id,
            'nip' => '197901012005011003',
        ]);

        return [$user, $pusat];
    }

    public function test_guest_cannot_access_laporan_endpoints()
    {
        $this->get(route('pimpinan.laporan'))->assertRedirect(route('login'));
        $this->get(route('jurusan.laporan'))->assertRedirect(route('login'));
        $this->get(route('unit.laporan'))->assertRedirect(route('login'));
        $this->get(route('upa.laporan'))->assertRedirect(route('login'));
        $this->get(route('pusat.laporan'))->assertRedirect(route('login'));
    }

    public function test_pimpinan_can_access_laporan_page_with_filters()
    {
        $pimpinan = $this->setupPimpinanUser();

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.laporan'));

        $response->assertStatus(200);
        $response->assertViewHas('jurusans');
        $response->assertViewHas('upas');
        $response->assertViewHas('pusats');
    }

    public function test_pimpinan_can_preview_laporan_data_via_json()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri IT']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Solusi Digital'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'MoU Kerja Sama Smart Campus',
            'jenis' => 'MoU',
            'doc_number' => 'MOU/SC/2026/01',
            'status_berlaku' => 'Aktif',
            'status_dokumen' => 'Disahkan',
            'mitra_id' => $mitra->id,
            'start_date' => now()->startOfYear(),
            'end_date' => now()->addYears(3),
        ]);

        $response = $this->actingAs($pimpinan)->get(route('pimpinan.laporan.preview', [
            'jenis_dokumentasi' => 'mou',
            'status' => 'Aktif',
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $coop->id,
            'jenis' => 'MoU',
            'status' => 'Aktif',
        ]);
    }

    public function test_pimpinan_can_export_laporan_excel_and_pdf()
    {
        $pimpinan = $this->setupPimpinanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Pemerintahan']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'Dinas Kominfo Sulut'], ['id_klasifikasi' => $klasifikasi->id]);

        Cooperation::create([
            'judul' => 'MoA Pengembangan Aplikasi E-Gov',
            'jenis' => 'MoA',
            'status_berlaku' => 'Aktif',
            'status_dokumen' => 'Disahkan',
            'mitra_id' => $mitra->id,
        ]);

        // Export Excel (CSV Stream)
        $excelResponse = $this->actingAs($pimpinan)->get(route('pimpinan.laporan.excel'));
        $excelResponse->assertStatus(200);
        $excelResponse->assertHeader('Content-Disposition');

        // Export PDF
        $pdfResponse = $this->actingAs($pimpinan)->get(route('pimpinan.laporan.pdf'));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_jurusan_can_access_preview_and_export_laporan()
    {
        [$jurusanUser, $jurusan] = $this->setupJurusanUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'BUMN']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT PLN (Persero)'], ['id_klasifikasi' => $klasifikasi->id]);

        Cooperation::create([
            'judul' => 'IA Pelatihan K3 Kelistrikan',
            'jenis' => 'IA',
            'status_berlaku' => 'Aktif',
            'status_dokumen' => 'Disahkan',
            'jurusan_id' => $jurusan->id,
            'mitra_id' => $mitra->id,
        ]);

        // 1. Index
        $this->actingAs($jurusanUser)->get(route('jurusan.laporan'))->assertStatus(200);

        // 2. Preview
        $previewResponse = $this->actingAs($jurusanUser)->get(route('jurusan.laporan.preview'));
        $previewResponse->assertStatus(200);

        // 3. Excel
        $excelResponse = $this->actingAs($jurusanUser)->get(route('jurusan.laporan.excel'));
        $excelResponse->assertStatus(200);

        // 4. PDF
        $pdfResponse = $this->actingAs($jurusanUser)->get(route('jurusan.laporan.pdf'));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_unit_kerja_humas_can_access_preview_and_export_laporan()
    {
        [$unitUser, $unit] = $this->setupUnitUser();

        $this->actingAs($unitUser)->get(route('unit.laporan'))->assertStatus(200);
        $this->actingAs($unitUser)->get(route('unit.laporan.preview'))->assertStatus(200);
        $this->actingAs($unitUser)->get(route('unit.laporan.excel'))->assertStatus(200);
        $this->actingAs($unitUser)->get(route('unit.laporan.pdf'))->assertStatus(200);
    }

    public function test_upa_and_pusat_can_access_preview_and_export_laporan()
    {
        [$upaUser, $upa] = $this->setupUpaUser();
        [$pusatUser, $pusat] = $this->setupPusatUser();

        $this->actingAs($upaUser)->get(route('upa.laporan.preview'))->assertStatus(200);
        $this->actingAs($upaUser)->get(route('upa.laporan.excel'))->assertStatus(200);

        $this->actingAs($pusatUser)->get(route('pusat.laporan.preview'))->assertStatus(200);
        $this->actingAs($pusatUser)->get(route('pusat.laporan.excel'))->assertStatus(200);
    }
}

<?php

namespace Tests\Feature\Humas;

use App\Models\Cooperation;
use App\Models\Jurusan;
use App\Models\Mitra;
use App\Models\Profile;
use App\Models\Pusat;
use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\Upa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FilterDataKerjasamaHumasTest extends TestCase
{
    use DatabaseTransactions;

    protected User $userHumas;
    protected Mitra $mitraA;
    protected Mitra $mitraB;
    protected Jurusan $jurusanElektro;
    protected Jurusan $jurusanMesin;
    protected Upa $upaPerpustakaan;
    protected Pusat $pusatKarir;

    protected Cooperation $coopMouInstansi;
    protected Cooperation $coopMoaJurusan;
    protected Cooperation $coopIaUpa;
    protected Cooperation $coopMoaPusat;
    protected Cooperation $coopExpired;
    protected Cooperation $coopPerpanjangan;
    protected Cooperation $coopProses;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup User Humas (Unit Kerja)
        $roleUnit = Role::firstOrCreate(['role_name' => 'unit_kerja']);
        $this->userHumas = User::factory()->create([
            'name' => 'Petugas Humas Unit',
            'role_id' => $roleUnit->id,
            'is_active' => true,
        ]);
        $unitKerja = UnitKerja::firstOrCreate(['nama_unit_pelaksana' => 'Humas Politeknik']);
        Profile::create([
            'user_id' => $this->userHumas->id,
            'unit_kerja_id' => $unitKerja->id,
            'jabatan' => 'Staff Kerjasama',
        ]);

        // 2. Setup Master Data
        $this->mitraA = Mitra::firstOrCreate(['nama_mitra' => 'PT Astra International Filter Test', 'status_akses' => 'Aktif']);
        $this->mitraB = Mitra::firstOrCreate(['nama_mitra' => 'PT Telkom Indonesia Filter Test', 'status_akses' => 'Aktif']);

        $this->jurusanElektro = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro Filter Test']);
        $this->jurusanMesin = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Mesin Filter Test']);
        $this->upaPerpustakaan = Upa::firstOrCreate(['nama_upa' => 'UPA Perpustakaan Filter Test']);
        $this->pusatKarir = Pusat::firstOrCreate(['nama_pusat' => 'Pusat Karir dan Alumni Filter Test']);

        // 3. Seed variasi Data Kerjasama untuk Pengujian Filter
        // 3a. MoU Tingkat Institusi (Aktif, 2026)
        $this->coopMouInstansi = Cooperation::create([
            'judul' => 'MoU Institusi Kerjasama Industri 2026',
            'doc_number' => 'DOC/MOU/INSTANSI/001',
            'jenis' => 'MoU',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $this->mitraA->id,
            'tingkat' => 'Institusi',
            'start_date' => Carbon::parse('2026-01-10'),
            'end_date' => Carbon::parse('2029-01-10'),
            'created_by' => $this->userHumas->id,
        ]);

        // 3b. MoA Jurusan Elektro (Aktif, 2025)
        $this->coopMoaJurusan = Cooperation::create([
            'judul' => 'MoA Jurusan Elektro Pelatihan Otomasi 2025',
            'doc_number' => 'DOC/MOA/ELEKTRO/002',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $this->mitraA->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $this->jurusanElektro->id,
            'start_date' => Carbon::parse('2025-05-01'),
            'end_date' => Carbon::parse('2027-05-01'),
            'created_by' => $this->userHumas->id,
        ]);
        $this->coopMoaJurusan->jurusans()->sync([$this->jurusanElektro->id]);

        // 3c. IA UPA Perpustakaan (Aktif, 2026)
        $this->coopIaUpa = Cooperation::create([
            'judul' => 'IA Implementasi Digital Library UPA 2026',
            'doc_number' => 'DOC/IA/UPA/003',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $this->mitraB->id,
            'tingkat' => 'Pusat/UPA',
            'upa_id' => $this->upaPerpustakaan->id,
            'start_date' => Carbon::parse('2026-03-15'),
            'end_date' => Carbon::parse('2026-12-31'),
            'created_by' => $this->userHumas->id,
        ]);
        $this->coopIaUpa->upas()->sync([$this->upaPerpustakaan->id]);

        // 3d. MoA Pusat Karir (Aktif, 2026)
        $this->coopMoaPusat = Cooperation::create([
            'judul' => 'MoA Program Internship Pusat Karir 2026',
            'doc_number' => 'DOC/MOA/PUSAT/004',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $this->mitraB->id,
            'tingkat' => 'Pusat/UPA',
            'pusat_id' => $this->pusatKarir->id,
            'start_date' => Carbon::parse('2026-06-01'),
            'end_date' => Carbon::parse('2028-06-01'),
            'created_by' => $this->userHumas->id,
        ]);
        $this->coopMoaPusat->pusats()->sync([$this->pusatKarir->id]);

        // 3e. Dokumen Kadaluarsa (2020-2023)
        $this->coopExpired = Cooperation::create([
            'judul' => 'MoU Kerjasama Lama Kadaluarsa 2020',
            'doc_number' => 'DOC/MOU/EXPIRED/005',
            'jenis' => 'MoU',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Kadaluarsa',
            'mitra_id' => $this->mitraA->id,
            'tingkat' => 'Institusi',
            'start_date' => Carbon::parse('2020-01-01'),
            'end_date' => Carbon::parse('2023-01-01'),
            'created_by' => $this->userHumas->id,
        ]);

        // 3f. Dokumen Dalam Perpanjangan
        $this->coopPerpanjangan = Cooperation::create([
            'judul' => 'MoA Kerjasama Dalam Masa Perpanjangan 2026',
            'doc_number' => 'DOC/MOA/EXT/006',
            'jenis' => 'MoA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Dalam Perpanjangan',
            'mitra_id' => $this->mitraA->id,
            'tingkat' => 'Jurusan',
            'jurusan_id' => $this->jurusanMesin->id,
            'start_date' => Carbon::parse('2023-02-01'),
            'end_date' => Carbon::parse('2026-02-01'),
            'created_by' => $this->userHumas->id,
        ]);
        $this->coopPerpanjangan->jurusans()->sync([$this->jurusanMesin->id]);

        // 3g. Dokumen Sedang Proses / Draft
        $this->coopProses = Cooperation::create([
            'judul' => 'Draft Kerjasama Pengabdian Masyarakat 2026',
            'doc_number' => 'DOC/DRAFT/PROSES/007',
            'jenis' => 'IA',
            'status_dokumen' => 'Draft',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $this->mitraB->id,
            'tingkat' => 'Institusi',
            'start_date' => Carbon::parse('2026-04-01'),
            'end_date' => Carbon::parse('2027-04-01'),
            'created_by' => $this->userHumas->id,
        ]);
    }

    /**
     * Uji Filter Rentang Tanggal: tanggal_awal dan tanggal_akhir
     */
    public function test_filter_rentang_tanggal_awal_dan_akhir()
    {
        // 1. Filter tanggal_awal >= 2026-01-01 (Harus mengeluarkan data 2026+, tidak menyertakan data 2025 dan 2020)
        $response = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'tanggal_awal' => '2026-01-01',
        ]));
        $response->assertStatus(200);
        $titles = collect($response->json())->pluck('title')->all();

        $this->assertContains($this->coopMouInstansi->judul, $titles);
        $this->assertContains($this->coopIaUpa->judul, $titles);
        $this->assertContains($this->coopMoaPusat->judul, $titles);
        $this->assertNotContains($this->coopMoaJurusan->judul, $titles); // 2025-05-01
        $this->assertNotContains($this->coopExpired->judul, $titles);    // 2020-01-01

        // 2. Filter rentang tertutup: 2025-01-01 s/d 2025-12-31 (Hanya data 2025)
        $response2025 = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'tanggal_awal' => '2025-01-01',
            'tanggal_akhir' => '2025-12-31',
        ]));
        $response2025->assertStatus(200);
        $titles2025 = collect($response2025->json())->pluck('title')->all();

        $this->assertContains($this->coopMoaJurusan->judul, $titles2025);
        $this->assertNotContains($this->coopMouInstansi->judul, $titles2025);
        $this->assertNotContains($this->coopExpired->judul, $titles2025);
    }

    /**
     * Uji Filter Jenis Dokumentasi: MoU, MoA, IA, dan 'all'
     */
    public function test_filter_jenis_dokumentasi()
    {
        // 1. Filter jenis = 'MoU'
        $responseMou = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'jenis_dokumentasi' => 'MoU',
        ]));
        $responseMou->assertStatus(200);
        $mouData = collect($responseMou->json());
        $this->assertTrue($mouData->every(fn($item) => str_contains($item['jenis'], 'MoU')));
        $this->assertContains($this->coopMouInstansi->judul, $mouData->pluck('title')->all());
        $this->assertNotContains($this->coopMoaJurusan->judul, $mouData->pluck('title')->all());

        // 2. Filter jenis = 'MoA'
        $responseMoa = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'jenis_dokumentasi' => 'MoA',
        ]));
        $responseMoa->assertStatus(200);
        $moaData = collect($responseMoa->json());
        $this->assertTrue($moaData->every(fn($item) => str_contains($item['jenis'], 'MoA')));
        $this->assertContains($this->coopMoaJurusan->judul, $moaData->pluck('title')->all());
        $this->assertContains($this->coopMoaPusat->judul, $moaData->pluck('title')->all());
        $this->assertNotContains($this->coopIaUpa->judul, $moaData->pluck('title')->all());

        // 3. Filter jenis = 'IA'
        $responseIa = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'jenis_dokumentasi' => 'IA',
        ]));
        $responseIa->assertStatus(200);
        $iaData = collect($responseIa->json());
        $this->assertTrue($iaData->every(fn($item) => str_contains($item['jenis'], 'IA') && !str_contains($item['jenis'], 'MoA')));
        $this->assertContains($this->coopIaUpa->judul, $iaData->pluck('title')->all());
        $this->assertNotContains($this->coopMoaPusat->judul, $iaData->pluck('title')->all());
    }

    /**
     * Uji Filter Tipe Pelaksana: instansi, jurusan, upa, pusat
     */
    public function test_filter_tipe_pelaksana()
    {
        // 1. Tipe = 'instansi' (MoU tanpa spesifik Jurusan/UPA/Pusat)
        $responseInstansi = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'tipe_pelaksana' => 'instansi',
        ]));
        $responseInstansi->assertStatus(200);
        $instansiTitles = collect($responseInstansi->json())->pluck('title')->all();
        $this->assertContains($this->coopMouInstansi->judul, $instansiTitles);
        $this->assertNotContains($this->coopMoaJurusan->judul, $instansiTitles);
        $this->assertNotContains($this->coopIaUpa->judul, $instansiTitles);

        // 2. Tipe = 'jurusan' (Terhubung ke Jurusan)
        $responseJurusan = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'tipe_pelaksana' => 'jurusan',
        ]));
        $responseJurusan->assertStatus(200);
        $jurusanTitles = collect($responseJurusan->json())->pluck('title')->all();
        $this->assertContains($this->coopMoaJurusan->judul, $jurusanTitles);
        $this->assertContains($this->coopPerpanjangan->judul, $jurusanTitles);
        $this->assertNotContains($this->coopMouInstansi->judul, $jurusanTitles);

        // 3. Tipe = 'upa' (Terhubung ke UPA)
        $responseUpa = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'tipe_pelaksana' => 'upa',
        ]));
        $responseUpa->assertStatus(200);
        $upaTitles = collect($responseUpa->json())->pluck('title')->all();
        $this->assertContains($this->coopIaUpa->judul, $upaTitles);
        $this->assertNotContains($this->coopMoaPusat->judul, $upaTitles);

        // 4. Tipe = 'pusat' (Terhubung ke Pusat)
        $responsePusat = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'tipe_pelaksana' => 'pusat',
        ]));
        $responsePusat->assertStatus(200);
        $pusatTitles = collect($responsePusat->json())->pluck('title')->all();
        $this->assertContains($this->coopMoaPusat->judul, $pusatTitles);
        $this->assertNotContains($this->coopIaUpa->judul, $pusatTitles);
    }

    /**
     * Uji Filter Unit Spesifik: jurusan_id, upa_id, pusat_id
     */
    public function test_filter_spesifik_unit_id()
    {
        // 1. Filter jurusan_id spesifik (Teknik Elektro)
        $responseElektro = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'jurusan_id' => $this->jurusanElektro->id,
        ]));
        $responseElektro->assertStatus(200);
        $elektroTitles = collect($responseElektro->json())->pluck('title')->all();
        $this->assertContains($this->coopMoaJurusan->judul, $elektroTitles);
        $this->assertNotContains($this->coopPerpanjangan->judul, $elektroTitles); // Mesin

        // 2. Filter upa_id spesifik (UPA Perpustakaan)
        $responseUpa = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'upa_id' => $this->upaPerpustakaan->id,
        ]));
        $responseUpa->assertStatus(200);
        $upaTitles = collect($responseUpa->json())->pluck('title')->all();
        $this->assertContains($this->coopIaUpa->judul, $upaTitles);
        $this->assertNotContains($this->coopMoaPusat->judul, $upaTitles);

        // 3. Filter pusat_id spesifik (Pusat Karir)
        $responsePusat = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'pusat_id' => $this->pusatKarir->id,
        ]));
        $responsePusat->assertStatus(200);
        $pusatTitles = collect($responsePusat->json())->pluck('title')->all();
        $this->assertContains($this->coopMoaPusat->judul, $pusatTitles);
        $this->assertNotContains($this->coopIaUpa->judul, $pusatTitles);
    }

    /**
     * Uji Filter Status Kerjasama: aktif, kadarluarsa, dalam perpanjangan, proses
     */
    public function test_filter_status_kerjasama()
    {
        // 1. Status 'aktif'
        $responseAktif = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'status' => 'aktif',
        ]));
        $responseAktif->assertStatus(200);
        $aktifTitles = collect($responseAktif->json())->pluck('title')->all();
        $this->assertContains($this->coopMouInstansi->judul, $aktifTitles);
        $this->assertNotContains($this->coopExpired->judul, $aktifTitles);

        // 2. Status 'kadarluarsa' / 'kadaluarsa'
        $responseExpired = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'status' => 'kadarluarsa',
        ]));
        $responseExpired->assertStatus(200);
        $expiredTitles = collect($responseExpired->json())->pluck('title')->all();
        $this->assertContains($this->coopExpired->judul, $expiredTitles);
        $this->assertNotContains($this->coopMouInstansi->judul, $expiredTitles);

        // 3. Status 'dalam perpanjangan'
        $responseExt = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'status' => 'dalam perpanjangan',
        ]));
        $responseExt->assertStatus(200);
        $extTitles = collect($responseExt->json())->pluck('title')->all();
        $this->assertContains($this->coopPerpanjangan->judul, $extTitles);
        $this->assertNotContains($this->coopMouInstansi->judul, $extTitles);

        // 4. Status 'proses' (Dokumen Draft / Menunggu Evaluasi)
        $responseProses = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'status' => 'proses',
        ]));
        $responseProses->assertStatus(200);
        $prosesTitles = collect($responseProses->json())->pluck('title')->all();
        $this->assertContains($this->coopProses->judul, $prosesTitles);
    }

    /**
     * Uji Kombinasi Majemuk (Multi-Filter): Tanggal + Jenis + Unit Pelaksana + Status
     */
    public function test_multi_filter_combination()
    {
        $response = $this->actingAs($this->userHumas)->getJson(route('unit.dkerjasama.preview', [
            'tanggal_awal' => '2026-01-01',
            'tanggal_akhir' => '2026-12-31',
            'jenis_dokumentasi' => 'MoA',
            'tipe_pelaksana' => 'pusat',
            'pusat_id' => $this->pusatKarir->id,
            'status' => 'aktif',
        ]));

        $response->assertStatus(200);
        $titles = collect($response->json())->pluck('title')->all();

        // Hanya MoA Pusat Karir yang memenuhi SEMUA kriteria
        $this->assertContains($this->coopMoaPusat->judul, $titles);
        $this->assertNotContains($this->coopMouInstansi->judul, $titles); // Jenis MoU
        $this->assertNotContains($this->coopMoaJurusan->judul, $titles);   // Tanggal 2025 & Unit Jurusan
        $this->assertNotContains($this->coopIaUpa->judul, $titles);        // Jenis IA & Unit UPA
    }

    /**
     * Uji Halaman Utama SSR dengan parameter Filter
     */
    public function test_filter_pada_halaman_view_utama()
    {
        $response = $this->actingAs($this->userHumas)->get(route('unit.dkerjasama', [
            'jenis_dokumentasi' => 'MoU',
            'status' => 'aktif',
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->coopMouInstansi->judul);
        $response->assertDontSee($this->coopExpired->judul);
    }

    /**
     * Uji Ekspor PDF dengan parameter Filter
     */
    public function test_filter_pada_export_pdf()
    {
        $response = $this->actingAs($this->userHumas)->get(route('unit.dkerjasama.pdf', [
            'jenis_dokumentasi' => 'MoU',
            'status' => 'aktif',
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /**
     * Uji Export Excel dengan parameter Filter
     */
    public function test_filter_pada_export_excel()
    {
        $response = $this->actingAs($this->userHumas)->get(route('unit.dkerjasama.excel', [
            'jenis_dokumentasi' => 'MoA',
        ]));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains((string) $response->headers->get('content-disposition'), 'laporan_kerjasama_unit.xlsx') ||
            str_contains((string) $response->headers->get('Content-Type'), 'spreadsheetml')
        );
    }
}

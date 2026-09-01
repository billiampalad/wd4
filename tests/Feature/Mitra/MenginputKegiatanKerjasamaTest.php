<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\DetailKegiatan;
use App\Models\Indikator;
use App\Models\JenisKerjasama;
use App\Models\KegiatanKerjasama;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use App\Models\Role;
use App\Models\Sasaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MenginputKegiatanKerjasamaTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupUnitUser()
    {
        $roleUnit = Role::firstOrCreate(['name' => 'unit_kerja'], ['guard_name' => 'web']);
        return User::factory()->create([
            'role_id' => $roleUnit->id,
            'name' => 'Pengelola Kemitraan Unit',
            'email' => 'unit.kegiatan@polimdo.ac.id',
        ]);
    }

    public function test_user_can_access_create_kegiatan_page_with_disahkan_ia_documents()
    {
        $user = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'BUMN Telekomunikasi']);
        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Telkom Akses Manado'],
            ['id_klasifikasi' => $klasifikasi->id, 'kategori' => 'nasional']
        );

        $coop = Cooperation::create([
            'judul' => 'Implementation Agreement Magang Fiber Optic',
            'jenis' => 'IA',
            'doc_number' => 'IA/TELKOM/2026/01',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => '2026-01-01',
            'end_date' => '2027-01-01',
        ]);

        $response = $this->actingAs($user)->get(route('unit.kegiatan.create'));

        $response->assertStatus(200);
        $response->assertSee('Implementation Agreement Magang Fiber Optic');
        $response->assertSee('PT Telkom Akses Manado');
    }

    public function test_create_kegiatan_page_shows_empty_message_when_no_disahkan_ia_available()
    {
        $user = $this->setupUnitUser();

        // Make sure no IA is Disahkan
        Cooperation::where('jenis', 'IA')->where('status_dokumen', 'Disahkan')->update(['status_dokumen' => 'Draft']);

        $response = $this->actingAs($user)->get(route('unit.kegiatan.create'));

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Dokumen IA yang Disahkan');
    }

    public function test_user_can_store_kegiatan_kerjasama_linked_to_ia_document()
    {
        $user = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Schneider Electric'], ['id_klasifikasi' => $klasifikasi->id]);
        $sasaran = Sasaran::firstOrCreate(['deskripsi' => 'Meningkatnya Kualitas Lulusan']);
        $indikator = Indikator::firstOrCreate(['nama_indikator' => 'Persentase Lulusan yang Bekerja'], ['sasaran_id' => $sasaran->id]);
        $jenisKerjasama = JenisKerjasama::firstOrCreate(['nama' => 'Magang Industri']);

        $coop = Cooperation::create([
            'judul' => 'IA Otomasi Industri dan Magang Mahasiswa',
            'jenis' => 'IA',
            'doc_number' => 'IA/SCH/2026/05',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => '2026-02-01',
            'end_date' => '2027-02-01',
        ]);

        $payload = [
            'nama_kegiatan' => 'Pelatihan & Sertifikasi PLC Schneider',
            'cooperation_id' => $coop->id,
            'jenis_kerjasama_id' => $jenisKerjasama->id,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
            'sasaran_id' => $sasaran->id,
            'indikator_id' => $indikator->id,
            'volume_luaran' => '30 Mahasiswa Bersertifikat',
            'output' => '30 Mahasiswa Jurusan Teknik Elektro lulus uji kompetensi PLC.',
            'outcome' => 'Peningkatan serapan lulusan siap kerja di sektor manufaktur.',
        ];

        $response = $this->actingAs($user)->post(route('unit.kegiatan.store'), $payload);

        $response->assertRedirect(route('unit.kegiatan.index'));
        $response->assertSessionHas('success');

        // Assert record created in kegiatan_kerjasamas
        $this->assertDatabaseHas('kegiatan_kerjasamas', [
            'nama_kegiatan' => 'Pelatihan & Sertifikasi PLC Schneider',
            'cooperation_id' => $coop->id,
            'status' => 'Perencanaan',
        ]);

        // Assert record created in detail_kegiatans
        $this->assertDatabaseHas('detail_kegiatans', [
            'cooperation_id' => $coop->id,
            'volume_luaran' => '30 Mahasiswa Bersertifikat',
            'sasaran_id' => $sasaran->id,
            'indikator_id' => $indikator->id,
        ]);
    }

    public function test_kegiatan_validation_errors_when_required_fields_missing()
    {
        $user = $this->setupUnitUser();

        $response = $this->actingAs($user)->post(route('unit.kegiatan.store'), []);

        $response->assertSessionHasErrors([
            'nama_kegiatan',
            'cooperation_id',
            'periode_mulai',
        ]);
    }

    public function test_user_can_view_kegiatan_detail()
    {
        $user = $this->setupUnitUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Manado Tech Hub'], ['id_klasifikasi' => $klasifikasi->id]);

        $coop = Cooperation::create([
            'judul' => 'IA Bootcamp Web Fullstack',
            'jenis' => 'IA',
            'doc_number' => 'IA/MTH/2026/09',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => '2026-03-01',
            'end_date' => '2027-03-01',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Bootcamp Flutter Mobile 2026',
            'periode_mulai' => '2026-09-10',
            'periode_selesai' => '2026-11-10',
            'status' => 'Perencanaan',
        ]);

        DetailKegiatan::create([
            'kegiatan_kerjasama_id' => $kegiatan->id,
            'cooperation_id' => $coop->id,
            'volume_luaran' => '20 Aplikasi Mobile',
            'output' => '20 prototipe aplikasi mobile mahasiswa TI.',
        ]);

        $response = $this->actingAs($user)->get(route('unit.kegiatan.show', $kegiatan->id));

        $response->assertStatus(200);
        $response->assertSee('Bootcamp Flutter Mobile 2026');
        $response->assertSee('PT Manado Tech Hub');
        $response->assertSee('20 Aplikasi Mobile');
    }
}

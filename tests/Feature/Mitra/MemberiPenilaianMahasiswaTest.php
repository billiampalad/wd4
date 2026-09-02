<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\Jurusan;
use App\Models\KegiatanKerjasama;
use App\Models\KegiatanMahasiswa;
use App\Models\Klasifikasi;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\Pembimbing;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberiPenilaianMahasiswaTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupMitraAndUser()
    {
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri Telekomunikasi']);
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);

        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Lintasarta Manado'],
            [
                'id_klasifikasi' => $klasifikasi->id,
                'kategori' => 'nasional',
                'status_akses' => 'Aktif',
            ]
        );

        $mitraUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'HR Lintasarta',
            'email' => 'hr.lintasarta@partner.com',
        ]);

        return [$mitra, $mitraUser];
    }

    protected function setupProdiUser()
    {
        $roleProdi = Role::firstOrCreate(['name' => 'prodi'], ['guard_name' => 'web']);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);
        $prodi = Prodi::firstOrCreate(['nama_prodi' => 'D4 Teknik Informatika'], ['jurusan_id' => $jurusan->id]);

        $prodiUser = User::factory()->create([
            'role_id' => $roleProdi->id,
            'name' => 'Koordinator Magang TI',
            'email' => 'prodi.magang@polimdo.ac.id',
        ]);

        return [$prodiUser, $prodi];
    }

    public function test_guest_cannot_access_penilaian_page()
    {
        $response = $this->get(route('mitra.penilaian.index'));
        $response->assertRedirect('/login');
    }

    public function test_mitra_can_access_penilaian_index_and_view_placed_students()
    {
        [$mitra, $mitraUser] = $this->setupMitraAndUser();
        [$prodiUser, $prodi] = $this->setupProdiUser();

        $mhs = Mahasiswa::create([
            'nim' => '21024011',
            'nama' => 'Jonathan Christie Senduk',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Program Magang Cloud & Network',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Magang Network Security 2026',
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2027-02-28',
            'status' => 'Perencanaan',
        ]);

        $penempatan = KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2027-02-28',
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($mitraUser)->get(route('mitra.penilaian.index'));

        $response->assertStatus(200);
        $response->assertSee('Kegiatan &amp; Penilaian Magang', false);
        $response->assertSee('Jonathan Christie Senduk');
        $response->assertSee('21024011');
        $response->assertSee('Magang Network Security 2026');
        $response->assertSee('PT Lintasarta Manado');
    }

    public function test_mitra_can_submit_grading_for_placed_student()
    {
        [$mitra, $mitraUser] = $this->setupMitraAndUser();
        [$prodiUser, $prodi] = $this->setupProdiUser();

        $mhs = Mahasiswa::create([
            'nim' => '21024022',
            'nama' => 'Natasha Stephanie Wowor',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Magang Data Center',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Magang Cloud Infra 2026',
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2027-01-31',
            'status' => 'Perencanaan',
        ]);

        $penempatan = KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2027-01-31',
            'status' => 'Aktif',
        ]);

        $payload = [
            'nilai_mitra' => 94.5,
            'catatan_mitra' => 'Mahasiswa memiliki pemahaman arsitektur jaringan yang sangat baik, disiplin, dan inisiatif tinggi.',
        ];

        $response = $this->actingAs($mitraUser)->put(route('mitra.penilaian.update', $penempatan->id), $payload);

        $response->assertRedirect(route('mitra.penilaian.index'));
        $response->assertSessionHas('success');

        // 1. Assert record updated in kegiatan_mahasiswas
        $this->assertDatabaseHas('kegiatan_mahasiswas', [
            'id' => $penempatan->id,
            'nilai_mitra' => 94.50,
            'catatan_mitra' => 'Mahasiswa memiliki pemahaman arsitektur jaringan yang sangat baik, disiplin, dan inisiatif tinggi.',
            'status' => 'Selesai',
        ]);

        // 2. Assert notification dispatched to prodi
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $prodiUser->id,
            'type' => 'penilaian_magang',
            'source_id' => $penempatan->id,
        ]);
    }

    public function test_grading_validation_errors_when_score_is_missing_or_out_of_range()
    {
        [$mitra, $mitraUser] = $this->setupMitraAndUser();
        [$prodiUser, $prodi] = $this->setupProdiUser();

        $mhs = Mahasiswa::create([
            'nim' => '21024033',
            'nama' => 'Mahasiswa Test Nilai',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Test Nilai',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'mitra_id' => $mitra->id,
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Kegiatan Uji Nilai',
            'periode_mulai' => '2026-09-01',
            'status' => 'Perencanaan',
        ]);

        $penempatan = KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
            'status' => 'Aktif',
        ]);

        // Case 1: Empty score
        $response = $this->actingAs($mitraUser)->put(route('mitra.penilaian.update', $penempatan->id), [
            'nilai_mitra' => '',
        ]);
        $response->assertSessionHasErrors(['nilai_mitra']);

        // Case 2: Score > 100
        $response = $this->actingAs($mitraUser)->put(route('mitra.penilaian.update', $penempatan->id), [
            'nilai_mitra' => 120,
        ]);
        $response->assertSessionHasErrors(['nilai_mitra']);

        // Case 3: Score < 0
        $response = $this->actingAs($mitraUser)->put(route('mitra.penilaian.update', $penempatan->id), [
            'nilai_mitra' => -10,
        ]);
        $response->assertSessionHasErrors(['nilai_mitra']);
    }

    public function test_mitra_cannot_grade_student_placed_in_another_mitra()
    {
        [$mitraA, $mitraUserA] = $this->setupMitraAndUser();
        [$prodiUser, $prodi] = $this->setupProdiUser();

        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitraB = Mitra::create([
            'nama_mitra' => 'PT Mitra Lainnya',
            'id_klasifikasi' => $klasifikasi->id,
        ]);

        $mhs = Mahasiswa::create([
            'nim' => '21024044',
            'nama' => 'Mahasiswa di Mitra B',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Mitra B',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'mitra_id' => $mitraB->id,
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Kegiatan di Mitra B',
            'periode_mulai' => '2026-09-01',
            'status' => 'Perencanaan',
        ]);

        $penempatanB = KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitraB->id, // Belongs to Mitra B
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
            'status' => 'Aktif',
        ]);

        // Mitra User A attempts to grade placement of Mitra B
        $response = $this->actingAs($mitraUserA)->put(route('mitra.penilaian.update', $penempatanB->id), [
            'nilai_mitra' => 88.0,
            'catatan_mitra' => 'Mencoba menilai instansi lain.',
        ]);

        $response->assertStatus(404);
    }
}

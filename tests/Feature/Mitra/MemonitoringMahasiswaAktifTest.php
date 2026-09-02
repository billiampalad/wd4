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

class MemonitoringMahasiswaAktifTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupMitraAndUser()
    {
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri IT & Telekomunikasi']);
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);

        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Telkom Akses Manado'],
            [
                'id_klasifikasi' => $klasifikasi->id,
                'kategori' => 'nasional',
                'status_akses' => 'Aktif',
            ]
        );

        $mitraUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
            'name' => 'Supervisor Telkom Akses',
            'email' => 'spv.telkom@partner.com',
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
            'name' => 'Koor Magang Prodi TI',
            'email' => 'koor.ti@polimdo.ac.id',
        ]);

        return [$prodiUser, $prodi];
    }

    protected function setupPimpinanUser()
    {
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan'], ['guard_name' => 'web']);

        return User::factory()->create([
            'role_id' => $rolePimpinan->id,
            'name' => 'Direktur Polimdo',
            'email' => 'direktur@polimdo.ac.id',
        ]);
    }

    public function test_mitra_can_monitor_active_students_with_metrics_and_filters()
    {
        [$mitra, $mitraUser] = $this->setupMitraAndUser();
        [$prodiUser, $prodi] = $this->setupProdiUser();

        $mhs1 = Mahasiswa::create([
            'nim' => '21024001',
            'nama' => 'David Christian Wowor',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $mhs2 = Mahasiswa::create([
            'nim' => '21024002',
            'nama' => 'Griselda Pangkey',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Magang Jaringan Fiber Optic',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Praktik Fiber Optic 2026',
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2027-02-28',
            'status' => 'Perencanaan',
        ]);

        // Student 1: Aktif, belum dinilai
        $penempatan1 = KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs1->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2027-02-28',
            'status' => 'Aktif',
            'nilai_mitra' => null,
        ]);

        // Student 2: Selesai, sudah dinilai
        $penempatan2 = KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs2->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2027-02-28',
            'status' => 'Selesai',
            'nilai_mitra' => 95.0,
            'catatan_mitra' => 'Sangat memuaskan.',
        ]);

        $response = $this->actingAs($mitraUser)->get(route('mitra.penilaian.index'));

        $response->assertStatus(200);
        $response->assertSee('Kegiatan &amp; Penilaian Magang', false);
        $response->assertSee('David Christian Wowor');
        $response->assertSee('21024001');
        $response->assertSee('Griselda Pangkey');
        $response->assertSee('21024002');
        $response->assertSee('PT Telkom Akses Manado');
        $response->assertSee('Total Peserta Magang');
        $response->assertSee('Sedang Magang (Aktif)');
        $response->assertSee('Menunggu Penilaian');
    }

    public function test_mitra_can_view_student_monitoring_detail_via_ajax()
    {
        [$mitra, $mitraUser] = $this->setupMitraAndUser();
        [$prodiUser, $prodi] = $this->setupProdiUser();

        $mhs = Mahasiswa::create([
            'nim' => '21024003',
            'nama' => 'Kevin Sanjaya Mamuaja',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Magang Cloud Infrastructure',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Magang Cloud DevOps 2026',
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

        Pembimbing::create([
            'kegiatan_mahasiswa_id' => $penempatan->id,
            'nama_pembimbing' => 'Dosen Pembimbing Cloud',
            'tipe' => 'Internal',
            'kontak' => '081122334455',
        ]);

        $response = $this->actingAs($mitraUser)->getJson(route('mitra.penilaian.show', $penempatan->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $penempatan->id,
                'status' => 'Aktif',
                'mahasiswa' => [
                    'nim' => '21024003',
                    'nama' => 'Kevin Sanjaya Mamuaja',
                ],
            ],
        ]);
    }

    public function test_prodi_can_monitor_active_students_list_and_details()
    {
        [$prodiUser, $prodi] = $this->setupProdiUser();
        [$mitra] = $this->setupMitraAndUser();

        $mhs = Mahasiswa::create([
            'nim' => '21024004',
            'nama' => 'Jessica Mila Polii',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Magang Multimedia',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Magang Desain UI/UX 2026',
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

        // 1. Monitor list
        $responseList = $this->actingAs($prodiUser)->get(route('prodi.penempatan.index'));
        $responseList->assertStatus(200);
        $responseList->assertSee('Jessica Mila Polii');
        $responseList->assertSee('21024004');

        // 2. Monitor detail
        $responseDetail = $this->actingAs($prodiUser)->get(route('prodi.penempatan.show', $penempatan->id));
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee('Jessica Mila Polii');
        $responseDetail->assertSee('21024004');
        $responseDetail->assertSee('PT Telkom Akses Manado');
    }

    public function test_prodi_dashboard_monitors_active_students()
    {
        [$prodiUser, $prodi] = $this->setupProdiUser();
        [$mitra] = $this->setupMitraAndUser();

        $mhs = Mahasiswa::create([
            'nim' => '21024005',
            'nama' => 'Arnold Sompotan',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Cyber Security',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Pelatihan Cyber Security 2026',
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
            'status' => 'Perencanaan',
        ]);

        KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($prodiUser)->get(route('prodi.penempatan.index'));

        $response->assertStatus(200);
        $response->assertSee('Arnold Sompotan');
        $response->assertSee('21024005');
    }
}

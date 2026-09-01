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

class MenginputPesertaMahasiswaTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupProdiUser()
    {
        $roleProdi = Role::firstOrCreate(['name' => 'prodi'], ['guard_name' => 'web']);
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Elektro']);
        $prodi = Prodi::firstOrCreate(['nama_prodi' => 'D4 Teknik Informatika'], ['jurusan_id' => $jurusan->id]);

        return [
            User::factory()->create([
                'role_id' => $roleProdi->id,
                'name' => 'Koordinator Magang Prodi TI',
                'email' => 'prodi.ti@polimdo.ac.id',
            ]),
            $prodi,
        ];
    }

    public function test_prodi_can_access_penempatan_create_page()
    {
        [$prodiUser, $prodi] = $this->setupProdiUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Teknologi Informasi']);
        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Astra Graphia Information Technology'],
            ['id_klasifikasi' => $klasifikasi->id]
        );

        $mhs = Mahasiswa::create([
            'nim' => '21024099',
            'nama' => 'Christian Ronaldo Palad',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'email' => 'christian@student.polimdo.ac.id',
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Program Magang IT AGIT',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Magang Industri Fullstack Engineer Batch 1',
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2027-02-28',
            'status' => 'Perencanaan',
        ]);

        $response = $this->actingAs($prodiUser)->get(route('prodi.penempatan.create'));

        $response->assertStatus(200);
        $response->assertSee('Formulir Penempatan Mahasiswa');
        $response->assertSee('Christian Ronaldo Palad');
        $response->assertSee('21024099');
        $response->assertSee('Magang Industri Fullstack Engineer Batch 1');
    }

    public function test_prodi_can_store_penempatan_mahasiswa_with_pembimbing()
    {
        [$prodiUser, $prodi] = $this->setupProdiUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Software House']);
        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Manado Software Studio'],
            ['id_klasifikasi' => $klasifikasi->id]
        );

        $mhs = Mahasiswa::create([
            'nim' => '21024088',
            'nama' => 'Maria V. S. Tulung',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Magang Mobile Dev',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Magang Mobile Flutter 2026',
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
            'status' => 'Perencanaan',
        ]);

        $payload = [
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
            'nama_pembimbing_internal' => 'Ir. Steven Kawalo, M.T.',
            'kontak_pembimbing_internal' => '081234567890',
            'nama_pembimbing_eksternal' => 'Bpk. Richard Liow (Lead Engineer)',
            'kontak_pembimbing_eksternal' => 'richard@manadosoftware.com',
        ];

        $response = $this->actingAs($prodiUser)->post(route('prodi.penempatan.store'), $payload);

        $response->assertRedirect(route('prodi.penempatan.index'));
        $response->assertSessionHas('success');

        // 1. Assert record in kegiatan_mahasiswas
        $this->assertDatabaseHas('kegiatan_mahasiswas', [
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitra->id,
            'status' => 'Aktif',
        ]);

        $penempatan = KegiatanMahasiswa::where('kegiatan_id', $kegiatan->id)->where('mahasiswa_id', $mhs->id)->first();

        // 2. Assert Pembimbing records
        $this->assertDatabaseHas('pembimbings', [
            'kegiatan_mahasiswa_id' => $penempatan->id,
            'nama_pembimbing' => 'Ir. Steven Kawalo, M.T.',
            'tipe' => 'Internal',
        ]);

        $this->assertDatabaseHas('pembimbings', [
            'kegiatan_mahasiswa_id' => $penempatan->id,
            'nama_pembimbing' => 'Bpk. Richard Liow (Lead Engineer)',
            'tipe' => 'Eksternal',
        ]);
    }

    public function test_cannot_place_duplicate_student_in_same_kegiatan()
    {
        [$prodiUser, $prodi] = $this->setupProdiUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Mitra Duplikasi'], ['id_klasifikasi' => $klasifikasi->id]);

        $mhs = Mahasiswa::create([
            'nim' => '21024077',
            'nama' => 'Siswa Percobaan',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Kegiatan Duplikasi',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Kegiatan Pelatihan Duplikasi',
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
            'status' => 'Perencanaan',
        ]);

        // Place student for the first time
        KegiatanMahasiswa::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
            'status' => 'Aktif',
        ]);

        // Attempt to place again
        $payload = [
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mhs->id,
            'mitra_id' => $mitra->id,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
            'nama_pembimbing_internal' => 'Dosen A',
            'nama_pembimbing_eksternal' => 'Mentor B',
        ];

        $response = $this->actingAs($prodiUser)->post(route('prodi.penempatan.store'), $payload);

        $response->assertSessionHas('error');
    }

    public function test_validation_errors_when_required_fields_missing()
    {
        [$prodiUser] = $this->setupProdiUser();

        $response = $this->actingAs($prodiUser)->post(route('prodi.penempatan.store'), []);

        $response->assertSessionHasErrors([
            'kegiatan_id',
            'mahasiswa_id',
            'mitra_id',
            'periode_mulai',
            'nama_pembimbing_internal',
            'nama_pembimbing_eksternal',
        ]);
    }

    public function test_prodi_can_view_penempatan_detail()
    {
        [$prodiUser, $prodi] = $this->setupProdiUser();
        $klasifikasi = Klasifikasi::firstOrCreate(['nama' => 'Industri']);
        $mitra = Mitra::firstOrCreate(['nama_mitra' => 'PT Manado Media Group'], ['id_klasifikasi' => $klasifikasi->id]);

        $mhs = Mahasiswa::create([
            'nim' => '21024066',
            'nama' => 'Gabriel Mamuaja',
            'prodi_id' => $prodi->id,
            'angkatan' => 2021,
            'status' => 'Aktif',
        ]);

        $coop = Cooperation::create([
            'judul' => 'IA Jurnalisme Digital',
            'jenis' => 'IA',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
        ]);

        $kegiatan = KegiatanKerjasama::create([
            'cooperation_id' => $coop->id,
            'nama_kegiatan' => 'Praktik Industri Media 2026',
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
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

        Pembimbing::create([
            'kegiatan_mahasiswa_id' => $penempatan->id,
            'nama_pembimbing' => 'Dosen Pembimbing TI',
            'tipe' => 'Internal',
        ]);

        Pembimbing::create([
            'kegiatan_mahasiswa_id' => $penempatan->id,
            'nama_pembimbing' => 'Mentor Redaksi',
            'tipe' => 'Eksternal',
        ]);

        $response = $this->actingAs($prodiUser)->get(route('prodi.penempatan.show', $penempatan->id));

        $response->assertStatus(200);
        $response->assertSee('Gabriel Mamuaja');
        $response->assertSee('21024066');
        $response->assertSee('PT Manado Media Group');
        $response->assertSee('Dosen Pembimbing TI');
        $response->assertSee('Mentor Redaksi');
    }
}

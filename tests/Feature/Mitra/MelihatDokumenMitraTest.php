<?php

namespace Tests\Feature\Mitra;

use App\Models\Cooperation;
use App\Models\CooperationDetail;
use App\Models\JenisKerjasama;
use App\Models\Mitra;
use App\Models\Pejabat;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MelihatDokumenMitraTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupMitraUser()
    {
        $roleMitra = Role::firstOrCreate(['name' => 'mitra'], ['guard_name' => 'web']);
        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Mitra Solusi Mandiri'],
            ['status_akses' => 'Aktif']
        );

        $mitraUser = User::factory()->create([
            'role_id' => $roleMitra->id,
            'mitra_id' => $mitra->id,
        ]);

        return [$mitra, $mitraUser];
    }

    public function test_guest_cannot_access_mitra_dokumen()
    {
        $response = $this->get(route('mitra.dokumen.index'));
        $response->assertRedirect('/login');
    }

    public function test_mitra_can_view_dokumen_list_and_filters()
    {
        [$mitra, $mitraUser] = $this->setupMitraUser();

        $coop = Cooperation::create([
            'judul' => 'Kerjasama Strategis Industri 4.0',
            'jenis' => 'MoU',
            'doc_number' => 'MOU/2026/001',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Institusi',
            'start_date' => '2026-01-01',
            'end_date' => '2028-12-31',
        ]);

        $response = $this->actingAs($mitraUser)->get(route('mitra.dokumen.index'));

        $response->assertStatus(200);
        $response->assertSee('Kerjasama Strategis Industri 4.0');
        $response->assertSee('MOU/2026/001');
        $response->assertSee('jenisFilter');
        $response->assertSee('periodeFilter');
        $response->assertSee('statusFilter');
    }

    public function test_mitra_can_view_dokumen_detail_page()
    {
        [$mitra, $mitraUser] = $this->setupMitraUser();

        $pejabatInternal = Pejabat::firstOrCreate(
            ['nama' => 'Dr. Maryke Alelo, MBA'],
            ['jabatan' => 'Direktur', 'jenis' => 'internal']
        );
        $pejabatMitra = Pejabat::firstOrCreate(
            ['nama' => 'Ir. Budi Santoso'],
            ['jabatan' => 'Direktur Utama', 'jenis' => 'eksternal']
        );

        $jenisKs = JenisKerjasama::firstOrCreate(['nama' => 'Pengembangan Kurikulum']);

        $coop = Cooperation::create([
            'judul' => 'Detail Dokumen Kerja Sama Riset',
            'jenis' => 'MoA',
            'doc_number' => 'MOA/PLM/2026/088',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra->id,
            'tingkat' => 'Jurusan',
            'start_date' => '2026-03-01',
            'end_date' => '2029-03-01',
            'penandatangan_internal_id' => $pejabatInternal->id,
            'penandatangan_mitra_id' => $pejabatMitra->id,
            'document_link' => 'https://example.com/files/dokumen_ks_088.pdf',
        ]);

        $kegiatan = new \App\Models\KegiatanKerjasama();
        $kegiatan->cooperation_id = $coop->id;
        $kegiatan->nama_kegiatan = 'Kegiatan Riset Bersama';
        $kegiatan->save();

        $detail = new \App\Models\DetailKegiatan();
        $detail->kegiatan_kerjasama_id = $kegiatan->id;
        $detail->cooperation_id = $coop->id;
        $detail->jenis_kerjasama_id = $jenisKs->id;
        $detail->volume_luaran = 1;
        $detail->save();

        $response = $this->actingAs($mitraUser)->get(route('mitra.dokumen.show', $coop->id));

        $response->assertStatus(200);
        $response->assertSee('Detail Dokumen Kerja Sama Riset');
        $response->assertSee('MOA/PLM/2026/088');
        $response->assertSee('PT Mitra Solusi Mandiri');
        $response->assertSee('Dr. Maryke Alelo, MBA');
        $response->assertSee('Ir. Budi Santoso');
        $response->assertSee('https://example.com/files/dokumen_ks_088.pdf');
    }

    public function test_mitra_cannot_view_other_mitra_dokumen_detail()
    {
        [$mitra1, $mitraUser1] = $this->setupMitraUser();

        $mitra2 = Mitra::firstOrCreate(
            ['nama_mitra' => 'PT Mitra Kompetitor'],
            ['status_akses' => 'Aktif']
        );

        $coopMitra2 = Cooperation::create([
            'judul' => 'Dokumen Rahasia Mitra Lain',
            'jenis' => 'MoU',
            'status_dokumen' => 'Disahkan',
            'status_berlaku' => 'Aktif',
            'mitra_id' => $mitra2->id,
            'tingkat' => 'Institusi',
        ]);

        $response = $this->actingAs($mitraUser1)->get(route('mitra.dokumen.show', $coopMitra2->id));

        $response->assertStatus(404);
    }
}

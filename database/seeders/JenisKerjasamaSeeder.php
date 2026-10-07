<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisKerjasama;

class JenisKerjasamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            1 => 'Pelatihan',
            2 => 'Pemagangan',
            3 => 'Penelitian Bersama',
            4 => 'Penelitian Bersama - Artikel/Jurnal ilmiah',
            5 => 'Pelatihan Bersama - Paten',
            6 => 'Asistensi Mengajar di Satuan Pendidikan-Kampus Merdeka',
            7 => 'Gelar Bersama (Joint Degree)',
            8 => 'Gelar Ganda (Dual Degree)',
            9 => 'Kegiatan Wirausaha-Kampus Merdeka',
            10 => 'Magang/Praktik Kerja-Kampus Merdeka',
            11 => 'Membangun Desa/KKN Tematik-Kampus Merdeka',
            12 => 'Penelitian Bersama - Prototipe',
            13 => 'Penelitian/Riset-Kampus Merdeka',
            14 => 'Penerbitan Berkala Ilmiah',
            15 => 'Pengabdian Kepada Masyarakat',
            16 => 'Pengembangan Kurikulum/Program Bersama',
            17 => 'Pengembangan Pusat Penelitian dan Pengembangan Keilmuan',
            18 => 'Pengembangan Sistem/Produk',
            19 => 'Pengiriman Praktisi sebagai Dosen',
            20 => 'Penyaluran Lulusan',
            21 => 'Penyelenggaraan Seminar/Konferensi Ilmiah',
            22 => 'Pertukaran Dosen',
            23 => 'Pertukaran Mahasiswa',
            24 => 'Pertukaran Pelajar-Kampus Merdeka',
            25 => 'Proyek kemanusiaan-Kampus Merdeka',
            26 => 'Studi/Proyek Independen-Kampus Merdeka',
            27 => 'Transfer Kredit',
            28 => 'Visiting Professor',
        ];

        foreach ($items as $id => $nama) {
            JenisKerjasama::firstOrCreate(
                ['id' => $id],
                [
                    'nama' => $nama,
                    'nama_kerjasama' => $nama,
                ]
            );
        }
    }
}

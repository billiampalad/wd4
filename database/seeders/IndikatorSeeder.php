<?php

namespace Database\Seeders;

use App\Models\Indikator;
use App\Models\Sasaran;
use Illuminate\Database\Seeder;

class IndikatorSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Meningkatnya kualitas lulusan pendidikan tinggi' => [
                'Kesiapan kerja lulusan',
                'Mahasiswa di luar kampus',
            ],
            'Meningkatnya inovasi perguruan tinggi dalam rangka meningkatkan mutu pendidikan' => [
                'Link and match PTS',
            ],
            'Meningkatnya kualitas dosen pendidikan tinggi' => [
                'Dosen di luar kampus',
                'Kualifikasi dosen',
                'Penerapan riset dosen',
            ],
            'Meningkatnya kualitas kurikulum dan pembelajaran' => [
                'Kemitraan program studi',
                'Pembelajaran dalam kelas',
                'Akreditasi Internasional',
            ],
            'Meningkatnya program studi yang berkualitas' => [
                'IKK 2.5.2.1 Persentase Prodi bekerjasama dengan mitra',
            ],
        ];

        foreach ($data as $sasaranText => $indikators) {
            $sasaran = Sasaran::firstOrCreate(['deskripsi' => $sasaranText]);

            foreach ($indikators as $indikatorText) {
                Indikator::firstOrCreate([
                    'sasaran_id' => $sasaran->id,
                    'nama_indikator' => $indikatorText,
                ]);
            }
        }
    }
}

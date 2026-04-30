<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SasaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sasarans = [
            'Meningkatnya Kualitas Lulusan Perguruan Tinggi',
            'Meningkatnya Inovasi Perguruan Tinggi Dalam Rangka Meningkatkan Mutu Pendidikan',
            'Meningkatnya Kualitas Dosen Pendidikan Tinggi',
            'Meningkatkan Kualitas Kurikulum dan Pembelajaran',
            'Meningkatnya Program Studi yang Berkualitas'
        ];

        foreach ($sasarans as $sasaran) {
            \App\Models\Sasaran::create(['deskripsi' => $sasaran]);
        }
    }
}

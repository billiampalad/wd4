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
            'Meningkatnya kualitas lulusan pendidikan tinggi',
            'Meningkatnya inovasi perguruan tinggi dalam rangka meningkatkan mutu pendidikan',
            'Meningkatnya kualitas dosen pendidikan tinggi',
            'Meningkatnya kualitas kurikulum dan pembelajaran',
            'Meningkatnya program studi yang berkualitas',
        ];

        foreach ($sasarans as $sasaran) {
            \App\Models\Sasaran::firstOrCreate(['deskripsi' => $sasaran]);
        }
    }
}

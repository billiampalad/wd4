<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $groups = [
            'Meningkatnya kualitas lulusan pendidikan tinggi' => [
                'Meningkatkan kualitas lulusan pendidikan tinggi',
                'Meningkatnya Kualitas Lulusan Perguruan Tinggi',
            ],
            'Meningkatnya inovasi perguruan tinggi dalam rangka meningkatkan mutu pendidikan' => [
                'Meningkatnya inovasi pergurusan tinggi dalam rangka meningkatkan mutu pendidikan',
                'Meningkatnya Inovasi Perguruan Tinggi Dalam Rangka Meningkatkan Mutu Pendidikan',
            ],
            'Meningkatnya kualitas dosen pendidikan tinggi' => [
                'Meningkatnya Kualitas Dosen Pendidikan Tinggi',
            ],
            'Meningkatnya kualitas kurikulum dan pembelajaran' => [
                'Meningkatkan Kualitas Kurikulum dan Pembelajaran',
            ],
            'Meningkatnya program studi yang berkualitas' => [
                'Meningkatnya Program Studi yang Berkualitas',
            ],
        ];

        foreach ($groups as $canonical => $aliases) {
            $names = array_merge([$canonical], $aliases);
            $sasaranIds = DB::table('sasarans')
                ->whereIn('deskripsi', $names)
                ->orderBy('id')
                ->pluck('id')
                ->all();

            if (empty($sasaranIds)) {
                continue;
            }

            $keeperId = $sasaranIds[0];

            foreach (array_slice($sasaranIds, 1) as $duplicateId) {
                DB::table('detail_kegiatans')
                    ->where('sasaran_id', $duplicateId)
                    ->update(['sasaran_id' => $keeperId]);

                $duplicateIndikators = DB::table('indikators')
                    ->where('sasaran_id', $duplicateId)
                    ->get();

                foreach ($duplicateIndikators as $duplicateIndikator) {
                    $keeperIndikatorId = DB::table('indikators')
                        ->where('sasaran_id', $keeperId)
                        ->where('nama_indikator', $duplicateIndikator->nama_indikator)
                        ->value('id');

                    if (!$keeperIndikatorId) {
                        $keeperIndikatorId = DB::table('indikators')->insertGetId([
                            'sasaran_id' => $keeperId,
                            'nama_indikator' => $duplicateIndikator->nama_indikator,
                            'created_at' => $duplicateIndikator->created_at,
                            'updated_at' => now(),
                        ]);
                    }

                    DB::table('detail_kegiatans')
                        ->where('indikator_id', $duplicateIndikator->id)
                        ->update(['indikator_id' => $keeperIndikatorId]);

                    DB::table('indikators')->where('id', $duplicateIndikator->id)->delete();
                }

                DB::table('sasarans')->where('id', $duplicateId)->delete();
            }

            DB::table('sasarans')
                ->where('id', $keeperId)
                ->update(['deskripsi' => $canonical, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Normalization is intentionally not reversed.
    }
};

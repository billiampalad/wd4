<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sasaran_id')->constrained('sasarans')->cascadeOnDelete();
            $table->string('nama_indikator');
            $table->timestamps();

            $table->unique(['sasaran_id', 'nama_indikator']);
        });

        $now = now();
        $sasaranRenames = [
            'Meningkatnya Kualitas Lulusan Perguruan Tinggi' => 'Meningkatnya kualitas lulusan pendidikan tinggi',
            'Meningkatnya Inovasi Perguruan Tinggi Dalam Rangka Meningkatkan Mutu Pendidikan' => 'Meningkatnya inovasi perguruan tinggi dalam rangka meningkatkan mutu pendidikan',
            'Meningkatnya Kualitas Dosen Pendidikan Tinggi' => 'Meningkatnya kualitas dosen pendidikan tinggi',
            'Meningkatkan Kualitas Kurikulum dan Pembelajaran' => 'Meningkatnya kualitas kurikulum dan pembelajaran',
            'Meningkatnya Program Studi yang Berkualitas' => 'Meningkatnya program studi yang berkualitas',
        ];

        foreach ($sasaranRenames as $old => $new) {
            DB::table('sasarans')
                ->where('deskripsi', $old)
                ->update(['deskripsi' => $new, 'updated_at' => $now]);
        }

        $indikatorsBySasaran = [
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

        foreach ($indikatorsBySasaran as $sasaran => $indikators) {
            $sasaranId = DB::table('sasarans')->whereRaw('LOWER(deskripsi) = ?', [strtolower($sasaran)])->value('id');

            if (!$sasaranId) {
                $sasaranId = DB::table('sasarans')->insertGetId([
                    'deskripsi' => $sasaran,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($indikators as $indikator) {
                DB::table('indikators')->updateOrInsert(
                    ['sasaran_id' => $sasaranId, 'nama_indikator' => $indikator],
                    ['updated_at' => $now, 'created_at' => $now]
                );
            }
        }

        Schema::table('detail_kegiatans', function (Blueprint $table) {
            $table->foreignId('indikator_id')
                ->nullable()
                ->after('sasaran_id')
                ->constrained('indikators')
                ->nullOnDelete();
        });

        if (Schema::hasColumn('detail_kegiatans', 'indikator_kinerja')) {
            DB::table('detail_kegiatans')
                ->join('indikators', 'detail_kegiatans.indikator_kinerja', '=', 'indikators.nama_indikator')
                ->whereNull('detail_kegiatans.indikator_id')
                ->update(['detail_kegiatans.indikator_id' => DB::raw('indikators.id')]);

            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->dropColumn('indikator_kinerja');
            });
        }
    }

    public function down(): void
    {
        Schema::table('detail_kegiatans', function (Blueprint $table) {
            $table->text('indikator_kinerja')->nullable()->after('tujuan');
        });

        DB::table('detail_kegiatans')
            ->leftJoin('indikators', 'detail_kegiatans.indikator_id', '=', 'indikators.id')
            ->whereNotNull('detail_kegiatans.indikator_id')
            ->update(['detail_kegiatans.indikator_kinerja' => DB::raw('indikators.nama_indikator')]);

        Schema::table('detail_kegiatans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('indikator_id');
        });

        Schema::dropIfExists('indikators');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ERD Sync Migrasi 8/12: Tabel `pks_numbers`
 * - Tambah kolom `nomor_pihak_kampus` VARCHAR(100) nullable
 * - Tambah kolom `nomor_pihak_mitra`  VARCHAR(100) nullable
 * - Migrasi data dari kolom `number` → `nomor_pihak_kampus`
 * - Hapus kolom `number` (dan `sort_order` yang tidak ada di ERD)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pks_numbers', function (Blueprint $table) {
            // Tambah dua kolom baru sesuai ERD
            $table->string('nomor_pihak_kampus', 100)->after('cooperation_id')->nullable();
            $table->string('nomor_pihak_mitra', 100)->after('nomor_pihak_kampus')->nullable();
        });

        // Migrasi data: pindahkan nilai `number` ke `nomor_pihak_kampus`
        if (Schema::hasColumn('pks_numbers', 'number')) {
            DB::table('pks_numbers')
                ->whereNotNull('number')
                ->where('number', '<>', '')
                ->update([
                    'nomor_pihak_kampus' => DB::raw('`number`'),
                ]);

            // Drop kolom `number` (dan `sort_order`) setelah data dipindahkan
            Schema::table('pks_numbers', function (Blueprint $table) {
                // Drop unique index pada number jika ada
                try {
                    $table->dropUnique('pks_numbers_number_unique');
                } catch (\Exception $e) {
                    // ignore
                }
                $table->dropColumn('number');
            });
        }

        // Hapus sort_order juga karena tidak ada di ERD
        if (Schema::hasColumn('pks_numbers', 'sort_order')) {
            Schema::table('pks_numbers', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }

    public function down(): void
    {
        // Kembalikan kolom number dan sort_order
        Schema::table('pks_numbers', function (Blueprint $table) {
            $table->string('number')->after('cooperation_id')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->after('number');
        });

        // Pindahkan data kembali dari nomor_pihak_kampus ke number
        DB::table('pks_numbers')
            ->whereNotNull('nomor_pihak_kampus')
            ->update(['number' => DB::raw('`nomor_pihak_kampus`')]);

        Schema::table('pks_numbers', function (Blueprint $table) {
            $table->dropColumn(['nomor_pihak_kampus', 'nomor_pihak_mitra']);
        });
    }
};

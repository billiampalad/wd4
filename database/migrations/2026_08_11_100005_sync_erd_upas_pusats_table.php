<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ERD Sync Migrasi 5/12: Tabel `upas` & `pusats`
 * - Tambah kolom `keterangan` TEXT nullable di kedua tabel
 * - Tambah UNIQUE constraint pada `nama_upa` dan `nama_pusat`
 */
return new class extends Migration
{
    public function up(): void
    {
        // Tabel upas
        Schema::table('upas', function (Blueprint $table) {
            if (! Schema::hasColumn('upas', 'keterangan')) {
                $table->text('keterangan')->after('nama_upa')->nullable();
            }

            try {
                $table->unique('nama_upa');
            } catch (\Exception $e) {
                // UNIQUE sudah ada, abaikan
            }
        });

        // Tabel pusats
        Schema::table('pusats', function (Blueprint $table) {
            if (! Schema::hasColumn('pusats', 'keterangan')) {
                $table->text('keterangan')->after('nama_pusat')->nullable();
            }

            try {
                $table->unique('nama_pusat');
            } catch (\Exception $e) {
                // UNIQUE sudah ada, abaikan
            }
        });
    }

    public function down(): void
    {
        Schema::table('upas', function (Blueprint $table) {
            try {
                $table->dropUnique('upas_nama_upa_unique');
            } catch (\Exception $e) {}
            if (Schema::hasColumn('upas', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });

        Schema::table('pusats', function (Blueprint $table) {
            try {
                $table->dropUnique('pusats_nama_pusat_unique');
            } catch (\Exception $e) {}
            if (Schema::hasColumn('pusats', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};

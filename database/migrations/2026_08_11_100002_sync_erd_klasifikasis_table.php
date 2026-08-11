<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ERD Sync Migrasi 2/12: Tabel `klasifikasis`
 * - Rename tabel `klasifikasi` → `klasifikasis`
 * - Rename FK di `mitras`: `id_klasifikasi` → `klasifikasi_id`
 * - Tambah kolom `keterangan` TEXT nullable
 * - Tambah UNIQUE constraint pada `nama`
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop FK lama di mitras yang mereferensikan klasifikasi
        Schema::table('mitras', function (Blueprint $table) {
            // Drop FK dengan nama konvensi lama
            try {
                $table->dropForeign(['id_klasifikasi']);
            } catch (\Exception $e) {
                // ignore jika FK tidak ada
            }
        });

        // 2. Rename tabel klasifikasi → klasifikasis
        Schema::rename('klasifikasi', 'klasifikasis');

        // 3. Tambah kolom keterangan & UNIQUE di tabel klasifikasis
        Schema::table('klasifikasis', function (Blueprint $table) {
            $table->text('keterangan')->after('nama')->nullable();

            // Tambahkan UNIQUE jika belum ada
            try {
                $table->unique('nama');
            } catch (\Exception $e) {
                // ignore jika sudah ada
            }
        });

        // 4. Rename kolom id_klasifikasi → klasifikasi_id di tabel mitras
        Schema::table('mitras', function (Blueprint $table) {
            $table->renameColumn('id_klasifikasi', 'klasifikasi_id');
        });

        // 5. Tambahkan kembali FK dengan nama baru ke tabel klasifikasis
        Schema::table('mitras', function (Blueprint $table) {
            $table->foreign('klasifikasi_id')
                ->references('id')
                ->on('klasifikasis')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Reverse semua langkah
        Schema::table('mitras', function (Blueprint $table) {
            try {
                $table->dropForeign(['klasifikasi_id']);
            } catch (\Exception $e) {}
        });

        Schema::table('mitras', function (Blueprint $table) {
            $table->renameColumn('klasifikasi_id', 'id_klasifikasi');
        });

        Schema::table('klasifikasis', function (Blueprint $table) {
            try {
                $table->dropUnique('klasifikasis_nama_unique');
            } catch (\Exception $e) {}
            $table->dropColumn('keterangan');
        });

        Schema::rename('klasifikasis', 'klasifikasi');

        Schema::table('mitras', function (Blueprint $table) {
            $table->foreign('id_klasifikasi')
                ->references('id')
                ->on('klasifikasi')
                ->onDelete('set null');
        });
    }
};

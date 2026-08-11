<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ERD Sync Migrasi 9/12: Tabel `jenis_kerjasamas`
 * - Rename `nama_kerjasama` → `nama`
 * - Tambah UNIQUE constraint pada `nama`
 */
return new class extends Migration
{
    public function up(): void
    {
        // Rename kolom nama_kerjasama → nama
        if (Schema::hasColumn('jenis_kerjasamas', 'nama_kerjasama')) {
            Schema::table('jenis_kerjasamas', function (Blueprint $table) {
                $table->renameColumn('nama_kerjasama', 'nama');
            });
        }

        // Tambah UNIQUE constraint pada nama
        Schema::table('jenis_kerjasamas', function (Blueprint $table) {
            try {
                $table->unique('nama');
            } catch (\Exception $e) {
                // Ignore jika sudah ada UNIQUE
            }
        });
    }

    public function down(): void
    {
        Schema::table('jenis_kerjasamas', function (Blueprint $table) {
            try {
                $table->dropUnique('jenis_kerjasamas_nama_unique');
            } catch (\Exception $e) {}
        });

        if (Schema::hasColumn('jenis_kerjasamas', 'nama')) {
            Schema::table('jenis_kerjasamas', function (Blueprint $table) {
                $table->renameColumn('nama', 'nama_kerjasama');
            });
        }
    }
};

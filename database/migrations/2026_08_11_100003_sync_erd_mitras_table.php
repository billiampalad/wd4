<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ERD Sync Migrasi 3/12: Tabel `mitras`
 * - Rename `telp` → `telepon`
 * - Tambah kolom `kota` VARCHAR(100) nullable
 * - Pastikan kolom `status_akses` ENUM sesuai ERD
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            // Rename telp → telepon
            if (Schema::hasColumn('mitras', 'telp')) {
                $table->renameColumn('telp', 'telepon');
            }

            // Tambah kolom kota setelah alamat
            if (! Schema::hasColumn('mitras', 'kota')) {
                $table->string('kota', 100)->after('alamat')->nullable();
            }
        });

        // Pastikan status_akses adalah ENUM yang sesuai ERD
        // Cek apakah kolom status_akses sudah ada
        if (Schema::hasColumn('mitras', 'status_akses')) {
            // Update ENUM values ke sesuai ERD
            DB::statement("ALTER TABLE mitras MODIFY COLUMN status_akses ENUM('Pending', 'Aktif', 'Nonaktif') NOT NULL DEFAULT 'Pending'");
        } else {
            Schema::table('mitras', function (Blueprint $table) {
                $table->enum('status_akses', ['Pending', 'Aktif', 'Nonaktif'])
                    ->default('Pending')
                    ->after('website');
            });
        }

        // Hapus kolom kategori lama jika masih ada (diganti oleh klasifikasi_id)
        if (Schema::hasColumn('mitras', 'kategori')) {
            Schema::table('mitras', function (Blueprint $table) {
                $table->dropColumn('kategori');
            });
        }
    }

    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            if (Schema::hasColumn('mitras', 'telepon')) {
                $table->renameColumn('telepon', 'telp');
            }
            if (Schema::hasColumn('mitras', 'kota')) {
                $table->dropColumn('kota');
            }
        });

        // Kembalikan kategori jika perlu
        if (! Schema::hasColumn('mitras', 'kategori')) {
            Schema::table('mitras', function (Blueprint $table) {
                $table->enum('kategori', ['nasional', 'internasional'])->after('nama_mitra');
            });
        }
    }
};

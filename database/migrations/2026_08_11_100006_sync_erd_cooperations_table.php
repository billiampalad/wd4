<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ERD Sync Migrasi 6/12: Tabel `cooperations`
 * - Rename `title`        → `judul`
 * - Rename `description`  → `ruang_lingkup`
 * - Rename `tipe_pelaksana` → `tingkat` + sesuaikan ENUM
 * - Perbaiki ENUM `jenis` → ('MoU','MoA','IA','SPK')
 * - Perbaiki ENUM `status_dokumen` → ('Draft','Menunggu Evaluasi','Menunggu Validasi','Disahkan','Revisi')
 * - Rename kolom `status` (berlaku) → `status_berlaku` ENUM('Aktif','Akan Berakhir','Kadaluarsa','Diperpanjang')
 * - Tambah kolom `catatan_pimpinan` TEXT nullable
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Rename kolom title → judul (skip jika sudah done)
        if (Schema::hasColumn('cooperations', 'title') && ! Schema::hasColumn('cooperations', 'judul')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->renameColumn('title', 'judul');
            });
        }

        // Step 2: Rename kolom description → ruang_lingkup (skip jika sudah done)
        if (Schema::hasColumn('cooperations', 'description') && ! Schema::hasColumn('cooperations', 'ruang_lingkup')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->renameColumn('description', 'ruang_lingkup');
            });
        }

        // Step 3: Rename tipe_pelaksana → tingkat (skip jika sudah done)
        if (Schema::hasColumn('cooperations', 'tipe_pelaksana') && ! Schema::hasColumn('cooperations', 'tingkat')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->renameColumn('tipe_pelaksana', 'tingkat');
            });
        }

        // Step 4: Perbaiki ENUM `jenis` (hanya jika masih menggunakan nilai panjang)
        $jenisCol = collect(DB::select("SHOW COLUMNS FROM cooperations LIKE 'jenis'"))->first();
        if ($jenisCol && str_contains($jenisCol->Type, 'Memorandum')) {
            // Ubah ke VARCHAR dulu agar update data tanpa truncation error
            DB::statement("ALTER TABLE cooperations MODIFY COLUMN jenis VARCHAR(100) NOT NULL DEFAULT 'MoU'");
            $jenisMap = [
                'MoU (Memorandum of Understanding)' => 'MoU',
                'MoA (Memorandum of Agreement)'     => 'MoA',
                'IA (Implementation Agreement)'     => 'IA',
            ];
            foreach ($jenisMap as $old => $new) {
                DB::table('cooperations')->where('jenis', $old)->update(['jenis' => $new]);
            }
        }
        // Pastikan ENUM sesuai ERD (idempotent)
        DB::statement("ALTER TABLE cooperations MODIFY COLUMN jenis ENUM('MoU','MoA','IA','SPK') NOT NULL DEFAULT 'MoU'");

        // Step 5: Perbaiki ENUM `status_dokumen` (idempotent)
        DB::statement("ALTER TABLE cooperations MODIFY COLUMN status_dokumen ENUM('Draft','Menunggu Evaluasi','Menunggu Validasi','Disahkan','Revisi') NOT NULL DEFAULT 'Draft'");

        // Step 6: Rename `status` → `status_berlaku`
        if (Schema::hasColumn('cooperations', 'status') && ! Schema::hasColumn('cooperations', 'status_berlaku')) {
            // Ubah ke VARCHAR dulu agar update data bisa dilakukan
            DB::statement("ALTER TABLE cooperations MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Aktif'");
            $statusMap = [
                'aktif'              => 'Aktif',
                'dalam perpanjangan' => 'Diperpanjang',
                'kadarluarsa'        => 'Kadaluarsa',
                'tidak aktif'        => 'Kadaluarsa',
                'proses'             => 'Aktif',
            ];
            foreach ($statusMap as $old => $new) {
                DB::table('cooperations')->where('status', $old)->update(['status' => $new]);
            }
            Schema::table('cooperations', function (Blueprint $table) {
                $table->renameColumn('status', 'status_berlaku');
            });
        }
        // Pastikan ENUM status_berlaku sesuai ERD (idempotent)
        DB::statement("ALTER TABLE cooperations MODIFY COLUMN status_berlaku ENUM('Aktif','Akan Berakhir','Kadaluarsa','Diperpanjang') NOT NULL DEFAULT 'Aktif'");

        // Step 7: Perbaiki ENUM `tingkat` sesuai ERD (idempotent)
        if (Schema::hasColumn('cooperations', 'tingkat')) {
            // Normalisasi nilai lama ke nilai ENUM yang valid sebelum ALTER
            $validTingkat = ['Institusi', 'Jurusan', 'Prodi', 'Pusat/UPA'];
            DB::table('cooperations')
                ->whereNotNull('tingkat')
                ->whereNotIn('tingkat', $validTingkat)
                ->update(['tingkat' => null]);
            DB::statement("ALTER TABLE cooperations MODIFY COLUMN tingkat ENUM('Institusi','Jurusan','Prodi','Pusat/UPA') NULL DEFAULT 'Institusi'");
        }

        // Step 8: Tambah kolom catatan_pimpinan jika belum ada
        if (! Schema::hasColumn('cooperations', 'catatan_pimpinan')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->text('catatan_pimpinan')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            if (Schema::hasColumn('cooperations', 'catatan_pimpinan')) {
                $table->dropColumn('catatan_pimpinan');
            }
        });

        if (Schema::hasColumn('cooperations', 'status_berlaku') && ! Schema::hasColumn('cooperations', 'status')) {
            DB::statement("ALTER TABLE cooperations MODIFY COLUMN status_berlaku VARCHAR(50) NOT NULL DEFAULT 'aktif'");
            Schema::table('cooperations', function (Blueprint $table) {
                $table->renameColumn('status_berlaku', 'status');
            });
            DB::statement("ALTER TABLE cooperations MODIFY COLUMN status ENUM('aktif','dalam perpanjangan','kadarluarsa','tidak aktif','proses') NOT NULL DEFAULT 'aktif'");
        }

        if (Schema::hasColumn('cooperations', 'tingkat') && ! Schema::hasColumn('cooperations', 'tipe_pelaksana')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->renameColumn('tingkat', 'tipe_pelaksana');
            });
        }
        if (Schema::hasColumn('cooperations', 'ruang_lingkup') && ! Schema::hasColumn('cooperations', 'description')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->renameColumn('ruang_lingkup', 'description');
            });
        }
        if (Schema::hasColumn('cooperations', 'judul') && ! Schema::hasColumn('cooperations', 'title')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->renameColumn('judul', 'title');
            });
        }
    }
};

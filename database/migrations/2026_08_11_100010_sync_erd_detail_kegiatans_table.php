<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ERD Sync Migrasi 10/12: Tabel `detail_kegiatans`
 * - Drop FK lama `cooperation_id` → cooperations (tidak sesuai ERD)
 * - Tambah kolom `kegiatan_kerjasama_id` FK → kegiatan_kerjasamas (sesuai ERD)
 * - Jadikan `jenis_kerjasama_id` nullable (sesuai ERD)
 * - Rename `keterangan` → `keterangan_luaran`
 * - Rename `income`     → `output`
 * - Tambah kolom `outcome` TEXT nullable (jika belum ada)
 *
 * CATATAN: Data lama di `cooperation_id` (sekarang `kegiatan_kerjasama_id`) merujuk ke
 * cooperations. Karena kegiatan_kerjasamas baru dibuat dan belum ada entri yang terkait,
 * nilai kolom tersebut akan di-set NULL (nullable FK) agar tidak melanggar constraint.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Tangani kolom cooperation_id → kegiatan_kerjasama_id
        if (Schema::hasColumn('detail_kegiatans', 'cooperation_id') &&
            ! Schema::hasColumn('detail_kegiatans', 'kegiatan_kerjasama_id')) {
            // Drop FK lama ke cooperations
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                try {
                    $table->dropForeign(['cooperation_id']);
                } catch (\Exception $e) {
                    // ignore
                }
            });
            // Rename ke kegiatan_kerjasama_id
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->renameColumn('cooperation_id', 'kegiatan_kerjasama_id');
            });
        }

        // Step 2: Set semua nilai kegiatan_kerjasama_id ke NULL (karena data lama merujuk
        // ke cooperations.id bukan kegiatan_kerjasamas.id)
        if (Schema::hasColumn('detail_kegiatans', 'kegiatan_kerjasama_id')) {
            // Jadikan nullable dulu via raw SQL (agar update ke NULL bisa berhasil)
            DB::statement('ALTER TABLE detail_kegiatans MODIFY COLUMN kegiatan_kerjasama_id BIGINT UNSIGNED NULL');

            // Hapus data lama yang tidak valid (FK ke cooperations, bukan kegiatan_kerjasamas)
            DB::table('detail_kegiatans')->update(['kegiatan_kerjasama_id' => null]);

            // Cek apakah FK sudah ada
            $fkExists = collect(DB::select(
                "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'detail_kegiatans'
                   AND CONSTRAINT_NAME = 'detail_kegiatans_kegiatan_kerjasama_id_foreign'"
            ))->isNotEmpty();

            if (! $fkExists) {
                Schema::table('detail_kegiatans', function (Blueprint $table) {
                    $table->foreign('kegiatan_kerjasama_id')
                        ->references('id')
                        ->on('kegiatan_kerjasamas')
                        ->onDelete('cascade');
                });
            }
        } elseif (! Schema::hasColumn('detail_kegiatans', 'kegiatan_kerjasama_id')) {
            // Jika kolom belum ada sama sekali, buat baru sebagai nullable
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->foreignId('kegiatan_kerjasama_id')
                    ->after('id')
                    ->nullable()
                    ->constrained('kegiatan_kerjasamas')
                    ->onDelete('cascade');
            });
        }

        // Step 3: Jadikan jenis_kerjasama_id nullable sesuai ERD
        if (Schema::hasColumn('detail_kegiatans', 'jenis_kerjasama_id')) {
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->unsignedBigInteger('jenis_kerjasama_id')->nullable()->change();
            });
        }

        // Step 4: Rename keterangan → keterangan_luaran
        if (Schema::hasColumn('detail_kegiatans', 'keterangan') &&
            ! Schema::hasColumn('detail_kegiatans', 'keterangan_luaran')) {
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->renameColumn('keterangan', 'keterangan_luaran');
            });
        }

        // Step 5: Rename income → output
        if (Schema::hasColumn('detail_kegiatans', 'income') &&
            ! Schema::hasColumn('detail_kegiatans', 'output')) {
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->renameColumn('income', 'output');
            });
        }

        // Step 6: Tambah kolom outcome jika belum ada
        if (! Schema::hasColumn('detail_kegiatans', 'outcome')) {
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->text('outcome')->nullable();
            });
        }

        // Step 7: Drop kolom yang tidak ada di ERD
        $colsToDrop = [];
        foreach (['nilai_kontrak', 'satuan_luaran', 'tujuan'] as $col) {
            if (Schema::hasColumn('detail_kegiatans', $col)) {
                $colsToDrop[] = $col;
            }
        }
        if (! empty($colsToDrop)) {
            Schema::table('detail_kegiatans', function (Blueprint $table) use ($colsToDrop) {
                $table->dropColumn($colsToDrop);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('detail_kegiatans', 'outcome')) {
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->dropColumn('outcome');
            });
        }
        if (Schema::hasColumn('detail_kegiatans', 'output') &&
            ! Schema::hasColumn('detail_kegiatans', 'income')) {
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->renameColumn('output', 'income');
            });
        }
        if (Schema::hasColumn('detail_kegiatans', 'keterangan_luaran') &&
            ! Schema::hasColumn('detail_kegiatans', 'keterangan')) {
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->renameColumn('keterangan_luaran', 'keterangan');
            });
        }
        if (Schema::hasColumn('detail_kegiatans', 'kegiatan_kerjasama_id')) {
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                try {
                    $table->dropForeign(['kegiatan_kerjasama_id']);
                } catch (\Exception $e) {}
                $table->renameColumn('kegiatan_kerjasama_id', 'cooperation_id');
            });
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                $table->foreign('cooperation_id')
                    ->references('id')
                    ->on('cooperations')
                    ->onDelete('cascade');
            });
        }
    }
};

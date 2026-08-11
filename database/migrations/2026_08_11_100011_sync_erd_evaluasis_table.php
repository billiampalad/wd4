<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ERD Sync Migrasi 11/12: Tabel `evaluasis`
 * - Rename `dinilai_oleh` → `evaluator_id`
 * - Tambah kolom `tipe_evaluasi` ENUM('Internal','Umpan_Balik_Mitra') NOT NULL default 'Internal'
 * - Tambah kolom `score`           DECIMAL(5,2) nullable
 * - Tambah kolom `realisasi_volume` INT nullable
 * - Tambah kolom `realisasi_output` TEXT nullable
 * - Tambah kolom `realisasi_outcome` TEXT nullable
 * - Rename `catatan` → `kendala`
 * - Rename `saran`   → `rekomendasi`
 * - Tambah kolom `kesimpulan` ENUM('Sangat Baik','Baik','Cukup','Perlu Perbaikan') nullable
 * - Kolom 5 tinyInteger LAMA (sesuai_rencana, kualitas, dll) DIPERTAHANKAN
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Rename dinilai_oleh → evaluator_id
        if (Schema::hasColumn('evaluasis', 'dinilai_oleh') &&
            ! Schema::hasColumn('evaluasis', 'evaluator_id')) {
            Schema::table('evaluasis', function (Blueprint $table) {
                $table->renameColumn('dinilai_oleh', 'evaluator_id');
            });
        }

        // Step 2: Rename catatan → kendala
        if (Schema::hasColumn('evaluasis', 'catatan') &&
            ! Schema::hasColumn('evaluasis', 'kendala')) {
            Schema::table('evaluasis', function (Blueprint $table) {
                $table->renameColumn('catatan', 'kendala');
            });
        }

        // Step 3: Rename saran → rekomendasi
        if (Schema::hasColumn('evaluasis', 'saran') &&
            ! Schema::hasColumn('evaluasis', 'rekomendasi')) {
            Schema::table('evaluasis', function (Blueprint $table) {
                $table->renameColumn('saran', 'rekomendasi');
            });
        }

        // Step 4: Tambah kolom-kolom baru sesuai ERD
        Schema::table('evaluasis', function (Blueprint $table) {
            if (! Schema::hasColumn('evaluasis', 'tipe_evaluasi')) {
                $table->enum('tipe_evaluasi', ['Internal', 'Umpan_Balik_Mitra'])
                    ->default('Internal')
                    ->after('evaluator_id');
            }

            if (! Schema::hasColumn('evaluasis', 'score')) {
                $table->decimal('score', 5, 2)->after('tipe_evaluasi')->nullable();
            }

            if (! Schema::hasColumn('evaluasis', 'realisasi_volume')) {
                $table->integer('realisasi_volume')->after('score')->nullable();
            }

            if (! Schema::hasColumn('evaluasis', 'realisasi_output')) {
                $table->text('realisasi_output')->after('realisasi_volume')->nullable();
            }

            if (! Schema::hasColumn('evaluasis', 'realisasi_outcome')) {
                $table->text('realisasi_outcome')->after('realisasi_output')->nullable();
            }

            if (! Schema::hasColumn('evaluasis', 'kesimpulan')) {
                $table->enum('kesimpulan', ['Sangat Baik', 'Baik', 'Cukup', 'Perlu Perbaikan'])
                    ->after('rekomendasi')
                    ->nullable();
            }
        });

        // Step 5: Ubah status_validasi menjadi ENUM sesuai ERD
        if (Schema::hasColumn('evaluasis', 'status_validasi')) {
            // Normalisasi data lama ke nilai ENUM yang valid
            $validStatus = ['Draft', 'Menunggu Validasi', 'Divalidasi', 'Perlu Revisi'];
            // Map nilai lama yang tidak standar ke nilai valid
            $statusMap = [
                'draft'            => 'Draft',
                'menunggu'         => 'Menunggu Validasi',
                'validated'        => 'Divalidasi',
                'divalidasi'       => 'Divalidasi',
                'perlu revisi'     => 'Perlu Revisi',
            ];
            foreach ($statusMap as $old => $new) {
                DB::table('evaluasis')->whereRaw('LOWER(status_validasi) = ?', [$old])->update(['status_validasi' => $new]);
            }
            // Set nilai tidak dikenal ke 'Draft'
            DB::table('evaluasis')
                ->whereNotIn('status_validasi', $validStatus)
                ->update(['status_validasi' => 'Draft']);

            DB::statement("ALTER TABLE evaluasis MODIFY COLUMN status_validasi ENUM('Draft','Menunggu Validasi','Divalidasi','Perlu Revisi') NOT NULL DEFAULT 'Draft'");
        } else {
            Schema::table('evaluasis', function (Blueprint $table) {
                $table->enum('status_validasi', ['Draft', 'Menunggu Validasi', 'Divalidasi', 'Perlu Revisi'])
                    ->default('Draft')
                    ->nullable(false);
            });
        }
    }

    public function down(): void
    {
        Schema::table('evaluasis', function (Blueprint $table) {
            $colsToDrop = [];
            foreach (['tipe_evaluasi', 'score', 'realisasi_volume', 'realisasi_output', 'realisasi_outcome', 'kesimpulan'] as $col) {
                if (Schema::hasColumn('evaluasis', $col)) {
                    $colsToDrop[] = $col;
                }
            }
            if (! empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });

        if (Schema::hasColumn('evaluasis', 'rekomendasi') &&
            ! Schema::hasColumn('evaluasis', 'saran')) {
            Schema::table('evaluasis', function (Blueprint $table) {
                $table->renameColumn('rekomendasi', 'saran');
            });
        }
        if (Schema::hasColumn('evaluasis', 'kendala') &&
            ! Schema::hasColumn('evaluasis', 'catatan')) {
            Schema::table('evaluasis', function (Blueprint $table) {
                $table->renameColumn('kendala', 'catatan');
            });
        }
        if (Schema::hasColumn('evaluasis', 'evaluator_id') &&
            ! Schema::hasColumn('evaluasis', 'dinilai_oleh')) {
            Schema::table('evaluasis', function (Blueprint $table) {
                $table->renameColumn('evaluator_id', 'dinilai_oleh');
            });
        }
    }
};

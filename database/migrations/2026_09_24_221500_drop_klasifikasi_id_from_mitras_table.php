<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('mitras')) {
            // Drop foreign key on klasifikasi_id if exists
            try {
                DB::statement('ALTER TABLE `mitras` DROP FOREIGN KEY `mitras_klasifikasi_id_foreign`');
            } catch (\Throwable $e) {
                // Ignore if not present
            }

            // Drop virtual generated column if present
            if (Schema::hasColumn('mitras', 'id_klasifikasi')) {
                try {
                    DB::statement('ALTER TABLE `mitras` DROP COLUMN `id_klasifikasi`');
                } catch (\Throwable $e) {
                    // Ignore
                }
            }

            // Rename klasifikasi_id to id_klasifikasi if klasifikasi_id exists
            if (Schema::hasColumn('mitras', 'klasifikasi_id')) {
                DB::statement('ALTER TABLE `mitras` CHANGE COLUMN `klasifikasi_id` `id_klasifikasi` BIGINT UNSIGNED NULL DEFAULT NULL');
            } else if (!Schema::hasColumn('mitras', 'id_klasifikasi')) {
                DB::statement('ALTER TABLE `mitras` ADD COLUMN `id_klasifikasi` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `id`');
            }

            // Add foreign key on id_klasifikasi
            try {
                DB::statement('ALTER TABLE `mitras` ADD CONSTRAINT `mitras_id_klasifikasi_foreign` FOREIGN KEY (`id_klasifikasi`) REFERENCES `klasifikasis` (`id`) ON DELETE SET NULL');
            } catch (\Throwable $e) {
                // Foreign key already exists
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mitras')) {
            try {
                DB::statement('ALTER TABLE `mitras` DROP FOREIGN KEY `mitras_id_klasifikasi_foreign`');
            } catch (\Throwable $e) {
                // Ignore
            }

            if (Schema::hasColumn('mitras', 'id_klasifikasi')) {
                DB::statement('ALTER TABLE `mitras` CHANGE COLUMN `id_klasifikasi` `klasifikasi_id` BIGINT UNSIGNED NULL DEFAULT NULL');
                DB::statement('ALTER TABLE `mitras` ADD CONSTRAINT `mitras_klasifikasi_id_foreign` FOREIGN KEY (`klasifikasi_id`) REFERENCES `klasifikasis` (`id`) ON DELETE SET NULL');
            }
        }
    }
};

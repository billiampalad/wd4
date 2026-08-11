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
        DB::statement(<<<SQL
CREATE TABLE `laporan_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_kerja_id` bigint unsigned DEFAULT NULL,
  `jurusan_id` bigint unsigned DEFAULT NULL,
  `upa_id` bigint unsigned DEFAULT NULL,
  `pusat_id` bigint unsigned DEFAULT NULL,
  `cooperation_id` bigint unsigned DEFAULT NULL,
  `uploaded_by` bigint unsigned NOT NULL,
  `uploader_role` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `laporan_files_unit_kerja_id_foreign` (`unit_kerja_id`),
  KEY `laporan_files_uploaded_by_foreign` (`uploaded_by`),
  KEY `laporan_files_cooperation_id_foreign` (`cooperation_id`),
  KEY `laporan_files_jurusan_id_foreign` (`jurusan_id`),
  KEY `laporan_files_upa_id_foreign` (`upa_id`),
  KEY `laporan_files_pusat_id_foreign` (`pusat_id`),
  CONSTRAINT `laporan_files_cooperation_id_foreign` FOREIGN KEY (`cooperation_id`) REFERENCES `cooperations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `laporan_files_jurusan_id_foreign` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `laporan_files_pusat_id_foreign` FOREIGN KEY (`pusat_id`) REFERENCES `pusats` (`id`) ON DELETE SET NULL,
  CONSTRAINT `laporan_files_upa_id_foreign` FOREIGN KEY (`upa_id`) REFERENCES `upas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `laporan_files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_files');
    }
};
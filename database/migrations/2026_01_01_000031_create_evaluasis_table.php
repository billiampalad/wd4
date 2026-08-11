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
CREATE TABLE `evaluasis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cooperation_id` bigint unsigned NOT NULL,
  `evaluator_id` bigint unsigned NOT NULL,
  `tipe_evaluasi` enum('Internal','Umpan_Balik_Mitra') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Internal',
  `score` decimal(5,2) DEFAULT NULL,
  `realisasi_volume` int DEFAULT NULL,
  `realisasi_output` text COLLATE utf8mb4_unicode_ci,
  `realisasi_outcome` text COLLATE utf8mb4_unicode_ci,
  `sesuai_rencana` tinyint DEFAULT NULL,
  `kualitas` tinyint DEFAULT NULL,
  `keterlibatan` tinyint DEFAULT NULL,
  `efisiensi` tinyint DEFAULT NULL,
  `kepuasan` tinyint DEFAULT NULL,
  `kendala` text COLLATE utf8mb4_unicode_ci,
  `ringkasan` text COLLATE utf8mb4_unicode_ci,
  `rekomendasi` text COLLATE utf8mb4_unicode_ci,
  `kesimpulan` enum('Sangat Baik','Baik','Cukup','Perlu Perbaikan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tindak_lanjut` text COLLATE utf8mb4_unicode_ci,
  `status_validasi` enum('Draft','Menunggu Validasi','Divalidasi','Perlu Revisi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evaluasis_cooperation_id_foreign` (`cooperation_id`),
  KEY `evaluasis_dinilai_oleh_foreign` (`evaluator_id`),
  CONSTRAINT `evaluasis_cooperation_id_foreign` FOREIGN KEY (`cooperation_id`) REFERENCES `cooperations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluasis_dinilai_oleh_foreign` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasis');
    }
};
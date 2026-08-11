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
CREATE TABLE `detail_kegiatans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kegiatan_kerjasama_id` bigint unsigned DEFAULT NULL,
  `jenis_kerjasama_id` bigint unsigned DEFAULT NULL,
  `sasaran_id` bigint unsigned DEFAULT NULL,
  `indikator_id` bigint unsigned DEFAULT NULL,
  `income` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `volume_luaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan_luaran` text COLLATE utf8mb4_unicode_ci,
  `output` text COLLATE utf8mb4_unicode_ci,
  `outcome` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_kegiatans_jenis_kerjasama_id_foreign` (`jenis_kerjasama_id`),
  KEY `detail_kegiatans_sasaran_id_foreign` (`sasaran_id`),
  KEY `detail_kegiatans_indikator_id_foreign` (`indikator_id`),
  KEY `detail_kegiatans_kegiatan_kerjasama_id_foreign` (`kegiatan_kerjasama_id`),
  CONSTRAINT `detail_kegiatans_indikator_id_foreign` FOREIGN KEY (`indikator_id`) REFERENCES `indikators` (`id`) ON DELETE SET NULL,
  CONSTRAINT `detail_kegiatans_jenis_kerjasama_id_foreign` FOREIGN KEY (`jenis_kerjasama_id`) REFERENCES `jenis_kerjasamas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_kegiatans_kegiatan_kerjasama_id_foreign` FOREIGN KEY (`kegiatan_kerjasama_id`) REFERENCES `kegiatan_kerjasamas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_kegiatans_sasaran_id_foreign` FOREIGN KEY (`sasaran_id`) REFERENCES `sasarans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kegiatans');
    }
};
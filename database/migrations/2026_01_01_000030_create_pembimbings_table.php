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
CREATE TABLE `pembimbings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kegiatan_mahasiswa_id` bigint unsigned NOT NULL,
  `nama_pembimbing` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('Internal','Eksternal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `kontak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pembimbings_kegiatan_mahasiswa_id_foreign` (`kegiatan_mahasiswa_id`),
  CONSTRAINT `pembimbings_kegiatan_mahasiswa_id_foreign` FOREIGN KEY (`kegiatan_mahasiswa_id`) REFERENCES `kegiatan_mahasiswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembimbings');
    }
};
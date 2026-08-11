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
CREATE TABLE `profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jurusan_id` bigint unsigned DEFAULT NULL,
  `upa_id` bigint unsigned DEFAULT NULL,
  `pusat_id` bigint unsigned DEFAULT NULL,
  `unit_kerja_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profiles_user_id_foreign` (`user_id`),
  KEY `profiles_jurusan_id_foreign` (`jurusan_id`),
  KEY `profiles_unit_kerja_id_foreign` (`unit_kerja_id`),
  KEY `profiles_upa_id_foreign` (`upa_id`),
  KEY `profiles_pusat_id_foreign` (`pusat_id`),
  CONSTRAINT `profiles_jurusan_id_foreign` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `profiles_pusat_id_foreign` FOREIGN KEY (`pusat_id`) REFERENCES `pusats` (`id`) ON DELETE SET NULL,
  CONSTRAINT `profiles_unit_kerja_id_foreign` FOREIGN KEY (`unit_kerja_id`) REFERENCES `unit_kerjas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `profiles_upa_id_foreign` FOREIGN KEY (`upa_id`) REFERENCES `upas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
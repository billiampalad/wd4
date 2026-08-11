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
CREATE TABLE `pks_numbers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cooperation_id` bigint unsigned NOT NULL,
  `nomor_pihak_kampus` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_pihak_mitra` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pks_numbers_cooperation_id_sort_order_index` (`cooperation_id`),
  CONSTRAINT `pks_numbers_cooperation_id_foreign` FOREIGN KEY (`cooperation_id`) REFERENCES `cooperations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pks_numbers');
    }
};
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
CREATE TABLE `cooperations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_cooperation_id` bigint unsigned DEFAULT NULL,
  `mitra_id` bigint unsigned DEFAULT NULL,
  `internal_instansi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Politeknik Negeri Manado',
  `penandatangan_internal_id` bigint unsigned DEFAULT NULL,
  `pj_internal_id` bigint unsigned DEFAULT NULL,
  `penandatangan_mitra_id` bigint unsigned DEFAULT NULL,
  `pj_mitra_id` bigint unsigned DEFAULT NULL,
  `jenis` enum('MoU','MoA','IA','SPK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MoU',
  `doc_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruang_lingkup` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status_berlaku` enum('Aktif','Akan Berakhir','Kadaluarsa','Diperpanjang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `status_dokumen` enum('Draft','Menunggu Evaluasi','Menunggu Validasi','Disahkan','Revisi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `perpanjangan_dari_id` bigint unsigned DEFAULT NULL,
  `pengajuan_kerjasama_baru_id` bigint unsigned DEFAULT NULL,
  `pengajuan_perpanjangan_kerjasama_id` bigint unsigned DEFAULT NULL,
  `tingkat` enum('Institusi','Jurusan','Prodi','Pusat/UPA') COLLATE utf8mb4_unicode_ci DEFAULT 'Institusi',
  `jurusan_id` bigint unsigned DEFAULT NULL,
  `upa_id` bigint unsigned DEFAULT NULL,
  `pusat_id` bigint unsigned DEFAULT NULL,
  `document_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_pimpinan` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `cooperations_mitra_id_foreign` (`mitra_id`),
  KEY `cooperations_penandatangan_internal_id_foreign` (`penandatangan_internal_id`),
  KEY `cooperations_pj_internal_id_foreign` (`pj_internal_id`),
  KEY `cooperations_penandatangan_mitra_id_foreign` (`penandatangan_mitra_id`),
  KEY `cooperations_pj_mitra_id_foreign` (`pj_mitra_id`),
  KEY `cooperations_jurusan_id_foreign` (`jurusan_id`),
  KEY `cooperations_upa_id_foreign` (`upa_id`),
  KEY `cooperations_pusat_id_foreign` (`pusat_id`),
  KEY `cooperations_perpanjangan_dari_id_foreign` (`perpanjangan_dari_id`),
  KEY `cooperations_created_by_foreign` (`created_by`),
  KEY `cooperations_updated_by_foreign` (`updated_by`),
  KEY `cooperations_pengajuan_kerjasama_baru_id_foreign` (`pengajuan_kerjasama_baru_id`),
  KEY `cooperations_pengajuan_perpanjangan_kerjasama_id_foreign` (`pengajuan_perpanjangan_kerjasama_id`),
  KEY `cooperations_parent_cooperation_id_foreign` (`parent_cooperation_id`),
  CONSTRAINT `cooperations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_jurusan_id_foreign` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `mitras` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_parent_cooperation_id_foreign` FOREIGN KEY (`parent_cooperation_id`) REFERENCES `cooperations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cooperations_penandatangan_internal_id_foreign` FOREIGN KEY (`penandatangan_internal_id`) REFERENCES `pejabats` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_penandatangan_mitra_id_foreign` FOREIGN KEY (`penandatangan_mitra_id`) REFERENCES `pejabats` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_pengajuan_kerjasama_baru_id_foreign` FOREIGN KEY (`pengajuan_kerjasama_baru_id`) REFERENCES `pengajuan_kerjasama_baru` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_pengajuan_perpanjangan_kerjasama_id_foreign` FOREIGN KEY (`pengajuan_perpanjangan_kerjasama_id`) REFERENCES `pengajuan_perpanjangan_kerjasama` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_perpanjangan_dari_id_foreign` FOREIGN KEY (`perpanjangan_dari_id`) REFERENCES `cooperations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_pj_internal_id_foreign` FOREIGN KEY (`pj_internal_id`) REFERENCES `pejabats` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_pj_mitra_id_foreign` FOREIGN KEY (`pj_mitra_id`) REFERENCES `pejabats` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_pusat_id_foreign` FOREIGN KEY (`pusat_id`) REFERENCES `pusats` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_upa_id_foreign` FOREIGN KEY (`upa_id`) REFERENCES `upas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cooperations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooperations');
    }
};
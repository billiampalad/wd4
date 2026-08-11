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
CREATE TABLE `pengajuan_kerjasama_baru` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_pengajuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mitra` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_klasifikasi` bigint unsigned DEFAULT NULL,
  `kategori` enum('nasional','internasional') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nasional',
  `negara` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `telp` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_penandatangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan_penandatangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_penanggung_jawab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan_penanggung_jawab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul_pengajuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tujuan_pengajuan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruang_lingkup` text COLLATE utf8mb4_unicode_ci,
  `pesan_tambahan` text COLLATE utf8mb4_unicode_ci,
  `status` enum('diajukan','disetujui','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diajukan',
  `catatan_pimpinan` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `mitra_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengajuan_kerjasama_baru_kode_pengajuan_unique` (`kode_pengajuan`),
  KEY `pengajuan_kerjasama_baru_id_klasifikasi_foreign` (`id_klasifikasi`),
  KEY `pengajuan_kerjasama_baru_reviewed_by_foreign` (`reviewed_by`),
  KEY `pengajuan_kerjasama_baru_mitra_id_foreign` (`mitra_id`),
  CONSTRAINT `pengajuan_kerjasama_baru_id_klasifikasi_foreign` FOREIGN KEY (`id_klasifikasi`) REFERENCES `klasifikasis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pengajuan_kerjasama_baru_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `mitras` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pengajuan_kerjasama_baru_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_kerjasama_baru');
    }
};
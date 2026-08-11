<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ERD Sync Migrasi 7/12: Rename tabel-tabel Pivot
 * - `kerjasama_jurusan` → `cooperation_jurusan`
 * - `kerjasama_prodi`   → `cooperation_prodi`
 * - `kerjasama_upa`     → `cooperation_upa`
 * - `kerjasama_pusat`   → `cooperation_pusat`
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kerjasama_jurusan')) {
            Schema::rename('kerjasama_jurusan', 'cooperation_jurusan');
        }

        if (Schema::hasTable('kerjasama_prodi')) {
            Schema::rename('kerjasama_prodi', 'cooperation_prodi');
        }

        if (Schema::hasTable('kerjasama_upa')) {
            Schema::rename('kerjasama_upa', 'cooperation_upa');
        }

        if (Schema::hasTable('kerjasama_pusat')) {
            Schema::rename('kerjasama_pusat', 'cooperation_pusat');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cooperation_jurusan')) {
            Schema::rename('cooperation_jurusan', 'kerjasama_jurusan');
        }

        if (Schema::hasTable('cooperation_prodi')) {
            Schema::rename('cooperation_prodi', 'kerjasama_prodi');
        }

        if (Schema::hasTable('cooperation_upa')) {
            Schema::rename('cooperation_upa', 'kerjasama_upa');
        }

        if (Schema::hasTable('cooperation_pusat')) {
            Schema::rename('cooperation_pusat', 'kerjasama_pusat');
        }
    }
};

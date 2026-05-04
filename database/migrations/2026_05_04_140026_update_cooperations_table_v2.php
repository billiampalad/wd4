<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan 'proses' ke enum status
        DB::statement("ALTER TABLE cooperations MODIFY COLUMN status ENUM('aktif', 'dalam perpanjangan', 'kadarluarsa', 'tidak aktif', 'proses') NOT NULL DEFAULT 'aktif'");

        // 2. Buat doc_number dan pks_number menjadi unique dengan tetap mengizinkan NULL
        // Catatan: Jika ada data duplikat, migrasi ini akan gagal di SQL level.
        // User perlu membersihkan data duplikat secara manual jika migrasi gagal.
        Schema::table('cooperations', function (Blueprint $table) {
            // Kita gunakan try-catch di level DB atau pastikan data unik sebelum add index
            $table->string('doc_number')->nullable()->change();
            $table->string('pks_number')->nullable()->change();
        });
        
        // Coba tambahkan unique secara terpisah agar lebih mudah didebug
        try {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->unique('doc_number');
            });
        } catch (\Exception $e) {
            dump("Peringatan: Gagal membuat unique index untuk doc_number. Kemungkinan ada data duplikat.");
        }

        try {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->unique('pks_number');
            });
        } catch (\Exception $e) {
            dump("Peringatan: Gagal membuat unique index untuk pks_number. Kemungkinan ada data duplikat.");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            $table->dropUnique(['doc_number']);
            $table->dropUnique(['pks_number']);
        });

        DB::statement("ALTER TABLE cooperations MODIFY COLUMN status ENUM('aktif', 'dalam perpanjangan', 'kadarluarsa', 'tidak aktif') NOT NULL DEFAULT 'aktif'");
    }
};

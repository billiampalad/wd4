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
        // Kembalikan ke enum asli tanpa 'draft'
        DB::statement("ALTER TABLE cooperations MODIFY COLUMN status ENUM('aktif', 'dalam perpanjangan', 'kadarluarsa', 'tidak aktif') NOT NULL DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Jika rollback, tambahkan kembali 'draft'
        DB::statement("ALTER TABLE cooperations MODIFY COLUMN status ENUM('aktif', 'dalam perpanjangan', 'kadarluarsa', 'tidak aktif', 'draft') NOT NULL DEFAULT 'draft'");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE cooperations MODIFY COLUMN status_dokumen ENUM('Draft', 'Menunggu Evaluasi', 'Menunggu Validasi', 'Disahkan', 'Revisi') NOT NULL DEFAULT 'Draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE cooperations MODIFY COLUMN status_dokumen ENUM('Draft', 'Menunggu Evaluasi', 'Disahkan') NOT NULL DEFAULT 'Draft'");
    }
};

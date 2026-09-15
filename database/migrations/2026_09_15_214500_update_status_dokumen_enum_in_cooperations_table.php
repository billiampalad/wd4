<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Temporarily change to VARCHAR to safely update any values
        DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_dokumen` VARCHAR(50) NOT NULL DEFAULT 'Draft'");

        // 2. Normalize existing data: map 'Menunggu Validasi' to 'Menunggu Evaluasi'
        DB::table('cooperations')
            ->whereIn(DB::raw("LOWER(COALESCE(status_dokumen, ''))"), ['menunggu validasi', 'menunggu_validasi'])
            ->update(['status_dokumen' => 'Menunggu Evaluasi']);

        DB::table('cooperations')
            ->whereNotIn('status_dokumen', ['Draft', 'Menunggu Evaluasi', 'Disahkan', 'Revisi'])
            ->orWhereNull('status_dokumen')
            ->update(['status_dokumen' => 'Draft']);

        // 3. Set standardized 4-state ENUM definition
        DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_dokumen` ENUM('Draft', 'Menunggu Evaluasi', 'Disahkan', 'Revisi') NOT NULL DEFAULT 'Draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_dokumen` VARCHAR(50) NOT NULL DEFAULT 'Draft'");
        DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_dokumen` ENUM('Draft', 'Menunggu Evaluasi', 'Menunggu Validasi', 'Disahkan', 'Revisi') NOT NULL DEFAULT 'Draft'");
    }
};

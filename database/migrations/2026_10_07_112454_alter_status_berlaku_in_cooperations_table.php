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
        if (Schema::hasTable('cooperations')) {
            // Ubah tipe kolom status_berlaku dan status_dokumen menjadi VARCHAR(50)
            // agar fleksibel mendukung nilai 'Proses', 'Aktif', 'Dalam Perpanjangan', 'Kadaluarsa', dll.
            DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_berlaku` VARCHAR(50) NULL DEFAULT 'Proses'");
            DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_dokumen` VARCHAR(50) NULL DEFAULT 'Draft'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cooperations')) {
            DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_berlaku` VARCHAR(50) NULL DEFAULT 'Aktif'");
            DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_dokumen` VARCHAR(50) NULL DEFAULT 'Draft'");
        }
    }
};

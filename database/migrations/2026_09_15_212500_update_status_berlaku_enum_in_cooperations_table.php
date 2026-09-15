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
        // 1. Temporarily change to VARCHAR so any value can be stored safely
        DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_berlaku` VARCHAR(50) NOT NULL DEFAULT 'Aktif'");

        // 2. Normalize existing data values
        DB::table('cooperations')
            ->whereIn(DB::raw("LOWER(COALESCE(status_berlaku, ''))"), ['diperpanjang', 'dalam perpanjangan'])
            ->update(['status_berlaku' => 'Dalam Perpanjangan']);

        DB::table('cooperations')
            ->whereIn(DB::raw("LOWER(COALESCE(status_berlaku, ''))"), ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa', 'akan berakhir'])
            ->update(['status_berlaku' => 'Kadaluarsa']);

        DB::table('cooperations')
            ->whereIn(DB::raw("LOWER(COALESCE(status_berlaku, ''))"), ['tidak aktif', 'nonaktif', 'non aktif'])
            ->update(['status_berlaku' => 'Tidak Aktif']);

        DB::table('cooperations')
            ->whereNotIn('status_berlaku', ['Aktif', 'Kadaluarsa', 'Dalam Perpanjangan', 'Tidak Aktif'])
            ->orWhereNull('status_berlaku')
            ->update(['status_berlaku' => 'Aktif']);

        // 3. Set final standardized ENUM definition
        DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_berlaku` ENUM('Aktif', 'Kadaluarsa', 'Dalam Perpanjangan', 'Tidak Aktif') NOT NULL DEFAULT 'Aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_berlaku` VARCHAR(50) NOT NULL DEFAULT 'Aktif'");
        DB::table('cooperations')
            ->where('status_berlaku', 'Dalam Perpanjangan')
            ->update(['status_berlaku' => 'Diperpanjang']);
        DB::statement("ALTER TABLE `cooperations` MODIFY COLUMN `status_berlaku` ENUM('Aktif', 'Akan Berakhir', 'Kadaluarsa', 'Diperpanjang') NOT NULL DEFAULT 'Aktif'");
    }
};

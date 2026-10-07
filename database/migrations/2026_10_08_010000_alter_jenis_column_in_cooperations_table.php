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
        $tables = ['cooperations', 'cooperation', 'kerjasama', 'kerjasamas'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Ubah kolom jenis agar fleksibel mendukung format singkat 'MoU', 'MoA', 'IA', 'SPK' maupun format panjang
                try {
                    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `jenis` VARCHAR(100) NOT NULL DEFAULT 'MoU'");
                } catch (\Throwable $e) {
                    // Abaikan jika bukan MySQL standard
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};

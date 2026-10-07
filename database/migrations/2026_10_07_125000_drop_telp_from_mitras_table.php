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
        $tables = ['mitras', 'mitra'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            if (Schema::hasColumn($tableName, 'telp')) {
                // Pastikan kolom telepon ada
                if (!Schema::hasColumn($tableName, 'telepon')) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->string('telepon', 50)->nullable();
                    });
                }

                // Salin data jika telepon masih kosong
                try {
                    DB::statement("UPDATE `{$tableName}` SET `telepon` = `telp` WHERE (`telepon` IS NULL OR `telepon` = '') AND `telp` IS NOT NULL");
                } catch (\Throwable $e) {
                    // Ignore query error if any
                }

                // Hapus kolom telp
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('telp');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mitras') && !Schema::hasColumn('mitras', 'telp')) {
            Schema::table('mitras', function (Blueprint $table) {
                $table->string('telp', 50)->nullable();
            });
        }
    }
};

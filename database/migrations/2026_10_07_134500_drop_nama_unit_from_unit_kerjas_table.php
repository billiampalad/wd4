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
        $tables = ['unit_kerjas', 'unit_kerja'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // Pastikan kolom nama_unit_pelaksana ada
            if (!Schema::hasColumn($tableName, 'nama_unit_pelaksana')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('nama_unit_pelaksana', 255)->nullable();
                });
            }

            // Salin data dari nama_unit jika nama_unit_pelaksana masih kosong
            if (Schema::hasColumn($tableName, 'nama_unit')) {
                try {
                    DB::statement("UPDATE `{$tableName}` SET `nama_unit_pelaksana` = `nama_unit` WHERE (`nama_unit_pelaksana` IS NULL OR `nama_unit_pelaksana` = '') AND `nama_unit` IS NOT NULL");
                } catch (\Throwable $e) {}

                // Hapus kolom nama_unit
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('nama_unit');
                });
            }

            // Hapus juga nama_unit_kerja jika ada agar bersih
            if (Schema::hasColumn($tableName, 'nama_unit_kerja')) {
                try {
                    DB::statement("UPDATE `{$tableName}` SET `nama_unit_pelaksana` = `nama_unit_kerja` WHERE (`nama_unit_pelaksana` IS NULL OR `nama_unit_pelaksana` = '') AND `nama_unit_kerja` IS NOT NULL");
                } catch (\Throwable $e) {}

                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('nama_unit_kerja');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('unit_kerjas') && !Schema::hasColumn('unit_kerjas', 'nama_unit')) {
            Schema::table('unit_kerjas', function (Blueprint $table) {
                $table->string('nama_unit', 255)->nullable();
            });
        }
    }
};

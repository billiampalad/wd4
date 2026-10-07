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
        $tables = ['jenis_kerjasamas', 'jenis_kerjasama'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // Pastikan kolom 'nama_kerjasama' ada
            if (!Schema::hasColumn($tableName, 'nama_kerjasama')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('nama_kerjasama', 255)->nullable();
                });
            }

            // Salin data dari kolom 'nama' ke 'nama_kerjasama' jika nama_kerjasama kosong
            if (Schema::hasColumn($tableName, 'nama')) {
                try {
                    DB::statement("UPDATE `{$tableName}` SET `nama_kerjasama` = `nama` WHERE (`nama_kerjasama` IS NULL OR `nama_kerjasama` = '') AND `nama` IS NOT NULL");
                } catch (\Throwable $e) {}

                // Hapus kolom 'nama'
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('nama');
                    });
                } catch (\Throwable $e) {
                    // Jika kolom virtual atau index conflict, drop index/kolom via raw statement
                    try {
                        DB::statement("ALTER TABLE `{$tableName}` DROP COLUMN `nama`");
                    } catch (\Throwable $e2) {}
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('jenis_kerjasamas') && !Schema::hasColumn('jenis_kerjasamas', 'nama')) {
            Schema::table('jenis_kerjasamas', function (Blueprint $table) {
                $table->string('nama', 255)->nullable();
            });
        }
    }
};

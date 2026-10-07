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

            // Pastikan kolom 'nama' ada
            if (!Schema::hasColumn($tableName, 'nama')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('nama', 255)->nullable();
                });
            }

            // Pastikan kolom 'nama_kerjasama' ada
            if (!Schema::hasColumn($tableName, 'nama_kerjasama')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('nama_kerjasama', 255)->nullable();
                });
            }

            // Sinkronisasi data dua arah agar nama dan nama_kerjasama selalu terisi
            try {
                DB::statement("UPDATE `{$tableName}` SET `nama_kerjasama` = `nama` WHERE (`nama_kerjasama` IS NULL OR `nama_kerjasama` = '') AND `nama` IS NOT NULL");
                DB::statement("UPDATE `{$tableName}` SET `nama` = `nama_kerjasama` WHERE (`nama` IS NULL OR `nama` = '') AND `nama_kerjasama` IS NOT NULL");
            } catch (\Throwable $e) {}
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

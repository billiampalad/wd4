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
        $tables = ['detail_kegiatans', 'detail_kegiatan'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            if (Schema::hasColumn($tableName, 'keterangan')) {
                if (Schema::hasColumn($tableName, 'keterangan_luaran')) {
                    // Salin data jika keterangan_luaran masih kosong
                    try {
                        DB::statement("UPDATE `{$tableName}` SET `keterangan_luaran` = `keterangan` WHERE (`keterangan_luaran` IS NULL OR `keterangan_luaran` = '') AND `keterangan` IS NOT NULL");
                    } catch (\Throwable $e) {
                        // Ignore query error if any
                    }

                    // Hapus kolom keterangan lama
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('keterangan');
                    });
                } else {
                    // Rename keterangan ke keterangan_luaran
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->renameColumn('keterangan', 'keterangan_luaran');
                    });
                }
            } elseif (!Schema::hasColumn($tableName, 'keterangan_luaran')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->text('keterangan_luaran')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op to preserve production data
    }
};

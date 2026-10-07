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

            if (Schema::hasColumn($tableName, 'nama')) {
                // Periksa apakah nama_kerjasama adalah virtual generated column
                $columns = DB::select("SHOW FULL COLUMNS FROM `{$tableName}`");
                $isVirtual = false;
                $hasNamaKerjasama = false;

                foreach ($columns as $col) {
                    if ($col->Field === 'nama_kerjasama') {
                        $hasNamaKerjasama = true;
                        if (str_contains(strtoupper((string)($col->Extra ?? '')), 'VIRTUAL') || str_contains(strtoupper((string)($col->Extra ?? '')), 'GENERATED')) {
                            $isVirtual = true;
                        }
                    }
                }

                if ($isVirtual) {
                    // Jika virtual column: salin data nama ke kolom sementara, drop kolom virtual & nama, lalu ubah kolom sementara jadi nama_kerjasama
                    try {
                        DB::statement("ALTER TABLE `{$tableName}` ADD COLUMN `tmp_nama_kerjasama` VARCHAR(255) NULL AFTER `id`");
                        DB::statement("UPDATE `{$tableName}` SET `tmp_nama_kerjasama` = `nama`");
                        DB::statement("ALTER TABLE `{$tableName}` DROP COLUMN `nama_kerjasama`");
                        DB::statement("ALTER TABLE `{$tableName}` DROP COLUMN `nama`");
                        DB::statement("ALTER TABLE `{$tableName}` CHANGE COLUMN `tmp_nama_kerjasama` `nama_kerjasama` VARCHAR(255) NULL");
                    } catch (\Throwable $e) {
                        // Fallback jika alter manual gagal
                    }
                } else {
                    // Jika bukan virtual column
                    if (!$hasNamaKerjasama) {
                        Schema::table($tableName, function (Blueprint $table) {
                            $table->string('nama_kerjasama', 255)->nullable();
                        });
                    }

                    try {
                        DB::statement("UPDATE `{$tableName}` SET `nama_kerjasama` = `nama` WHERE (`nama_kerjasama` IS NULL OR `nama_kerjasama` = '') AND `nama` IS NOT NULL");
                    } catch (\Throwable $e) {}

                    try {
                        Schema::table($tableName, function (Blueprint $table) {
                            $table->dropColumn('nama');
                        });
                    } catch (\Throwable $e) {
                        try {
                            DB::statement("ALTER TABLE `{$tableName}` DROP COLUMN `nama`");
                        } catch (\Throwable $e2) {}
                    }
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

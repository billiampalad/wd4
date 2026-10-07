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
        $tables = ['cooperations', 'cooperation', 'kerjasama', 'kerjasamas'];

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // 1. Drop Foreign Keys jika ada
            $foreignKeysToDrop = [
                'upa_id' => "{$tableName}_upa_id_foreign",
                'pusat_id' => "{$tableName}_pusat_id_foreign",
                'pengajuan_kerjasama_mitra_id' => "{$tableName}_pengajuan_kerjasama_mitra_id_foreign",
            ];

            foreach ($foreignKeysToDrop as $col => $fkName) {
                if (Schema::hasColumn($tableName, $col)) {
                    try {
                        Schema::table($tableName, function (Blueprint $table) use ($col, $fkName) {
                            $table->dropForeign([$col]);
                        });
                    } catch (\Throwable $e) {
                        try {
                            DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$fkName}`");
                        } catch (\Throwable $ex) {
                            // Abaikan jika FK tidak ada / sudah terdrop
                        }
                    }
                }
            }

            // 2. Drop Kolom yang diminta
            $columnsToDrop = [
                'title',
                'description',
                'status',
                'pengajuan_kerjasama_mitra_id',
                'tipe_pelaksana',
                'upa_id',
                'pusat_id',
            ];

            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columnsToDrop) {
                foreach ($columnsToDrop as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cooperations')) {
            Schema::table('cooperations', function (Blueprint $table) {
                if (!Schema::hasColumn('cooperations', 'upa_id')) {
                    $table->unsignedBigInteger('upa_id')->nullable();
                }
                if (!Schema::hasColumn('cooperations', 'pusat_id')) {
                    $table->unsignedBigInteger('pusat_id')->nullable();
                }
            });
        }
    }
};

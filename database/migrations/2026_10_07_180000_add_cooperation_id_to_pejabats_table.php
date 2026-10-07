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
        $tables = ['pejabats', 'pejabat'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // 1. Tambah kolom cooperation_id jika belum ada
            if (!Schema::hasColumn($tableName, 'cooperation_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('cooperation_id')->nullable()->after('id');
                });
            }

            // 2. Hubungkan data pejabat yang sudah ada saat ini dengan id kerjasama dari tabel cooperations
            if (Schema::hasTable('cooperations')) {
                try {
                    DB::statement("
                        UPDATE `{$tableName}` p 
                        JOIN `cooperations` c ON p.id = c.penandatangan_internal_id 
                        SET p.cooperation_id = c.id 
                        WHERE p.cooperation_id IS NULL
                    ");
                    DB::statement("
                        UPDATE `{$tableName}` p 
                        JOIN `cooperations` c ON p.id = c.pj_internal_id 
                        SET p.cooperation_id = c.id 
                        WHERE p.cooperation_id IS NULL
                    ");
                    DB::statement("
                        UPDATE `{$tableName}` p 
                        JOIN `cooperations` c ON p.id = c.penandatangan_mitra_id 
                        SET p.cooperation_id = c.id 
                        WHERE p.cooperation_id IS NULL
                    ");
                    DB::statement("
                        UPDATE `{$tableName}` p 
                        JOIN `cooperations` c ON p.id = c.pj_mitra_id 
                        SET p.cooperation_id = c.id 
                        WHERE p.cooperation_id IS NULL
                    ");
                } catch (\Throwable $e) {}

                // 3. Tambahkan foreign key constraint cascade
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->foreign('cooperation_id')
                              ->references('id')
                              ->on('cooperations')
                              ->onDelete('cascade');
                    });
                } catch (\Throwable $e) {}
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pejabats') && Schema::hasColumn('pejabats', 'cooperation_id')) {
            Schema::table('pejabats', function (Blueprint $table) {
                try {
                    $table->dropForeign(['cooperation_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('cooperation_id');
            });
        }
    }
};

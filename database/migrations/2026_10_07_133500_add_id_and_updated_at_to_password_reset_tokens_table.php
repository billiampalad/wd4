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
        $tables = ['password_reset_tokens', 'password_resets'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // 1. Tambahkan kolom 'id' di awal tabel jika belum ada
            if (!Schema::hasColumn($tableName, 'id')) {
                try {
                    // Coba drop primary key lama jika ada (biasanya di email)
                    DB::statement("ALTER TABLE `{$tableName}` DROP PRIMARY KEY");
                } catch (\Throwable $e) {
                    // Ignore jika belum ada primary key
                }

                try {
                    // Tambahkan id sebagai auto-increment primary key di kolom pertama
                    DB::statement("ALTER TABLE `{$tableName}` ADD `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
                } catch (\Throwable $e) {
                    // Fallback jika DB statement ditolak oleh driver database
                    try {
                        Schema::table($tableName, function (Blueprint $table) {
                            $table->id()->first();
                        });
                    } catch (\Throwable $e2) {
                        Schema::table($tableName, function (Blueprint $table) {
                            $table->unsignedBigInteger('id')->nullable()->first();
                        });
                    }
                }
            }

            // 2. Tambahkan kolom 'updated_at' jika belum ada
            if (!Schema::hasColumn($tableName, 'updated_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable();
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

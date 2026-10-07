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
        $tables = ['notifikasis', 'notifikasi'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // 1. Sinkronisasi kolom 'judul' -> 'title'
            if (Schema::hasColumn($tableName, 'judul')) {
                if (Schema::hasColumn($tableName, 'title')) {
                    try {
                        DB::statement("UPDATE `{$tableName}` SET `title` = `judul` WHERE (`title` IS NULL OR `title` = '') AND `judul` IS NOT NULL");
                    } catch (\Throwable $e) {}

                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('judul');
                    });
                } else {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->renameColumn('judul', 'title');
                    });
                }
            } elseif (!Schema::hasColumn($tableName, 'title')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('title', 255)->nullable();
                });
            }

            // 2. Sinkronisasi kolom 'pesan' -> 'message'
            if (Schema::hasColumn($tableName, 'pesan')) {
                if (Schema::hasColumn($tableName, 'message')) {
                    try {
                        DB::statement("UPDATE `{$tableName}` SET `message` = `pesan` WHERE (`message` IS NULL OR `message` = '') AND `pesan` IS NOT NULL");
                    } catch (\Throwable $e) {}

                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('pesan');
                    });
                } else {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->renameColumn('pesan', 'message');
                    });
                }
            } elseif (!Schema::hasColumn($tableName, 'message')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->text('message')->nullable();
                });
            }

            // 3. Sinkronisasi kolom 'link' -> 'url'
            if (Schema::hasColumn($tableName, 'link')) {
                if (Schema::hasColumn($tableName, 'url')) {
                    try {
                        DB::statement("UPDATE `{$tableName}` SET `url` = `link` WHERE (`url` IS NULL OR `url` = '') AND `link` IS NOT NULL");
                    } catch (\Throwable $e) {}

                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('link');
                    });
                } else {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->renameColumn('link', 'url');
                    });
                }
            } elseif (!Schema::hasColumn($tableName, 'url')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('url', 255)->nullable();
                });
            }

            // 4. Sinkronisasi & hapus kolom 'tipe' (digantikan 'type')
            if (Schema::hasColumn($tableName, 'tipe')) {
                if (Schema::hasColumn($tableName, 'type')) {
                    try {
                        DB::statement("UPDATE `{$tableName}` SET `type` = `tipe` WHERE (`type` IS NULL OR `type` = '') AND `tipe` IS NOT NULL");
                    } catch (\Throwable $e) {}
                }
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('tipe');
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

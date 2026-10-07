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

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // 1. Ubah atribut 'title' menjadi 'judul' dan hapus kolom judul yang lama
            if (Schema::hasColumn($tableName, 'title')) {
                if (Schema::hasColumn($tableName, 'judul')) {
                    // Jika kolom judul lama ada, salin data title ke judul bila title terisi, lalu drop kolom title
                    try {
                        DB::statement("UPDATE `{$tableName}` SET `judul` = `title` WHERE `title` IS NOT NULL AND `title` != ''");
                    } catch (\Throwable $e) {
                        // Ignore query error if any
                    }

                    // Atau drop kolom judul lama dan rename title menjadi judul
                    try {
                        Schema::table($tableName, function (Blueprint $table) {
                            $table->dropColumn('judul');
                        });
                        Schema::table($tableName, function (Blueprint $table) {
                            $table->renameColumn('title', 'judul');
                        });
                    } catch (\Throwable $e) {
                        // Fallback jika rename gagal: pastikan title didrop dan judul ada
                        if (Schema::hasColumn($tableName, 'title')) {
                            Schema::table($tableName, function (Blueprint $table) {
                                $table->dropColumn('title');
                            });
                        }
                    }
                } else {
                    // Jika kolom judul belum ada, langsung rename title ke judul
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->renameColumn('title', 'judul');
                    });
                }
            } elseif (!Schema::hasColumn($tableName, 'judul')) {
                // Jika tidak ada title dan tidak ada judul, buat kolom judul
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('judul', 255)->nullable();
                });
            }

            // 2. Ubah atribut 'status' menjadi 'status_berlaku'
            if (Schema::hasColumn($tableName, 'status')) {
                if (Schema::hasColumn($tableName, 'status_berlaku')) {
                    // Salin data status lama ke status_berlaku jika status_berlaku kosong
                    try {
                        DB::statement("UPDATE `{$tableName}` SET `status_berlaku` = `status` WHERE (`status_berlaku` IS NULL OR `status_berlaku` = '') AND `status` IS NOT NULL");
                    } catch (\Throwable $e) {
                        // Ignore query error if any
                    }

                    // Drop kolom status lama
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('status');
                    });
                } else {
                    // Rename status ke status_berlaku
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->renameColumn('status', 'status_berlaku');
                    });
                }
            } elseif (!Schema::hasColumn($tableName, 'status_berlaku')) {
                // Jika belum ada kolom status_berlaku, buat kolomnya
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('status_berlaku', 50)->nullable()->default('Aktif');
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

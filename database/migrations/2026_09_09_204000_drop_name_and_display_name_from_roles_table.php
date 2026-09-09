<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop display_name if exists
        if (Schema::hasColumn('roles', 'display_name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('display_name');
            });
        }

        // 2. Drop virtual generated column role_name if exists
        if (Schema::hasColumn('roles', 'role_name')) {
            DB::statement("ALTER TABLE `roles` DROP COLUMN `role_name`");
        }

        // 3. Rename name to role_name as real column
        if (Schema::hasColumn('roles', 'name') && !Schema::hasColumn('roles', 'role_name')) {
            DB::statement("ALTER TABLE `roles` CHANGE COLUMN `name` `role_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('roles', 'role_name') && !Schema::hasColumn('roles', 'name')) {
            DB::statement("ALTER TABLE `roles` CHANGE COLUMN `role_name` `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL");
        }

        if (!Schema::hasColumn('roles', 'display_name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('display_name', 100)->nullable()->after('name');
            });
        }
    }
};

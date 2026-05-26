<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('profiles', 'upa_id')) {
                $table->foreignId('upa_id')->nullable()->after('unit_kerja_id')->constrained('upas')->nullOnDelete();
            }

            if (!Schema::hasColumn('profiles', 'pusat_id')) {
                $table->foreignId('pusat_id')->nullable()->after('upa_id')->constrained('pusats')->nullOnDelete();
            }
        });

        DB::table('roles')->insertOrIgnore([
            ['role_name' => 'upa'],
            ['role_name' => 'pusat'],
        ]);
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (Schema::hasColumn('profiles', 'pusat_id')) {
                $table->dropForeign(['pusat_id']);
                $table->dropColumn('pusat_id');
            }

            if (Schema::hasColumn('profiles', 'upa_id')) {
                $table->dropForeign(['upa_id']);
                $table->dropColumn('upa_id');
            }
        });
    }
};

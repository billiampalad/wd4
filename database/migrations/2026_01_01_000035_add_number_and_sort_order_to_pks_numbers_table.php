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
        Schema::table('pks_numbers', function (Blueprint $table) {
            if (!Schema::hasColumn('pks_numbers', 'number')) {
                $table->string('number', 255)->nullable()->after('cooperation_id');
            }
            if (!Schema::hasColumn('pks_numbers', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('number');
            }
        });

        // Migrate any existing data
        if (Schema::hasColumn('pks_numbers', 'nomor_pihak_kampus') && Schema::hasColumn('pks_numbers', 'number')) {
            DB::statement("UPDATE pks_numbers SET number = COALESCE(nomor_pihak_kampus, nomor_pihak_mitra) WHERE number IS NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pks_numbers', function (Blueprint $table) {
            if (Schema::hasColumn('pks_numbers', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            if (Schema::hasColumn('pks_numbers', 'number')) {
                $table->dropColumn('number');
            }
        });
    }
};

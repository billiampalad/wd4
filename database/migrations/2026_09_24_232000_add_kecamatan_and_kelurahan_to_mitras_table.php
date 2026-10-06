<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            if (!Schema::hasColumn('mitras', 'kota')) {
                $table->string('kota', 100)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'kecamatan')) {
                $table->string('kecamatan', 100)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'kelurahan')) {
                $table->string('kelurahan', 100)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'provinsi')) {
                $table->string('provinsi', 120)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'country_code')) {
                $table->string('country_code', 2)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'province_code')) {
                $table->string('province_code', 10)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $drop = [];
            foreach (['kecamatan', 'kelurahan'] as $col) {
                if (Schema::hasColumn('mitras', $col)) {
                    $drop[] = $col;
                }
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};

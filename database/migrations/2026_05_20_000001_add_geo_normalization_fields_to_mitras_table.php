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
        $hasProvinsi = Schema::hasColumn('mitras', 'provinsi');
        $hasCountryCode = Schema::hasColumn('mitras', 'country_code');
        $hasProvinceCode = Schema::hasColumn('mitras', 'province_code');

        if ($hasProvinsi && $hasCountryCode && $hasProvinceCode) {
            return;
        }

        Schema::table('mitras', function (Blueprint $table) use ($hasProvinsi, $hasCountryCode, $hasProvinceCode) {
            if (! $hasCountryCode) {
                $table->string('country_code', 2)->nullable()->index();
            }

            if (! $hasProvinsi) {
                $table->string('provinsi', 120)->nullable();
            }

            if (! $hasProvinceCode) {
                $table->string('province_code', 10)->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            if (Schema::hasColumn('mitras', 'province_code')) {
                $table->dropIndex(['mitras_province_code_index']);
                $table->dropColumn('province_code');
            }

            if (Schema::hasColumn('mitras', 'country_code')) {
                $table->dropIndex(['mitras_country_code_index']);
                $table->dropColumn('country_code');
            }

            if (Schema::hasColumn('mitras', 'provinsi')) {
                $table->dropColumn('provinsi');
            }
        });
    }
};

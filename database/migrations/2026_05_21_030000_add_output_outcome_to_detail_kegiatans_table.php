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
        Schema::table('detail_kegiatans', function (Blueprint $table) {
            $table->text('output')->nullable()->after('indikator_kinerja');
            $table->text('outcome')->nullable()->after('output');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_kegiatans', function (Blueprint $table) {
            $table->dropColumn(['output', 'outcome']);
        });
    }
};

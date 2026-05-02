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
        Schema::table('laporan_files', function (Blueprint $table) {
            $table->unsignedBigInteger('cooperation_id')->nullable()->after('unit_kerja_id');
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_files', function (Blueprint $table) {
            $table->dropForeign(['cooperation_id']);
            $table->dropColumn('cooperation_id');
        });
    }
};

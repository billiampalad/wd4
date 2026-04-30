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
        Schema::table('cooperations', function (Blueprint $table) {
            $table->unsignedBigInteger('mitra_id')->nullable()->after('id');
            $table->string('internal_instansi')->default('Politeknik Negeri Manado')->after('mitra_id');
            $table->unsignedBigInteger('penandatangan_internal_id')->nullable()->after('internal_instansi');
            $table->unsignedBigInteger('pj_internal_id')->nullable()->after('penandatangan_internal_id');
            $table->unsignedBigInteger('penandatangan_mitra_id')->nullable()->after('pj_internal_id');
            $table->unsignedBigInteger('pj_mitra_id')->nullable()->after('penandatangan_mitra_id');

            $table->foreign('mitra_id')->references('id')->on('mitras')->onDelete('set null');
            $table->foreign('penandatangan_internal_id')->references('id')->on('pejabats')->onDelete('set null');
            $table->foreign('pj_internal_id')->references('id')->on('pejabats')->onDelete('set null');
            $table->foreign('penandatangan_mitra_id')->references('id')->on('pejabats')->onDelete('set null');
            $table->foreign('pj_mitra_id')->references('id')->on('pejabats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
            $table->dropForeign(['penandatangan_internal_id']);
            $table->dropForeign(['pj_internal_id']);
            $table->dropForeign(['penandatangan_mitra_id']);
            $table->dropForeign(['pj_mitra_id']);
            $table->dropColumn(['mitra_id', 'internal_instansi', 'penandatangan_internal_id', 'pj_internal_id', 'penandatangan_mitra_id', 'pj_mitra_id']);
        });
    }
};

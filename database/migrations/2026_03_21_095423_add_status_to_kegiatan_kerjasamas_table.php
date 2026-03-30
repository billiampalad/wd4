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
        Schema::table('kegiatan_kerjasamas', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('penanggung_jawab');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan_kerjasamas', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

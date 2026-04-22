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
            $table->string('jenis_dokumen')->nullable()->after('nama_kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan_kerjasamas', function (Blueprint $table) {
            $table->dropColumn('jenis_dokumen');
        });
    }
};

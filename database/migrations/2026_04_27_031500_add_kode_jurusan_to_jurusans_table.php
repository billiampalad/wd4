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
        Schema::table('jurusans', function (Blueprint $table) {
            $table->string('kode_jurusan', 20)->unique()->nullable()->after('id');
            // nama_jurusan sudah ada, ubah panjangnya menjadi 150
            $table->string('nama_jurusan', 150)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurusans', function (Blueprint $table) {
            $table->dropColumn('kode_jurusan');
            $table->string('nama_jurusan', 255)->change();
        });
    }
};

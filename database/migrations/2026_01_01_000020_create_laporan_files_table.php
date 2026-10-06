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
        if (!Schema::hasTable('laporan_files')) {
            Schema::create('laporan_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->string('file_path', 255);
            $table->string('nama_file', 255)->nullable();
            $table->string('tipe', 50)->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('laporan_files', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_files', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'file_path')) {
                $table->string('file_path', 255);
            }
            if (!Schema::hasColumn('laporan_files', 'nama_file')) {
                $table->string('nama_file', 255)->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'tipe')) {
                $table->string('tipe', 50)->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_files');
    }
};

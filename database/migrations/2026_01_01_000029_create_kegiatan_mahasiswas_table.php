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
        if (!Schema::hasTable('kegiatan_mahasiswas')) {
            Schema::create('kegiatan_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->unsignedBigInteger('mahasiswa_id')->nullable();
            $table->unsignedBigInteger('detail_kegiatan_id')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('kegiatan_mahasiswas', function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'kegiatan_kerjasama_id')) {
                $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'mahasiswa_id')) {
                $table->unsignedBigInteger('mahasiswa_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'detail_kegiatan_id')) {
                $table->unsignedBigInteger('detail_kegiatan_id')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_mahasiswas');
    }
};

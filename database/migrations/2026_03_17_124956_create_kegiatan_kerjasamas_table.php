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
        Schema::create('kegiatan_kerjasamas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->foreignId('id_jenis')->constrained('jenis_kerjasamas');
            $table->foreignId('id_jurusan')->nullable()->constrained('jurusans'); // SEKARANG AMAN
            $table->foreignId('id_unit')->nullable()->constrained('unit_kerjas'); // SEKARANG AMAN
            $table->foreignId('created_by')->constrained('users');
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->string('nomor_mou')->nullable();
            $table->date('tanggal_mou')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_kerjasamas');
    }
};

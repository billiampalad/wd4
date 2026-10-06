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
        Schema::create('detail_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('jenis_kerjasama_id')->nullable();
            $table->unsignedBigInteger('sasaran_id')->nullable();
            $table->unsignedBigInteger('indikator_id')->nullable();
            $table->string('income', 255)->nullable();
            $table->string('volume_luaran', 255)->nullable();
            $table->string('satuan_luaran', 255)->nullable();
            $table->text('keterangan_luaran')->nullable();
            $table->text('output')->nullable();
            $table->text('outcome')->nullable();
            $table->timestamps();

            $table->foreign('kegiatan_kerjasama_id')->references('id')->on('kegiatan_kerjasamas')->onDelete('cascade');
            $table->foreign('jenis_kerjasama_id')->references('id')->on('jenis_kerjasamas')->onDelete('cascade');
            $table->foreign('sasaran_id')->references('id')->on('sasarans')->onDelete('set null');
            $table->foreign('indikator_id')->references('id')->on('indikators')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kegiatans');
    }
};
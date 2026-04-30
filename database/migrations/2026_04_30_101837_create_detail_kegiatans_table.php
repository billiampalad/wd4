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
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('jenis_kerjasama_id');
            $table->unsignedBigInteger('sasaran_id')->nullable();
            $table->decimal('nilai_kontrak', 15, 2)->default(0);
            $table->string('income')->nullable();
            $table->integer('volume_luaran')->nullable();
            $table->string('satuan_luaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('tujuan')->nullable();
            $table->text('indikator_kinerja')->nullable();
            $table->timestamps();

            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            $table->foreign('jenis_kerjasama_id')->references('id')->on('jenis_kerjasamas')->onDelete('cascade');
            $table->foreign('sasaran_id')->references('id')->on('sasarans')->onDelete('set null');
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

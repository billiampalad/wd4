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
        Schema::create('pelaksanaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kegiatan')->constrained('kegiatan_kerjasamas')->cascadeOnDelete();
            $table->text('deskripsi');
            $table->text('cakupan')->nullable();
            $table->integer('jumlah_peserta')->nullable();
            $table->text('sumber_daya')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaksanaans');
    }
};

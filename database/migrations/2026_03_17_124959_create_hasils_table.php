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
        Schema::create('hasils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kegiatan')->constrained('kegiatan_kerjasamas')->cascadeOnDelete();
            $table->text('hasil_langsung')->nullable();
            $table->text('dampak')->nullable();
            $table->text('manfaat_mahasiswa')->nullable();
            $table->text('manfaat_polimdo')->nullable();
            $table->text('manfaat_mitra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasils');
    }
};

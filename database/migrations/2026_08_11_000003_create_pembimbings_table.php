<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembimbings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_mahasiswa_id')->constrained('kegiatan_mahasiswas')->onDelete('cascade');
            $table->string('nama_pembimbing');
            $table->enum('tipe', ['Internal', 'Eksternal']);
            $table->string('kontak')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembimbings');
    }
};

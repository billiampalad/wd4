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
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique();
            $table->string('nama');
            $table->foreignId('prodi_id')->constrained('prodis')->onDelete('cascade');
            $table->year('angkatan');
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->enum('status', ['Aktif', 'Lulus', 'Cuti', 'DO'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};

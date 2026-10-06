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
        if (!Schema::hasTable('prodis')) {
            Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->string('kode_prodi', 20)->nullable();
            $table->string('nama_prodi', 150);
            $table->enum('jenjang', ['D3', 'D4', 'S1', 'S2', 'Profesi'])->default('D4');
            $table->timestamps();
            });
        } else {
            Schema::table('prodis', function (Blueprint $table) {
            if (!Schema::hasColumn('prodis', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id')->nullable();
            }
            if (!Schema::hasColumn('prodis', 'kode_prodi')) {
                $table->string('kode_prodi', 20)->nullable();
            }
            if (!Schema::hasColumn('prodis', 'nama_prodi')) {
                $table->string('nama_prodi', 150);
            }
            if (!Schema::hasColumn('prodis', 'jenjang')) {
                $table->enum('jenjang', ['D3', 'D4', 'S1', 'S2', 'Profesi'])->default('D4');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};

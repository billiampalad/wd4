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
        if (!Schema::hasTable('jurusans')) {
            Schema::create('jurusans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jurusan', 20)->nullable()->unique();
            $table->string('nama_jurusan', 150)->unique();
            $table->timestamps();
            });
        } else {
            Schema::table('jurusans', function (Blueprint $table) {
            if (!Schema::hasColumn('jurusans', 'kode_jurusan')) {
                $table->string('kode_jurusan', 20)->nullable()->unique();
            }
            if (!Schema::hasColumn('jurusans', 'nama_jurusan')) {
                $table->string('nama_jurusan', 150)->unique();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurusans');
    }
};

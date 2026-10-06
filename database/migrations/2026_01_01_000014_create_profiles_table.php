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
        if (!Schema::hasTable('profiles')) {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('jabatan', 255)->nullable();
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->unsignedBigInteger('upa_id')->nullable();
            $table->unsignedBigInteger('pusat_id')->nullable();
            $table->unsignedBigInteger('unit_kerja_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jurusan_id')->references('id')->on('jurusans')->onDelete('set null');
            $table->foreign('prodi_id')->references('id')->on('prodis')->onDelete('set null');
            $table->foreign('upa_id')->references('id')->on('upas')->onDelete('set null');
            $table->foreign('pusat_id')->references('id')->on('pusats')->onDelete('set null');
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerjas')->onDelete('set null');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
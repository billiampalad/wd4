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
        Schema::create('kerjasama_jurusan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('jurusan_id');
            $table->timestamps();

            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            $table->foreign('jurusan_id')->references('id')->on('jurusans')->onDelete('cascade');
        });

        Schema::create('kerjasama_upa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('upa_id');
            $table->timestamps();

            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            $table->foreign('upa_id')->references('id')->on('upas')->onDelete('cascade');
        });

        Schema::create('kerjasama_pusat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('pusat_id');
            $table->timestamps();

            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            $table->foreign('pusat_id')->references('id')->on('pusats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjasama_jurusan');
        Schema::dropIfExists('kerjasama_upa');
        Schema::dropIfExists('kerjasama_pusat');
    }
};

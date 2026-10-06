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
        if (!Schema::hasTable('kerjasama_upa')) {
        Schema::create('kerjasama_upa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('upa_id');
            $table->timestamps();

            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            $table->foreign('upa_id')->references('id')->on('upas')->onDelete('cascade');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjasama_upa');
    }
};
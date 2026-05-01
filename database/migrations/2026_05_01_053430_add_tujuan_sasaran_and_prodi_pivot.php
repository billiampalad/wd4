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
        Schema::table('cooperations', function (Blueprint $table) {
            $table->text('tujuan')->nullable();
            $table->text('sasaran')->nullable();
        });

        Schema::create('kerjasama_prodi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('prodi_id');
            $table->timestamps();

            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            $table->foreign('prodi_id')->references('id')->on('prodis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            $table->dropColumn(['tujuan', 'sasaran']);
        });
        Schema::dropIfExists('kerjasama_prodi');
    }
};

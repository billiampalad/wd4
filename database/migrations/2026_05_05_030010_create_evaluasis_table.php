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
        Schema::create('evaluasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperation_id')->constrained('cooperations')->onDelete('cascade');
            $table->foreignId('dinilai_oleh')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('sesuai_rencana')->nullable();
            $table->tinyInteger('kualitas')->nullable();
            $table->tinyInteger('keterlibatan')->nullable();
            $table->tinyInteger('efisiensi')->nullable();
            $table->tinyInteger('kepuasan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasis');
    }
};

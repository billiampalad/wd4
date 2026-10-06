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
        if (!Schema::hasTable('sasarans')) {
            Schema::create('sasarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sasaran', 255);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('sasarans', function (Blueprint $table) {
            if (!Schema::hasColumn('sasarans', 'nama_sasaran')) {
                $table->string('nama_sasaran', 255);
            }
            if (!Schema::hasColumn('sasarans', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sasarans');
    }
};

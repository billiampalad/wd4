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
        if (!Schema::hasTable('alumnis')) {
            Schema::create('alumnis', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 50);
            $table->string('nama', 255);
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->integer('tahun_lulus')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('alumnis', function (Blueprint $table) {
            if (!Schema::hasColumn('alumnis', 'nim')) {
                $table->string('nim', 50);
            }
            if (!Schema::hasColumn('alumnis', 'nama')) {
                $table->string('nama', 255);
            }
            if (!Schema::hasColumn('alumnis', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'tahun_lulus')) {
                $table->integer('tahun_lulus')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};

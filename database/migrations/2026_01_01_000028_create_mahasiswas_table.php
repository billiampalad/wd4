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
        if (!Schema::hasTable('mahasiswas')) {
            Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 50)->unique();
            $table->string('nama', 255);
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('mahasiswas', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'nim')) {
                $table->string('nim', 50)->unique();
            }
            if (!Schema::hasColumn('mahasiswas', 'nama')) {
                $table->string('nama', 255);
            }
            if (!Schema::hasColumn('mahasiswas', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};

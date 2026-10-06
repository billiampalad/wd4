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
        if (!Schema::hasTable('indikators')) {
            Schema::create('indikators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sasaran_id')->nullable();
            $table->string('nama_indikator', 255);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('indikators', function (Blueprint $table) {
            if (!Schema::hasColumn('indikators', 'sasaran_id')) {
                $table->unsignedBigInteger('sasaran_id')->nullable();
            }
            if (!Schema::hasColumn('indikators', 'nama_indikator')) {
                $table->string('nama_indikator', 255);
            }
            if (!Schema::hasColumn('indikators', 'deskripsi')) {
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
        Schema::dropIfExists('indikators');
    }
};

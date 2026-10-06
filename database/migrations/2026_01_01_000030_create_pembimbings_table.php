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
        if (!Schema::hasTable('pembimbings')) {
            Schema::create('pembimbings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->unsignedBigInteger('pejabat_id')->nullable();
            $table->string('peran', 255)->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('pembimbings', function (Blueprint $table) {
            if (!Schema::hasColumn('pembimbings', 'kegiatan_kerjasama_id')) {
                $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'pejabat_id')) {
                $table->unsignedBigInteger('pejabat_id')->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'peran')) {
                $table->string('peran', 255)->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembimbings');
    }
};

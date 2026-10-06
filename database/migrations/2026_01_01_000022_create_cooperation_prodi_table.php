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
        if (!Schema::hasTable('kerjasama_prodi')) {
            Schema::create('kerjasama_prodi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('prodi_id');
            $table->timestamps();
            });
        } else {
            Schema::table('kerjasama_prodi', function (Blueprint $table) {
            if (!Schema::hasColumn('kerjasama_prodi', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id');
            }
            if (!Schema::hasColumn('kerjasama_prodi', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjasama_prodi');
    }
};

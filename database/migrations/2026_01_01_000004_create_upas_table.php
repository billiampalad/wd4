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
        if (!Schema::hasTable('upas')) {
            Schema::create('upas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_upa', 150)->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('upas', function (Blueprint $table) {
            if (!Schema::hasColumn('upas', 'nama_upa')) {
                $table->string('nama_upa', 150)->unique();
            }
            if (!Schema::hasColumn('upas', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upas');
    }
};

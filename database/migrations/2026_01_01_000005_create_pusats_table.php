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
        if (!Schema::hasTable('pusats')) {
            Schema::create('pusats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pusat', 150)->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('pusats', function (Blueprint $table) {
            if (!Schema::hasColumn('pusats', 'nama_pusat')) {
                $table->string('nama_pusat', 150)->unique();
            }
            if (!Schema::hasColumn('pusats', 'keterangan')) {
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
        Schema::dropIfExists('pusats');
    }
};

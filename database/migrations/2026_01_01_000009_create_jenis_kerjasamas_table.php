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
        if (!Schema::hasTable('jenis_kerjasamas')) {
            Schema::create('jenis_kerjasamas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150)->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('jenis_kerjasamas', function (Blueprint $table) {
            if (!Schema::hasColumn('jenis_kerjasamas', 'nama')) {
                $table->string('nama', 150)->unique();
            }
            if (!Schema::hasColumn('jenis_kerjasamas', 'keterangan')) {
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
        Schema::dropIfExists('jenis_kerjasamas');
    }
};

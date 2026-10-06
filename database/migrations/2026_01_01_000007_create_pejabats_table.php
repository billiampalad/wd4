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
        if (!Schema::hasTable('pejabats')) {
            Schema::create('pejabats', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('nip', 50)->nullable();
            $table->string('jabatan', 150)->nullable();
            $table->boolean('is_internal')->default(true);
            $table->string('instansi', 255)->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('pejabats', function (Blueprint $table) {
            if (!Schema::hasColumn('pejabats', 'nama')) {
                $table->string('nama', 150);
            }
            if (!Schema::hasColumn('pejabats', 'nip')) {
                $table->string('nip', 50)->nullable();
            }
            if (!Schema::hasColumn('pejabats', 'jabatan')) {
                $table->string('jabatan', 150)->nullable();
            }
            if (!Schema::hasColumn('pejabats', 'is_internal')) {
                $table->boolean('is_internal')->default(true);
            }
            if (!Schema::hasColumn('pejabats', 'instansi')) {
                $table->string('instansi', 255)->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pejabats');
    }
};

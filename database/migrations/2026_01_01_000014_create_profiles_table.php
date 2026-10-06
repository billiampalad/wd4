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
        if (!Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('jabatan', 255)->nullable();
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->unsignedBigInteger('upa_id')->nullable();
            $table->unsignedBigInteger('pusat_id')->nullable();
            $table->unsignedBigInteger('unit_kerja_id')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('profiles', 'user_id')) {
                $table->unsignedBigInteger('user_id');
            }
            if (!Schema::hasColumn('profiles', 'jabatan')) {
                $table->string('jabatan', 255)->nullable();
            }
            if (!Schema::hasColumn('profiles', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'upa_id')) {
                $table->unsignedBigInteger('upa_id')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'pusat_id')) {
                $table->unsignedBigInteger('pusat_id')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'unit_kerja_id')) {
                $table->unsignedBigInteger('unit_kerja_id')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};

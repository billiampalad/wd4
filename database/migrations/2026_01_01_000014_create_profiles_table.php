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
        $table = 'profiles';
        $oldNames = array (
  0 => 'profile',
);

        // 1. Rename tabel lama jika tabel lama ditemukan dan tabel baru belum ada
        foreach ($oldNames as $old) {
            if (Schema::hasTable($old) && !Schema::hasTable($table)) {
                Schema::rename($old, $table);
                break;
            }
        }

        // 2. Buat tabel jika belum ada
        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $table) {
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
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('profiles', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
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

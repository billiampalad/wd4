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
        $table = 'alumnis';
        $oldNames = array (
  0 => 'alumni',
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
            $table->string('nim', 50);
            $table->string('nama', 255);
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->integer('tahun_lulus')->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('alumnis', 'nim')) {
                $table->string('nim', 50)->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'nama')) {
                $table->string('nama', 255)->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'tahun_lulus')) {
                $table->integer('tahun_lulus')->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'email')) {
                $table->string('email', 150)->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'telepon')) {
                $table->string('telepon', 50)->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};

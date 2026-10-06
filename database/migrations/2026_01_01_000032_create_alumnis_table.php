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
            $table->string('nim', 255);
            $table->string('nama', 255);
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->integer('tahun_lulus')->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telepon', 255)->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('alumnis', 'nim')) {
                $table->string('nim', 255)->nullable();
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
                $table->string('email', 255)->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'telepon')) {
                $table->string('telepon', 255)->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'created_at')) {
                $table->timestamps();
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

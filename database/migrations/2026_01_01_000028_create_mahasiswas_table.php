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
        $table = 'mahasiswas';
        $oldNames = array (
  0 => 'mahasiswa',
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
            $table->integer('angkatan')->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telepon', 255)->nullable();
            $table->string('status', 50)->nullable()->default('Aktif');
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'nim')) {
                $table->string('nim', 255)->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'nama')) {
                $table->string('nama', 255)->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'angkatan')) {
                $table->integer('angkatan')->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'email')) {
                $table->string('email', 255)->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'telepon')) {
                $table->string('telepon', 255)->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'status')) {
                $table->string('status', 50)->nullable()->default('Aktif');
            }
            if (!Schema::hasColumn('mahasiswas', 'created_at')) {
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
        Schema::dropIfExists('mahasiswas');
    }
};

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
            $table->string('nim', 50)->unique();
            $table->string('nama', 255);
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'nim')) {
                $table->string('nim', 50)->unique();
            }
            if (!Schema::hasColumn('mahasiswas', 'nama')) {
                $table->string('nama', 255);
            }
            if (!Schema::hasColumn('mahasiswas', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable();
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

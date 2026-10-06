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
        $table = 'prodis';
        $oldNames = array (
  0 => 'prodi',
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
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->string('kode_prodi', 20)->nullable();
            $table->string('nama_prodi', 150);
            $table->enum('jenjang', ['D3', 'D4', 'S1', 'S2', 'Profesi'])->default('D4');
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('prodis', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id')->nullable();
            }
            if (!Schema::hasColumn('prodis', 'kode_prodi')) {
                $table->string('kode_prodi', 20)->nullable();
            }
            if (!Schema::hasColumn('prodis', 'nama_prodi')) {
                $table->string('nama_prodi', 150)->nullable();
            }
            if (!Schema::hasColumn('prodis', 'jenjang')) {
                $table->enum('jenjang', ['D3', 'D4', 'S1', 'S2', 'Profesi'])->default('D4');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};

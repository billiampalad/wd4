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
        $table = 'jurusans';
        $oldNames = array (
  0 => 'jurusan',
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
            $table->string('kode_jurusan', 20)->nullable();
            $table->string('nama_jurusan', 150);
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('jurusans', 'kode_jurusan')) {
                $table->string('kode_jurusan', 20)->nullable();
            }
            if (!Schema::hasColumn('jurusans', 'nama_jurusan')) {
                $table->string('nama_jurusan', 150)->nullable();
            }
            if (!Schema::hasColumn('jurusans', 'created_at')) {
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
        Schema::dropIfExists('jurusans');
    }
};

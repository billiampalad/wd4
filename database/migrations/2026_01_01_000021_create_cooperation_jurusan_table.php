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
        $table = 'kerjasama_jurusan';
        $oldNames = array (
  0 => 'cooperation_jurusan',
  1 => 'kerjasama_jurusans',
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
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('jurusan_id');
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('kerjasama_jurusan', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id');
            }
            if (!Schema::hasColumn('kerjasama_jurusan', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjasama_jurusan');
    }
};

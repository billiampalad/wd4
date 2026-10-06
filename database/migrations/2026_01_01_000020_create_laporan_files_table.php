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
        $table = 'laporan_files';
        $oldNames = array (
  0 => 'laporan_file',
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
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->string('file_path', 255);
            $table->string('nama_file', 255)->nullable();
            $table->string('tipe', 50)->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_files', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'file_path')) {
                $table->string('file_path', 255);
            }
            if (!Schema::hasColumn('laporan_files', 'nama_file')) {
                $table->string('nama_file', 255)->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'tipe')) {
                $table->string('tipe', 50)->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_files');
    }
};

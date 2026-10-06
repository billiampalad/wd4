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
        $table = 'unit_kerjas';
        $oldNames = array (
  0 => 'unit_kerja',
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
            $table->string('nama_unit', 150)->nullable();
            $table->string('nama_unit_pelaksana', 150)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('unit_kerjas', 'nama_unit')) {
                $table->string('nama_unit', 150)->nullable();
            }
            if (!Schema::hasColumn('unit_kerjas', 'nama_unit_pelaksana')) {
                $table->string('nama_unit_pelaksana', 150)->nullable();
            }
            if (!Schema::hasColumn('unit_kerjas', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_kerjas');
    }
};

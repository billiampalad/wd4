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
        $table = 'tujuans';
        $oldNames = array (
  0 => 'tujuan',
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
            $table->unsignedBigInteger('id_kegiatan')->nullable();
            $table->text('tujuan')->nullable();
            $table->text('sasaran')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('tujuans', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('tujuans', 'tujuan')) {
                $table->text('tujuan')->nullable();
            }
            if (!Schema::hasColumn('tujuans', 'sasaran')) {
                $table->text('sasaran')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tujuans');
    }
};

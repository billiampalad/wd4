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
        $table = 'indikators';
        $oldNames = array (
  0 => 'indikator',
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
            $table->unsignedBigInteger('sasaran_id')->nullable();
            $table->string('nama_indikator', 255);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('indikators', 'sasaran_id')) {
                $table->unsignedBigInteger('sasaran_id')->nullable();
            }
            if (!Schema::hasColumn('indikators', 'nama_indikator')) {
                $table->string('nama_indikator', 255)->nullable();
            }
            if (!Schema::hasColumn('indikators', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikators');
    }
};

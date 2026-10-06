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
        $table = 'kegiatan_kerjasamas';
        $oldNames = array (
  0 => 'kegiatan_kerjasama',
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
            $table->string('nama_kegiatan', 255);
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->enum('status', ['Perencanaan', 'Berjalan', 'Selesai'])->default('Perencanaan');
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'nama_kegiatan')) {
                $table->string('nama_kegiatan', 255);
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'periode_mulai')) {
                $table->date('periode_mulai')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'periode_selesai')) {
                $table->date('periode_selesai')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'status')) {
                $table->enum('status', ['Perencanaan', 'Berjalan', 'Selesai'])->default('Perencanaan');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_kerjasamas');
    }
};

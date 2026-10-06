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
        $table = 'kegiatan_mahasiswas';
        $oldNames = array (
  0 => 'kegiatan_mahasiswa',
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
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->unsignedBigInteger('kegiatan_id')->nullable();
            $table->unsignedBigInteger('mahasiswa_id')->nullable();
            $table->unsignedBigInteger('detail_kegiatan_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->string('status', 50)->nullable();
            $table->decimal('nilai_mitra', 5, 2)->nullable();
            $table->text('catatan_mitra')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'kegiatan_kerjasama_id')) {
                $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'kegiatan_id')) {
                $table->unsignedBigInteger('kegiatan_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'mahasiswa_id')) {
                $table->unsignedBigInteger('mahasiswa_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'detail_kegiatan_id')) {
                $table->unsignedBigInteger('detail_kegiatan_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'periode_mulai')) {
                $table->date('periode_mulai')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'periode_selesai')) {
                $table->date('periode_selesai')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'status')) {
                $table->string('status', 50)->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'nilai_mitra')) {
                $table->decimal('nilai_mitra', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'catatan_mitra')) {
                $table->text('catatan_mitra')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_mahasiswas');
    }
};

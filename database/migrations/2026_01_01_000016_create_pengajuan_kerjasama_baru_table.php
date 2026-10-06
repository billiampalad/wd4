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
        $table = 'pengajuan_kerjasama_baru';
        $oldNames = array (
  0 => 'pengajuan_kerjasama_barus',
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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->string('nama_mitra', 255)->nullable();
            $table->enum('jenis', ['MoU', 'MoA', 'IA', 'SPK'])->default('MoU');
            $table->string('judul', 255);
            $table->text('ruang_lingkup')->nullable();
            $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'])->default('Draft');
            $table->text('catatan')->nullable();
            $table->string('file_draft', 255)->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'nama_mitra')) {
                $table->string('nama_mitra', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'jenis')) {
                $table->enum('jenis', ['MoU', 'MoA', 'IA', 'SPK'])->default('MoU');
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'judul')) {
                $table->string('judul', 255);
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'ruang_lingkup')) {
                $table->text('ruang_lingkup')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'status')) {
                $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'])->default('Draft');
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'catatan')) {
                $table->text('catatan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'file_draft')) {
                $table->string('file_draft', 255)->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_kerjasama_baru');
    }
};

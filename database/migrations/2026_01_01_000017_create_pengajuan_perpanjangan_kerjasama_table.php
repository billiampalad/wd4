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
        $table = 'pengajuan_perpanjangan_kerjasama';
        $oldNames = array (
  0 => 'pengajuan_perpanjangan_kerjasamas',
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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('alasan_perpanjangan')->nullable();
            $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'])->default('Draft');
            $table->text('catatan')->nullable();
            $table->string('file_pendukung', 255)->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'alasan_perpanjangan')) {
                $table->text('alasan_perpanjangan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'status')) {
                $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'])->default('Draft');
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'catatan')) {
                $table->text('catatan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'file_pendukung')) {
                $table->string('file_pendukung', 255)->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_perpanjangan_kerjasama');
    }
};

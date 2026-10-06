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
        if (!Schema::hasTable('pengajuan_kerjasama_baru')) {
            Schema::create('pengajuan_kerjasama_baru', function (Blueprint $table) {
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
            Schema::table('pengajuan_kerjasama_baru', function (Blueprint $table) {
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

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
            $table->string('kode_pengajuan', 255)->nullable();
            $table->string('nama_mitra', 255)->nullable();
            $table->unsignedBigInteger('id_klasifikasi')->nullable();
            $table->string('kategori', 100)->nullable()->default('nasional');
            $table->string('negara', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->string('telp', 50)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('nama_penandatangan', 255)->nullable();
            $table->string('jabatan_penandatangan', 255)->nullable();
            $table->string('nama_penanggung_jawab', 255)->nullable();
            $table->string('jabatan_penanggung_jawab', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('judul_pengajuan', 255)->nullable();
            $table->text('tujuan_pengajuan')->nullable();
            $table->text('ruang_lingkup')->nullable();
            $table->text('pesan_tambahan')->nullable();
            $table->string('status', 50)->nullable()->default('diajukan');
            $table->text('catatan_pimpinan')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('jenis', 50)->nullable();
            $table->string('judul', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->string('file_draft', 255)->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'kode_pengajuan')) {
                $table->string('kode_pengajuan', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'nama_mitra')) {
                $table->string('nama_mitra', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'id_klasifikasi')) {
                $table->unsignedBigInteger('id_klasifikasi')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'kategori')) {
                $table->string('kategori', 100)->nullable()->default('nasional');
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'negara')) {
                $table->string('negara', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'alamat')) {
                $table->text('alamat')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'telp')) {
                $table->string('telp', 50)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'website')) {
                $table->string('website', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'nama_penandatangan')) {
                $table->string('nama_penandatangan', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'jabatan_penandatangan')) {
                $table->string('jabatan_penandatangan', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'nama_penanggung_jawab')) {
                $table->string('nama_penanggung_jawab', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'jabatan_penanggung_jawab')) {
                $table->string('jabatan_penanggung_jawab', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'email')) {
                $table->string('email', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'judul_pengajuan')) {
                $table->string('judul_pengajuan', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'tujuan_pengajuan')) {
                $table->text('tujuan_pengajuan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'ruang_lingkup')) {
                $table->text('ruang_lingkup')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'pesan_tambahan')) {
                $table->text('pesan_tambahan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'status')) {
                $table->string('status', 50)->nullable()->default('diajukan');
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'catatan_pimpinan')) {
                $table->text('catatan_pimpinan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'jenis')) {
                $table->string('jenis', 50)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'judul')) {
                $table->string('judul', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'catatan')) {
                $table->text('catatan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'file_draft')) {
                $table->string('file_draft', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'created_at')) {
                $table->timestamps();
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

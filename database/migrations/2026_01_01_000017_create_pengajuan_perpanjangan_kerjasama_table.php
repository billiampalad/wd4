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
            $table->string('kode_pengajuan', 100)->nullable();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->string('nama_mitra', 255)->nullable();
            $table->unsignedBigInteger('id_klasifikasi')->nullable();
            $table->string('kategori', 100)->nullable();
            $table->string('negara', 100)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('telp', 50)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('nama_penandatangan', 150)->nullable();
            $table->string('jabatan_penandatangan', 150)->nullable();
            $table->string('nama_penanggung_jawab', 150)->nullable();
            $table->string('jabatan_penanggung_jawab', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('jenis', 50)->nullable();
            $table->string('doc_number', 255)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('file_surat', 255)->nullable();
            $table->string('judul_pengajuan', 255)->nullable();
            $table->text('tujuan_pengajuan')->nullable();
            $table->text('ruang_lingkup')->nullable();
            $table->text('alasan_perpanjangan')->nullable();
            $table->text('pesan_tambahan')->nullable();
            $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'])->default('Draft');
            $table->text('catatan')->nullable();
            $table->text('catatan_pimpinan')->nullable();
            $table->string('file_pendukung', 255)->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'kode_pengajuan')) {
                $table->string('kode_pengajuan', 100)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'nama_mitra')) {
                $table->string('nama_mitra', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'id_klasifikasi')) {
                $table->unsignedBigInteger('id_klasifikasi')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'kategori')) {
                $table->string('kategori', 100)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'negara')) {
                $table->string('negara', 100)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'alamat')) {
                $table->string('alamat', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'telp')) {
                $table->string('telp', 50)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'website')) {
                $table->string('website', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'nama_penandatangan')) {
                $table->string('nama_penandatangan', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'jabatan_penandatangan')) {
                $table->string('jabatan_penandatangan', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'nama_penanggung_jawab')) {
                $table->string('nama_penanggung_jawab', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'jabatan_penanggung_jawab')) {
                $table->string('jabatan_penanggung_jawab', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'email')) {
                $table->string('email', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'jenis')) {
                $table->string('jenis', 50)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'doc_number')) {
                $table->string('doc_number', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'file_surat')) {
                $table->string('file_surat', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'judul_pengajuan')) {
                $table->string('judul_pengajuan', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'tujuan_pengajuan')) {
                $table->text('tujuan_pengajuan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'ruang_lingkup')) {
                $table->text('ruang_lingkup')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'alasan_perpanjangan')) {
                $table->text('alasan_perpanjangan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'pesan_tambahan')) {
                $table->text('pesan_tambahan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'status')) {
                $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'])->default('Draft');
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'catatan')) {
                $table->text('catatan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'catatan_pimpinan')) {
                $table->text('catatan_pimpinan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'file_pendukung')) {
                $table->string('file_pendukung', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable();
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

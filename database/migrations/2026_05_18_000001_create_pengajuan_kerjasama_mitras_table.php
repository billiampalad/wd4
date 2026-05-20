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
        Schema::create('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan')->unique();
            $table->string('nama_mitra');
            $table->foreignId('id_klasifikasi')->nullable()->constrained('klasifikasi')->nullOnDelete();
            $table->enum('kategori', ['nasional', 'internasional']);
            $table->string('negara')->nullable();
            $table->text('alamat');
            $table->string('telp', 30);
            $table->string('website')->nullable();
            $table->string('nama_pengaju');
            $table->string('jabatan_pengaju')->nullable();
            $table->string('email_pengaju');
            $table->string('telepon_pengaju', 30);
            $table->string('judul_pengajuan');
            $table->text('tujuan_pengajuan');
            $table->text('ruang_lingkup')->nullable();
            $table->text('pesan_tambahan')->nullable();
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->text('catatan_pimpinan')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('mitra_id')->nullable()->constrained('mitras')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_kerjasama_mitras');
    }
};

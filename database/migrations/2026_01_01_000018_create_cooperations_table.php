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
        if (!Schema::hasTable('cooperations')) {
        Schema::create('cooperations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_cooperation_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->string('internal_instansi', 255)->default('Politeknik Negeri Manado');
            $table->unsignedBigInteger('penandatangan_internal_id')->nullable();
            $table->unsignedBigInteger('pj_internal_id')->nullable();
            $table->unsignedBigInteger('penandatangan_mitra_id')->nullable();
            $table->unsignedBigInteger('pj_mitra_id')->nullable();
            $table->enum('jenis', ['MoU', 'MoA', 'IA', 'SPK'])->default('MoU');
            $table->string('doc_number', 255)->nullable();
            $table->string('judul', 255);
            $table->text('ruang_lingkup')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status_berlaku', ['Aktif', 'Kadaluarsa', 'Dalam Perpanjangan', 'Tidak Aktif'])->default('Aktif');
            $table->enum('status_dokumen', ['Draft', 'Menunggu Evaluasi', 'Disahkan', 'Revisi'])->default('Draft');
            $table->unsignedBigInteger('perpanjangan_dari_id')->nullable();
            $table->unsignedBigInteger('pengajuan_kerjasama_baru_id')->nullable();
            $table->unsignedBigInteger('pengajuan_perpanjangan_kerjasama_id')->nullable();
            $table->enum('tingkat', ['Institusi', 'Jurusan', 'Prodi', 'Pusat/UPA'])->default('Institusi');
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->unsignedBigInteger('upa_id')->nullable();
            $table->unsignedBigInteger('pusat_id')->nullable();
            $table->string('document_link', 255)->nullable();
            $table->text('catatan_pimpinan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('jurusan_id')->references('id')->on('jurusans')->onDelete('set null');
            $table->foreign('mitra_id')->references('id')->on('mitras')->onDelete('set null');
            $table->foreign('parent_cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            $table->foreign('penandatangan_internal_id')->references('id')->on('pejabats')->onDelete('set null');
            $table->foreign('penandatangan_mitra_id')->references('id')->on('pejabats')->onDelete('set null');
            $table->foreign('pengajuan_kerjasama_baru_id')->references('id')->on('pengajuan_kerjasama_baru')->onDelete('set null');
            $table->foreign('pengajuan_perpanjangan_kerjasama_id')->references('id')->on('pengajuan_perpanjangan_kerjasama')->onDelete('set null');
            $table->foreign('perpanjangan_dari_id')->references('id')->on('cooperations')->onDelete('set null');
            $table->foreign('pj_internal_id')->references('id')->on('pejabats')->onDelete('set null');
            $table->foreign('pj_mitra_id')->references('id')->on('pejabats')->onDelete('set null');
            $table->foreign('pusat_id')->references('id')->on('pusats')->onDelete('set null');
            $table->foreign('upa_id')->references('id')->on('upas')->onDelete('set null');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooperations');
    }
};
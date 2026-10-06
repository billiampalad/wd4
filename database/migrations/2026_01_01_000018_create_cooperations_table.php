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
            });
        } else {
            Schema::table('cooperations', function (Blueprint $table) {
            if (!Schema::hasColumn('cooperations', 'parent_cooperation_id')) {
                $table->unsignedBigInteger('parent_cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'internal_instansi')) {
                $table->string('internal_instansi', 255)->default('Politeknik Negeri Manado');
            }
            if (!Schema::hasColumn('cooperations', 'penandatangan_internal_id')) {
                $table->unsignedBigInteger('penandatangan_internal_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'pj_internal_id')) {
                $table->unsignedBigInteger('pj_internal_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'penandatangan_mitra_id')) {
                $table->unsignedBigInteger('penandatangan_mitra_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'pj_mitra_id')) {
                $table->unsignedBigInteger('pj_mitra_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'jenis')) {
                $table->enum('jenis', ['MoU', 'MoA', 'IA', 'SPK'])->default('MoU');
            }
            if (!Schema::hasColumn('cooperations', 'doc_number')) {
                $table->string('doc_number', 255)->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'judul')) {
                $table->string('judul', 255);
            }
            if (!Schema::hasColumn('cooperations', 'ruang_lingkup')) {
                $table->text('ruang_lingkup')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'status_berlaku')) {
                $table->enum('status_berlaku', ['Aktif', 'Kadaluarsa', 'Dalam Perpanjangan', 'Tidak Aktif'])->default('Aktif');
            }
            if (!Schema::hasColumn('cooperations', 'status_dokumen')) {
                $table->enum('status_dokumen', ['Draft', 'Menunggu Evaluasi', 'Disahkan', 'Revisi'])->default('Draft');
            }
            if (!Schema::hasColumn('cooperations', 'perpanjangan_dari_id')) {
                $table->unsignedBigInteger('perpanjangan_dari_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'pengajuan_kerjasama_baru_id')) {
                $table->unsignedBigInteger('pengajuan_kerjasama_baru_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'pengajuan_perpanjangan_kerjasama_id')) {
                $table->unsignedBigInteger('pengajuan_perpanjangan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'tingkat')) {
                $table->enum('tingkat', ['Institusi', 'Jurusan', 'Prodi', 'Pusat/UPA'])->default('Institusi');
            }
            if (!Schema::hasColumn('cooperations', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'upa_id')) {
                $table->unsignedBigInteger('upa_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'pusat_id')) {
                $table->unsignedBigInteger('pusat_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'document_link')) {
                $table->string('document_link', 255)->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'catatan_pimpinan')) {
                $table->text('catatan_pimpinan')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
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

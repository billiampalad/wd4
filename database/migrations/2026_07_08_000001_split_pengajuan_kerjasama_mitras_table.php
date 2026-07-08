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
        // 1. Drop foreign key constraint on cooperations table
        Schema::table('cooperations', function (Blueprint $table) {
            if (Schema::hasColumn('cooperations', 'pengajuan_kerjasama_mitra_id')) {
                $table->dropForeign(['pengajuan_kerjasama_mitra_id']);
                $table->dropColumn('pengajuan_kerjasama_mitra_id');
            }
        });

        // 2. Drop the old table if it exists
        Schema::dropIfExists('pengajuan_kerjasama_mitras');

        // 3. Create pengajuan_kerjasama_baru table
        Schema::create('pengajuan_kerjasama_baru', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan')->unique();
            $table->string('nama_mitra');
            $table->foreignId('id_klasifikasi')->nullable()->constrained('klasifikasi')->nullOnDelete();
            $table->enum('kategori', ['nasional', 'internasional'])->default('nasional');
            $table->string('negara')->nullable();
            $table->text('alamat');
            $table->string('telp', 30);
            $table->string('website')->nullable();
            $table->string('nama_penandatangan');
            $table->string('jabatan_penandatangan')->nullable();
            $table->string('nama_penanggung_jawab')->nullable();
            $table->string('jabatan_penanggung_jawab')->nullable();
            $table->string('email');
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

        // 4. Create pengajuan_perpanjangan_kerjasama table
        Schema::create('pengajuan_perpanjangan_kerjasama', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan')->unique();
            $table->foreignId('mitra_id')->constrained('mitras')->onDelete('cascade');
            $table->string('nama_mitra');
            $table->foreignId('id_klasifikasi')->nullable()->constrained('klasifikasi')->nullOnDelete();
            $table->enum('kategori', ['nasional', 'internasional'])->default('nasional');
            $table->string('negara')->nullable();
            $table->text('alamat');
            $table->string('telp', 30);
            $table->string('website')->nullable();
            $table->string('nama_penandatangan');
            $table->string('jabatan_penandatangan')->nullable();
            $table->string('nama_penanggung_jawab')->nullable();
            $table->string('jabatan_penanggung_jawab')->nullable();
            $table->string('email');
            $table->string('jenis')->nullable(); // MoU, MoA, IA
            $table->string('doc_number'); // Nomor Dokumen Lama
            $table->date('start_date');
            $table->date('end_date');
            $table->string('file_surat');
            $table->string('judul_pengajuan');
            $table->text('tujuan_pengajuan');
            $table->text('ruang_lingkup')->nullable();
            $table->text('pesan_tambahan')->nullable();
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->text('catatan_pimpinan')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        // 5. Add new foreign key columns to cooperations table
        Schema::table('cooperations', function (Blueprint $table) {
            $table->foreignId('pengajuan_kerjasama_baru_id')
                ->nullable()
                ->after('perpanjangan_dari_id')
                ->constrained('pengajuan_kerjasama_baru')
                ->nullOnDelete();

            $table->foreignId('pengajuan_perpanjangan_kerjasama_id')
                ->nullable()
                ->after('pengajuan_kerjasama_baru_id')
                ->constrained('pengajuan_perpanjangan_kerjasama')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new foreign keys and columns
        Schema::table('cooperations', function (Blueprint $table) {
            if (Schema::hasColumn('cooperations', 'pengajuan_perpanjangan_kerjasama_id')) {
                $table->dropForeign(['pengajuan_perpanjangan_kerjasama_id']);
                $table->dropColumn('pengajuan_perpanjangan_kerjasama_id');
            }
            if (Schema::hasColumn('cooperations', 'pengajuan_kerjasama_baru_id')) {
                $table->dropForeign(['pengajuan_kerjasama_baru_id']);
                $table->dropColumn('pengajuan_kerjasama_baru_id');
            }
        });

        // Drop the two new tables
        Schema::dropIfExists('pengajuan_perpanjangan_kerjasama');
        Schema::dropIfExists('pengajuan_kerjasama_baru');

        // Recreate the old table structure
        Schema::create('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan')->unique();
            $table->string('nama_mitra');
            $table->foreignId('id_klasifikasi')->nullable()->constrained('klasifikasi')->nullOnDelete();
            $table->enum('kategori', ['nasional', 'internasional'])->default('nasional');
            $table->string('negara')->nullable();
            $table->text('alamat');
            $table->string('telp', 30);
            $table->string('website')->nullable();
            $table->string('nama_penandatangan');
            $table->string('jabatan_penandatangan')->nullable();
            $table->string('nama_penanggung_jawab')->nullable();
            $table->string('jabatan_penanggung_jawab')->nullable();
            $table->string('email');
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
            $table->string('jenis')->nullable();
            $table->string('doc_number')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('file_surat')->nullable();
            $table->timestamps();
        });

        // Re-add the old foreign key column to cooperations
        Schema::table('cooperations', function (Blueprint $table) {
            $table->foreignId('pengajuan_kerjasama_mitra_id')
                ->nullable()
                ->after('perpanjangan_dari_id')
                ->constrained('pengajuan_kerjasama_mitras')
                ->nullOnDelete();
        });
    }
};

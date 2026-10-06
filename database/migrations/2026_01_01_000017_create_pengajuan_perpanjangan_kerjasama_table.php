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
        if (!Schema::hasTable('pengajuan_perpanjangan_kerjasama')) {
            Schema::create('pengajuan_perpanjangan_kerjasama', function (Blueprint $table) {
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
            Schema::table('pengajuan_perpanjangan_kerjasama', function (Blueprint $table) {
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

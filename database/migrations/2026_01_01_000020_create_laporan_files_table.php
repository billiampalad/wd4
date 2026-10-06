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
        $table = 'laporan_files';
        $oldNames = array (
  0 => 'laporan_file',
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
            $table->unsignedBigInteger('unit_kerja_id')->nullable();
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->unsignedBigInteger('upa_id')->nullable();
            $table->unsignedBigInteger('pusat_id')->nullable();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->string('uploader_role', 30)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_files', 'unit_kerja_id')) {
                $table->unsignedBigInteger('unit_kerja_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'upa_id')) {
                $table->unsignedBigInteger('upa_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'pusat_id')) {
                $table->unsignedBigInteger('pusat_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'uploaded_by')) {
                $table->unsignedBigInteger('uploaded_by')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'uploader_role')) {
                $table->string('uploader_role', 30)->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'file_path')) {
                $table->string('file_path', 255)->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'original_name')) {
                $table->string('original_name', 255)->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->default(0);
            }
            if (!Schema::hasColumn('laporan_files', 'created_at')) {
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
        Schema::dropIfExists('laporan_files');
    }
};

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
        $table = 'detail_kegiatans';
        $oldNames = array (
  0 => 'detail_kegiatan',
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
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('jenis_kerjasama_id')->nullable();
            $table->unsignedBigInteger('sasaran_id')->nullable();
            $table->unsignedBigInteger('indikator_id')->nullable();
            $table->string('income', 255)->nullable();
            $table->string('volume_luaran', 255)->nullable();
            $table->string('satuan_luaran', 100)->nullable();
            $table->text('keterangan_luaran')->nullable();
            $table->text('output')->nullable();
            $table->text('outcome')->nullable();
            $table->string('nilai_kontrak', 255)->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('detail_kegiatans', 'kegiatan_kerjasama_id')) {
                $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'jenis_kerjasama_id')) {
                $table->unsignedBigInteger('jenis_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'sasaran_id')) {
                $table->unsignedBigInteger('sasaran_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'indikator_id')) {
                $table->unsignedBigInteger('indikator_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'income')) {
                $table->string('income', 255)->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'volume_luaran')) {
                $table->string('volume_luaran', 255)->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'satuan_luaran')) {
                $table->string('satuan_luaran', 100)->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'keterangan_luaran')) {
                $table->text('keterangan_luaran')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'output')) {
                $table->text('output')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'outcome')) {
                $table->text('outcome')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'nilai_kontrak')) {
                $table->string('nilai_kontrak', 255)->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'created_at')) {
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
        Schema::dropIfExists('detail_kegiatans');
    }
};

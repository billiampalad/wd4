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
        $table = 'hasils';
        $oldNames = array (
  0 => 'hasil',
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
            $table->unsignedBigInteger('id_kegiatan')->nullable();
            $table->text('hasil_langsung')->nullable();
            $table->text('dampak')->nullable();
            $table->text('manfaat_mahasiswa')->nullable();
            $table->text('manfaat_polimdo')->nullable();
            $table->text('manfaat_mitra')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('hasils', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('hasils', 'hasil_langsung')) {
                $table->text('hasil_langsung')->nullable();
            }
            if (!Schema::hasColumn('hasils', 'dampak')) {
                $table->text('dampak')->nullable();
            }
            if (!Schema::hasColumn('hasils', 'manfaat_mahasiswa')) {
                $table->text('manfaat_mahasiswa')->nullable();
            }
            if (!Schema::hasColumn('hasils', 'manfaat_polimdo')) {
                $table->text('manfaat_polimdo')->nullable();
            }
            if (!Schema::hasColumn('hasils', 'manfaat_mitra')) {
                $table->text('manfaat_mitra')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasils');
    }
};

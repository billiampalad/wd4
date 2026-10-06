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
        $table = 'permasalahan_solusis';
        $oldNames = array (
  0 => 'permasalahan_solusi',
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
            $table->text('kendala')->nullable();
            $table->text('solusi')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('permasalahan_solusis', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('permasalahan_solusis', 'kendala')) {
                $table->text('kendala')->nullable();
            }
            if (!Schema::hasColumn('permasalahan_solusis', 'solusi')) {
                $table->text('solusi')->nullable();
            }
            if (!Schema::hasColumn('permasalahan_solusis', 'rekomendasi')) {
                $table->text('rekomendasi')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permasalahan_solusis');
    }
};

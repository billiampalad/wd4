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
        $table = 'kesimpulans';
        $oldNames = array (
  0 => 'kesimpulan',
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
            $table->text('ringkasan')->nullable();
            $table->text('saran')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('kesimpulans', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('kesimpulans', 'ringkasan')) {
                $table->text('ringkasan')->nullable();
            }
            if (!Schema::hasColumn('kesimpulans', 'saran')) {
                $table->text('saran')->nullable();
            }
            if (!Schema::hasColumn('kesimpulans', 'tindak_lanjut')) {
                $table->text('tindak_lanjut')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kesimpulans');
    }
};

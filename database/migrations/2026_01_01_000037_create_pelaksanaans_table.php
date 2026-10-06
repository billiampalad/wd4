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
        $table = 'pelaksanaans';
        $oldNames = array (
  0 => 'pelaksanaan',
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
            $table->text('deskripsi')->nullable();
            $table->string('cakupan', 255)->nullable();
            $table->integer('jumlah_peserta')->nullable();
            $table->text('sumber_daya')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('pelaksanaans', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('pelaksanaans', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }
            if (!Schema::hasColumn('pelaksanaans', 'cakupan')) {
                $table->string('cakupan', 255)->nullable();
            }
            if (!Schema::hasColumn('pelaksanaans', 'jumlah_peserta')) {
                $table->integer('jumlah_peserta')->nullable();
            }
            if (!Schema::hasColumn('pelaksanaans', 'sumber_daya')) {
                $table->text('sumber_daya')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaksanaans');
    }
};

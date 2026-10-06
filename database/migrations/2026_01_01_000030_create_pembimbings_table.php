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
        $table = 'pembimbings';
        $oldNames = array (
  0 => 'pembimbing',
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
            $table->unsignedBigInteger('kegiatan_mahasiswa_id')->nullable();
            $table->string('nama_pembimbing', 255);
            $table->string('tipe', 50)->nullable()->default('Internal');
            $table->string('kontak', 255)->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('pembimbings', 'kegiatan_mahasiswa_id')) {
                $table->unsignedBigInteger('kegiatan_mahasiswa_id')->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'nama_pembimbing')) {
                $table->string('nama_pembimbing', 255)->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'tipe')) {
                $table->string('tipe', 50)->nullable()->default('Internal');
            }
            if (!Schema::hasColumn('pembimbings', 'kontak')) {
                $table->string('kontak', 255)->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'created_at')) {
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
        Schema::dropIfExists('pembimbings');
    }
};

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
        $table = 'alumni_mitras';
        $oldNames = array (
  0 => 'alumni_mitra',
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
            $table->unsignedBigInteger('alumni_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->string('posisi', 255)->nullable();
            $table->decimal('gaji', 15, 2)->nullable();
            $table->integer('masa_tunggu_bulan')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('alumni_mitras', 'alumni_id')) {
                $table->unsignedBigInteger('alumni_id')->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'posisi')) {
                $table->string('posisi', 255)->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'gaji')) {
                $table->decimal('gaji', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'masa_tunggu_bulan')) {
                $table->integer('masa_tunggu_bulan')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_mitras');
    }
};

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
        $table = 'pks_numbers';
        $oldNames = array (
  0 => 'pks_number',
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
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->string('number', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('nomor_pihak_kampus', 100)->nullable();
            $table->string('nomor_pihak_mitra', 100)->nullable();
            $table->string('pks_number', 255)->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('pks_numbers', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'number')) {
                $table->string('number', 255)->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'sort_order')) {
                $table->integer('sort_order')->nullable()->default(0);
            }
            if (!Schema::hasColumn('pks_numbers', 'nomor_pihak_kampus')) {
                $table->string('nomor_pihak_kampus', 100)->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'nomor_pihak_mitra')) {
                $table->string('nomor_pihak_mitra', 100)->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'pks_number')) {
                $table->string('pks_number', 255)->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'created_at')) {
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
        Schema::dropIfExists('pks_numbers');
    }
};

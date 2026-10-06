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
        $table = 'pejabats';
        $oldNames = array (
  0 => 'pejabat',
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
            $table->string('nama', 255);
            $table->string('jabatan', 255);
            $table->string('nip', 50)->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('pejabats', 'nama')) {
                $table->string('nama', 255)->nullable();
            }
            if (!Schema::hasColumn('pejabats', 'jabatan')) {
                $table->string('jabatan', 255)->nullable();
            }
            if (!Schema::hasColumn('pejabats', 'nip')) {
                $table->string('nip', 50)->nullable();
            }
            if (!Schema::hasColumn('pejabats', 'created_at')) {
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
        Schema::dropIfExists('pejabats');
    }
};

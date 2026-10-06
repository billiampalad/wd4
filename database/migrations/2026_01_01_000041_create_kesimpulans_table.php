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
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('evaluasi_id')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('kesimpulans', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('kesimpulans', 'evaluasi_id')) {
                $table->unsignedBigInteger('evaluasi_id')->nullable();
            }
            if (!Schema::hasColumn('kesimpulans', 'kesimpulan')) {
                $table->text('kesimpulan')->nullable();
            }
            if (!Schema::hasColumn('kesimpulans', 'rekomendasi')) {
                $table->text('rekomendasi')->nullable();
            }
            if (!Schema::hasColumn('kesimpulans', 'created_at')) {
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
        Schema::dropIfExists('kesimpulans');
    }
};

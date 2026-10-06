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
        $table = 'evaluasis';
        $oldNames = array (
  0 => 'evaluasi',
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
            $table->unsignedBigInteger('evaluator_id')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('nilai')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('evaluasis', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'evaluator_id')) {
                $table->unsignedBigInteger('evaluator_id')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'catatan')) {
                $table->text('catatan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'nilai')) {
                $table->integer('nilai')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasis');
    }
};

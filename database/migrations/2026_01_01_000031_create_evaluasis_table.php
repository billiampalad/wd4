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
            $table->string('tipe_evaluasi', 50)->nullable();
            $table->integer('score')->nullable();
            $table->string('realisasi_volume', 255)->nullable();
            $table->text('realisasi_output')->nullable();
            $table->text('realisasi_outcome')->nullable();
            $table->string('sesuai_rencana', 50)->nullable();
            $table->integer('kualitas')->nullable();
            $table->integer('keterlibatan')->nullable();
            $table->integer('efisiensi')->nullable();
            $table->integer('kepuasan')->nullable();
            $table->text('kendala')->nullable();
            $table->text('ringkasan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->string('status_validasi', 50)->nullable()->default('Draft');
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
            if (!Schema::hasColumn('evaluasis', 'tipe_evaluasi')) {
                $table->string('tipe_evaluasi', 50)->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'score')) {
                $table->integer('score')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'realisasi_volume')) {
                $table->string('realisasi_volume', 255)->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'realisasi_output')) {
                $table->text('realisasi_output')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'realisasi_outcome')) {
                $table->text('realisasi_outcome')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'sesuai_rencana')) {
                $table->string('sesuai_rencana', 50)->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'kualitas')) {
                $table->integer('kualitas')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'keterlibatan')) {
                $table->integer('keterlibatan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'efisiensi')) {
                $table->integer('efisiensi')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'kepuasan')) {
                $table->integer('kepuasan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'kendala')) {
                $table->text('kendala')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'ringkasan')) {
                $table->text('ringkasan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'rekomendasi')) {
                $table->text('rekomendasi')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'kesimpulan')) {
                $table->text('kesimpulan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'tindak_lanjut')) {
                $table->text('tindak_lanjut')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'status_validasi')) {
                $table->string('status_validasi', 50)->nullable();
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

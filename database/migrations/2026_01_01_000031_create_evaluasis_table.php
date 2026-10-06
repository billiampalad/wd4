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
            $table->string('tipe_evaluasi', 50)->nullable()->default('Internal');
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('realisasi_volume')->nullable();
            $table->text('realisasi_output')->nullable();
            $table->text('realisasi_outcome')->nullable();
            $table->tinyInteger('sesuai_rencana')->nullable();
            $table->tinyInteger('kualitas')->nullable();
            $table->tinyInteger('keterlibatan')->nullable();
            $table->tinyInteger('efisiensi')->nullable();
            $table->tinyInteger('kepuasan')->nullable();
            $table->text('kendala')->nullable();
            $table->text('ringkasan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->string('kesimpulan', 100)->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->string('status_validasi', 50)->nullable()->default('Draft');
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('evaluasis', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'evaluator_id')) {
                $table->unsignedBigInteger('evaluator_id')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'tipe_evaluasi')) {
                $table->string('tipe_evaluasi', 50)->nullable()->default('Internal');
            }
            if (!Schema::hasColumn('evaluasis', 'score')) {
                $table->decimal('score', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'realisasi_volume')) {
                $table->integer('realisasi_volume')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'realisasi_output')) {
                $table->text('realisasi_output')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'realisasi_outcome')) {
                $table->text('realisasi_outcome')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'sesuai_rencana')) {
                $table->tinyInteger('sesuai_rencana')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'kualitas')) {
                $table->tinyInteger('kualitas')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'keterlibatan')) {
                $table->tinyInteger('keterlibatan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'efisiensi')) {
                $table->tinyInteger('efisiensi')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'kepuasan')) {
                $table->tinyInteger('kepuasan')->nullable();
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
                $table->string('kesimpulan', 100)->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'tindak_lanjut')) {
                $table->text('tindak_lanjut')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'status_validasi')) {
                $table->string('status_validasi', 50)->nullable()->default('Draft');
            }
            if (!Schema::hasColumn('evaluasis', 'created_at')) {
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
        Schema::dropIfExists('evaluasis');
    }
};

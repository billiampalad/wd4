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
        if (!Schema::hasTable('detail_kegiatans')) {
            Schema::create('detail_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('jenis_kerjasama_id')->nullable();
            $table->unsignedBigInteger('sasaran_id')->nullable();
            $table->unsignedBigInteger('indikator_id')->nullable();
            $table->string('income', 255)->nullable();
            $table->string('volume_luaran', 255)->nullable();
            $table->string('satuan_luaran', 255)->nullable();
            $table->text('keterangan_luaran')->nullable();
            $table->text('output')->nullable();
            $table->text('outcome')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('detail_kegiatans', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_kegiatans', 'kegiatan_kerjasama_id')) {
                $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'jenis_kerjasama_id')) {
                $table->unsignedBigInteger('jenis_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'sasaran_id')) {
                $table->unsignedBigInteger('sasaran_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'indikator_id')) {
                $table->unsignedBigInteger('indikator_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'income')) {
                $table->string('income', 255)->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'volume_luaran')) {
                $table->string('volume_luaran', 255)->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'satuan_luaran')) {
                $table->string('satuan_luaran', 255)->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'keterangan_luaran')) {
                $table->text('keterangan_luaran')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'output')) {
                $table->text('output')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'outcome')) {
                $table->text('outcome')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kegiatans');
    }
};

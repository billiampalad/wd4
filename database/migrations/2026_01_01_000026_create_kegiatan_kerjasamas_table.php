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
        if (!Schema::hasTable('kegiatan_kerjasamas')) {
            Schema::create('kegiatan_kerjasamas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->string('nama_kegiatan', 255);
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->enum('status', ['Perencanaan', 'Berjalan', 'Selesai'])->default('Perencanaan');
            $table->timestamps();
            });
        } else {
            Schema::table('kegiatan_kerjasamas', function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'nama_kegiatan')) {
                $table->string('nama_kegiatan', 255);
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'periode_mulai')) {
                $table->date('periode_mulai')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'periode_selesai')) {
                $table->date('periode_selesai')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'status')) {
                $table->enum('status', ['Perencanaan', 'Berjalan', 'Selesai'])->default('Perencanaan');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_kerjasamas');
    }
};

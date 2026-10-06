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
        if (!Schema::hasTable('alumni_mitras')) {
            Schema::create('alumni_mitras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumni_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->string('posisi', 255)->nullable();
            $table->decimal('gaji', 15, 2)->nullable();
            $table->integer('masa_tunggu_bulan')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('alumni_mitras', function (Blueprint $table) {
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

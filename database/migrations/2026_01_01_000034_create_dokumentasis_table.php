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
        if (!Schema::hasTable('dokumentasis')) {
            Schema::create('dokumentasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->string('file_path', 255);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('dokumentasis', function (Blueprint $table) {
            if (!Schema::hasColumn('dokumentasis', 'kegiatan_kerjasama_id')) {
                $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('dokumentasis', 'file_path')) {
                $table->string('file_path', 255);
            }
            if (!Schema::hasColumn('dokumentasis', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumentasis');
    }
};

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
        if (!Schema::hasTable('kerjasama_jurusan')) {
            Schema::create('kerjasama_jurusan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('jurusan_id');
            $table->timestamps();
            });
        } else {
            Schema::table('kerjasama_jurusan', function (Blueprint $table) {
            if (!Schema::hasColumn('kerjasama_jurusan', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id');
            }
            if (!Schema::hasColumn('kerjasama_jurusan', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjasama_jurusan');
    }
};

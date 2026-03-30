<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permasalahan_solusis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kegiatan');
            $table->text('kendala');
            $table->text('solusi');
            $table->text('rekomendasi');
            $table->timestamps();

            $table->foreign('id_kegiatan')
                  ->references('id')
                  ->on('kegiatan_kerjasamas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permasalahan_solusis');
    }
};

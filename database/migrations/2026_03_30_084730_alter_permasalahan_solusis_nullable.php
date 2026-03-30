<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permasalahan_solusis', function (Blueprint $table) {
            $table->text('kendala')->nullable()->change();
            $table->text('solusi')->nullable()->change();
            $table->text('rekomendasi')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('permasalahan_solusis', function (Blueprint $table) {
            $table->text('kendala')->nullable(false)->change();
            $table->text('solusi')->nullable(false)->change();
            $table->text('rekomendasi')->nullable(false)->change();
        });
    }
};

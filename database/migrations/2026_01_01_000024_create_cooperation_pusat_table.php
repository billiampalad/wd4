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
        if (!Schema::hasTable('kerjasama_pusat')) {
            Schema::create('kerjasama_pusat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('pusat_id');
            $table->timestamps();
            });
        } else {
            Schema::table('kerjasama_pusat', function (Blueprint $table) {
            if (!Schema::hasColumn('kerjasama_pusat', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id');
            }
            if (!Schema::hasColumn('kerjasama_pusat', 'pusat_id')) {
                $table->unsignedBigInteger('pusat_id');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjasama_pusat');
    }
};

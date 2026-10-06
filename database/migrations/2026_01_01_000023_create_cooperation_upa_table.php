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
        if (!Schema::hasTable('kerjasama_upa')) {
            Schema::create('kerjasama_upa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('upa_id');
            $table->timestamps();
            });
        } else {
            Schema::table('kerjasama_upa', function (Blueprint $table) {
            if (!Schema::hasColumn('kerjasama_upa', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id');
            }
            if (!Schema::hasColumn('kerjasama_upa', 'upa_id')) {
                $table->unsignedBigInteger('upa_id');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjasama_upa');
    }
};

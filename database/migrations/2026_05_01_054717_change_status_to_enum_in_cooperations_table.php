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
        Schema::table('cooperations', function (Blueprint $table) {
            $table->enum('status', [
                'aktif', 
                'dalam perpanjangan', 
                'kadarluarsa', 
                'tidak aktif', 
                'draft', 
                'menunggu_validasi'
            ])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }
};

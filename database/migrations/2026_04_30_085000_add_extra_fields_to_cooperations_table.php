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
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['aktif', 'perpanjangan', 'kadaluarsa', 'tidak_aktif'])->default('aktif');
            $table->string('document_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'status', 'document_link']);
        });
    }
};

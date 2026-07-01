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
        Schema::table('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            $table->string('jenis')->nullable();
            $table->string('doc_number')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            $table->dropColumn(['jenis', 'doc_number', 'start_date', 'end_date']);
        });
    }
};

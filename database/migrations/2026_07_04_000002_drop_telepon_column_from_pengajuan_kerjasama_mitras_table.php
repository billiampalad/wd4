<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'telepon')) {
                $table->dropColumn('telepon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            $table->string('telepon', 30)->nullable();
        });
    }
};

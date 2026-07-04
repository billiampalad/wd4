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
            if (! Schema::hasColumn('pengajuan_kerjasama_mitras', 'file_surat')) {
                $table->string('file_surat')->nullable()->after('pesan_tambahan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'file_surat')) {
                $table->dropColumn('file_surat');
            }
        });
    }
};

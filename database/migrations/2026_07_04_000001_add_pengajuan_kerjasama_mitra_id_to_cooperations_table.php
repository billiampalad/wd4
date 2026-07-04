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
            if (! Schema::hasColumn('cooperations', 'pengajuan_kerjasama_mitra_id')) {
                $table->foreignId('pengajuan_kerjasama_mitra_id')
                    ->nullable()
                    ->after('perpanjangan_dari_id')
                    ->constrained('pengajuan_kerjasama_mitras')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            if (Schema::hasColumn('cooperations', 'pengajuan_kerjasama_mitra_id')) {
                $table->dropForeign(['pengajuan_kerjasama_mitra_id']);
                $table->dropColumn('pengajuan_kerjasama_mitra_id');
            }
        });
    }
};
